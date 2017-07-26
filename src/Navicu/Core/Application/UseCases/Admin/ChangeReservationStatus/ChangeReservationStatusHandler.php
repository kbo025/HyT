<?php
namespace Navicu\Core\Application\UseCases\Admin\ChangeReservationStatus;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\InventoryService;
use Navicu\Core\Domain\Model\Entity\ReservationChangeHistory;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\Notification;
use Navicu\Core\Domain\Model\Entity\ReservationPack;
use Navicu\Core\Domain\Model\Entity\AAVVInvoice;

/**
 * El siguiente handler es usado para cambiar el estado de una reserva.
 * 
 * Class ChangeReservationStatusHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ChangeReservationStatusHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * Método Set del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function setEmailService(EmailInterface $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * 
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->rf = $rf;
        $request = $command->getRequest();
        $code = 400;
        $response = ["status"=>false];
        $rpReservation = $rf->get('Reservation');
        $rpPayment = $rf->get('Payment');
        
        $reservation = $rpReservation->findOneByArray(["public_id"=>$request["idReservation"]]);
        if (!$reservation)
            return new ResponseCommandBus(404, 'Not Found', null);

	    if ($reservation->getAavvReservationGroup() == null) {
		    $reservation->roundReservation();
	    }
        switch ($reservation->getState())
        {
            case 0: // 0: pre-reserva
                break;
            
            case 1: // 1: por confirmación
                //Paso de la reserva a Aceptada.
                if ($request["reservationStatus"] == 2) {
                    if (!$request["arrayTransferred"])
                        break;

                    $payments = $reservation->getPayments();
                    $totalReservation = $reservation->isForeignCurrency() ?
                        $reservation->getCurrencyPrice() :
                        $reservation->getTotalToPay();
                    $amount = 0;
                    $values = [];
                    foreach ($request["arrayTransferred"] as $transf) {
                        $amount = $amount + $this->setPaymentInReservation($reservation,$transf);
                        $values[] = [
                            'paymentId' => $transf['paymentId'],
                            'amountTransferred' => $transf['amountTransferred']
                        ];
                    }
                    //Se verifica si el pago esta completo, en el caso de ser una reserva de AAVV se considera el descuento
                    $paymentComplete = false;
	                if($reservation->getAavvReservationGroup() == null){
	                	if ($amount >= $totalReservation) {
	                		$paymentComplete = true;
		                }
	                } else {
	                	$aavv = $reservation->getAavvReservationGroup()->getAavv();
		                if ($amount >= round($totalReservation * (1-$aavv->getAgreement()->getDiscountRate()),2)) {
			                $paymentComplete = true;
		                }
	                }
                    if ($paymentComplete) {
                        $changeHistory = new ReservationChangeHistory;
                        $changeHistory
                            ->setDate(new \DateTime())
                            ->setDataLog($values)
                            ->setStatus($request["reservationStatus"])
                            ->setReservation($reservation);   

                        if ($reservation->getCurrentState())
                            $changeHistory->setLastStatus($reservation->getCurrentState());

                        $reservation
                            ->setCurrentState($changeHistory)
                            ->addChangeHistory($changeHistory)
                            ->setState($request["reservationStatus"]);

                        $this->processDataEmail($reservation);
                        $response = ["status" => true];

                        $this->setNotification($reservation->getClientId(), $reservation->getStatus());
                        $code = 200;
                    }
                    $rpReservation->save($reservation);
                    $this->generateInvoiceToAavv($reservation);
                }

                //Paso de la reserva a Cancelada.
                if ($request["reservationStatus"] == 3) {
                    $change = new InventoryService;
                    if ($change->increasedInventoryReserves($reservation)) {
                        $reservation->setState($request["reservationStatus"]);

                        $changeHistory = new ReservationChangeHistory;
                        $changeHistory->setDate(new \DateTime());
                        $changeHistory->setStatus($request["reservationStatus"]);
                        $changeHistory->setDataLog(["description" => $request["description"]]);

                        if ($reservation->getCurrentState()) {
                            $changeHistory->setLastStatus($reservation->getCurrentState());
                        }
                        
                        $reservation->setCurrentState($changeHistory);
                        $changeHistory->setReservation($reservation);
                        $reservation->setCurrentState($changeHistory);
                        $reservation->addChangeHistory($changeHistory);

                        //$rpReservation->save($reservation);
                        $this->cancellationReservation($reservation);
                        $response = ["status"=>true];

                        $this->setNotification($reservation->getClientId(), $reservation->getStatus());
                        $code = 200;
                    }
                }
                break;
            
            case 2: // 2: confirmadas
                break;
            
            case 3: // 3: canceladas
                break;
        }
        return new ResponseCommandBus($code, '', $response);

    }
    
    /**
      * La siguiente función se encarga de enviar el correo al usuario al momento de
      * procesar la reserva
      *
      * @author Freddy Contreras <freddycontreras3@gmail.com>
      * @version 02/11/2015
      * @param Reservation $reservation
      */
    private function processDataEmail(Reservation $reservation)
    {
        /**
         * Este conjunto de datos se estan procesando para enviarlo a la vista
         * todavia no se encuentra lista esta funcionalidad
         */
        $property = $reservation->getPropertyId();
        $publicId = $reservation->getPublicId();
        $dataEmail['propertyName'] = $property->getName();
        $dataEmail['propertyImage'] = str_replace(' ', '%20', $property->getProfileImage()->getImage()->getFileName());
        $dataEmail['clientNames'] = $reservation->getClientId()->getFullName();
        $dataEmail['clientGender'] = $reservation->getClientId()->getGender();
        $dataEmail['clientEmail'] = $reservation->getClientId()->getEmail();
        $dataEmail['sendOwner'] = false;
        $dataEmail['publicReservationId'] = empty($publicId) ?
            null :
            $publicId instanceof PublicId ?
                $publicId->toString() :
                $publicId;

        if ($reservation->getPaymentType() == 2)
            $dataEmail['typePayment'] = true;

        $dataEmail['propertyAddress'] = $property->getAddress();
        if ($reservation->getPropertyId()->getLocation()->getCityId())
            $cityOrState = $property->getLocation()->getCityId()->getTitle();
        else
            $cityOrState = $property->getLocation()->getParent()->getParent()->getTitle();
        $dataEmail['city'] = $cityOrState;
        $dataEmail['propertyStars'] = $property->getStar();
        $dataEmail['propertyPhone'] = $property->getPhones();/*******/
        $dataEmail['checkInReservation'] = $reservation->getDateCheckIn()->format('d-m-Y');
        $dataEmail['checkOutReservation'] = $reservation->getDateCheckOut()->format('d-m-Y');
        $dataEmail['numberAdult'] = $reservation->getAdultNumber();
        $dataEmail['numberChild'] = $reservation->getChildNumber();
        $dataEmail['specialRequests'] = $reservation->getSpecialRequest();

        $dataEmail['subTotal'] = $reservation->getTotalToPay(false);
        $dataEmail['tax'] = $reservation->getTaxPay();
        $discountRate = $reservation->getTotalToPay() * $property->getDiscountRate();
        $auxTotalOwner = $reservation->getTotalToPay() * (1 - $property->getDiscountRate());
        $dataEmail['subTotalOwner'] = $reservation->getNetRate(false);
        $dataEmail['taxOwner'] = $reservation->getTaxNetRate();

        $coordinates = $property->getCoordinates();
        $dataEmail['propertyLatitude'] = $coordinates['latitude'];
        $dataEmail['propertyLongitude'] = $coordinates['longitude'];
        $dataEmail['propertyGps'] = CoordinatesGps::getGps($coordinates['latitude'], $coordinates['longitude']);

        //Condiciones adicionales del establecimiento
        $dataEmail['propertyAdditionalInfo'] = [];

        if ($property->getAllIncluded())
            $dataEmail['propertyAdditionalInfo'][] = 'Todo Incluido';

        if ($property->getChild())
            $dataEmail['propertyAdditionalInfo'][] = 'Acepta el hospedaje de niños';

        if ($property->getPets())
            $dataEmail['propertyAdditionalInfo'][] = 'Se permiten mascotas';

        if ($property->getBeds())
            $dataEmail['propertyAdditionalInfo'][] = 'Puede solicitar camas adicionales';

        if ($property->getCribs())
            $dataEmail['propertyAdditionalInfo'][] = 'Puede solicitar cunas';

        // Formas de pago en el establecimiento
        $dataEmail['methodToPay'] = [];

        if ($property->getDebit())
            $dataEmail['methodToPay'][] = 'Acepta tarjeta de débito como forma de pago';

        if ($property->getCreditCard())
            $dataEmail['methodToPay'][] = 'Acepta tarjeta de crédito como forma de pago';

        if ($property->getCash()) {
            $cash = 'Acepta pagos en efectivo';
            $maxCash = $property->getMaxCash();
            $maxCash = (!empty($maxCash) ? ' hasta ' . $maxCash . ' Bs' : '');
            $cash = $cash . $maxCash;
            $dataEmail['methodToPay'][] = $cash;
        }

        $cash = $reservation->getPropertyId()->getCash();
        $dataEmail['creditCardAmex'] = $property->getCreditCardAmex();
        $dataEmail['creditCardMc'] = $property->getCreditCardMc();
        $dataEmail['creditCardVisa'] = $property->getCreditCardVisa();

        $dataEmail['creditCard'] = ($cash ||
            $dataEmail['creditCardAmex'] ||
            $dataEmail['creditCardMc'] ||
            $dataEmail['creditCardVisa']);

        //Datos de las habitaciones y servicio a reservar
        $dataEmail['totalRooms'] = 0;
        $dataEmail['packages'] = [];

        foreach ($reservation->getReservationPackages() as $currentPack) {
            $auxPack['roomName'] = $currentPack->getRoomName();

            $auxPack['bedsType'] = '';
            $auxPack['bedsType'] = $currentPack->getBedroom();

            if (isset($auxPack['bedsType'])) {
                $auxBeds = '';
                if(isset($auxPack['bedsType']['beds'])) {
                    foreach ($auxPack['bedsType']['beds'] as $bed) {
                        if ($bed['amount'] > 1)
                            $auxBeds .= ' '.$bed['amount'].' ';
                        $auxBeds .= ' '.$bed['typeString'].' +';
                    }
                }

                $auxBeds = trim($auxBeds,'+');
                $auxPack['bedsType'] = $auxBeds;
            }

            $auxPack['roomImage'] = '';
            //Images01
            /*$auxPack['roomImage'] = str_replace(' ', '%20',
                $currentPack
                    ->getPackId()
                    ->getRoom()
                    ->getProfileImage()
                    ->getImage()
                    ->getWebPath());*/

            $auxPack['namePack'] = CoreTranslator::getTranslator(
                $currentPack
                    ->getTypePack()
                    ->getCode()
            );

            $auxPack['pricePack'] = $currentPack->getPrice();
            $discountRate = $auxPack['pricePack'] * $property->getDiscountRate();
            $auxPack['pricePackOwner'] = $auxPack['pricePack'] - $discountRate;
            $auxPack['numberPack'] = $currentPack->getNumberRooms();
            $auxPack['numberAdults'] = $currentPack->getNumberAdults();
            $auxPack['numberChildren'] = $currentPack->getNumberKids();
            $dataEmail['totalRooms'] = $dataEmail['totalRooms'] + $auxPack['numberPack'];

            array_push($dataEmail['packages'], $auxPack);
        }

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients([$dataEmail['clientEmail']]);
        $emailService->setViewParameters($dataEmail);

        if ($reservation->getStatus() == 0) {
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:preReservationConfirmation.html.twig');
            $emailService->setSubject('Reserva Pendiente de Pago - Navicu');
        }
        else if ($reservation->getStatus() == 2) {
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationConfirmation.html.twig');
            $emailService->setSubject('Confirmación de la Reserva - Navicu');
        }


        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();


        $emailService->setRecipients([
            'jacastro@navicu.com',
            'mcontreras@navicu.com',
            'yzuzolo@navicu.com',
            'yvasquez@navicu.com',
            'ocoronel@navicu.com',
            'dmora@navicu.com',
            'eblanco@navicu.com',
            'preguntas@navicu.com',
            'dcruz@navicu.com'
        ]);

        $emailService->sendEmail();

        if ($reservation->getStatus() == 2) {

            //Enviando correo al comercial responsable
            if ($property->getNvcProfile()) {
                $commercialProfile = $property->getNvcProfile();
                if ($commercialProfile->getUser()) {
                    $emailService->setRecipients([
                        $commercialProfile->getUser()->getEmail()
                    ]);

                    $emailService->sendEmail();
                }
            }

            $dataEmail['ownerEmail'] = [];
            foreach($property->getContacts() as $contact)
            {
                if($contact->getEmailReservationReceiver())
                    $dataEmail['ownerEmail'][] = $contact->getEmail();
            }

            $emailService->setRecipients($dataEmail['ownerEmail']);
            $dataEmail['sendOwner'] = true;
            $emailService->setViewParameters($dataEmail);
            $emailService->sendEmail();
        }
    }
    
    /**
     * La siguiente procesa las cancelaciones de la reserva
     *
     * @param Reservation $reservation
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/11/2015
     *
     */
    private function cancellationReservation(Reservation $reservation)
    {
        $emailData = $this->getDataEmail($reservation);
        $emailData['packages'] = array();

        $dateNow = new \DateTime();
        $emailData['idCancelation'] = $reservation->getPublicId();
        $emailData['dateCancelation'] = $dateNow->format('Y-m-d');

        //Se devuelven las disponibilidades de los servicios
        foreach ($reservation->getReservationPackages() as $currentPack) {
            $auxPack = array();
            $auxPack['nameRoom'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getRoom()->getType()->getTitle());
            $auxPack['namePack'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getType()->getCode());
            $auxPack['roomsNumber'] = $currentPack->getNumberRooms();
            $auxPack['childNumber'] = $currentPack->getNumberKids();
            $auxPack['adultNumber'] = $currentPack->getNumberAdults();
            $auxPack['bedsType'] = '';

            $bedRoom = $currentPack->getBedRoomId();

            if ($bedRoom) {
                foreach ($bedRoom['beds'] as $currentBed) {
                    $auxPack['bedsType'] = $auxPack['bedsType'] .
                        $currentBed['typeString'] .
                        ($currentBed['amount'] == 1 ? '' : '(' . $currentBed['amount'] . ') + ');
                }
            }

            array_push($emailData['packages'], $auxPack);
        }


        $emailService = $this->getEmailService($reservation);
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients(array($emailData['emailClient']));
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationCanceled.html.twig');
        $emailService->setSubject('Cancelación de la Reserva - Navicu');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
    
    /**
     * La siguiente funcion se encarga de obtener los datos basicos
     * para la actualizacion de una reserva, sea al editar o cancelar la reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Reservation $reservation
     * @return array
     */
    private function getDataEmail(Reservation $reservation)
    {
        $emailData = array();
        $emailData['nameProperty'] = $reservation->getPropertyId()->getName();
        $emailData['propertyImage'] = str_replace(' ', '%20',$reservation->getPropertyId()->getProfileImage()->getImage()->getFileName());
        $emailData['stars'] = $reservation->getPropertyId()->getStar();
        $emailData['address'] = $reservation->getPropertyId()->getAddress();
        $emailData['idReservation'] = $reservation->getPublicId();
        $emailData['dateReservation'] = $reservation->getReservationDate()->format('Y-m-d');
        $emailData['checkIn'] = $reservation->getDateCheckIn()->format('Y-m-d');
        $emailData['checkOut'] = $reservation->getDateCheckOut()->format('Y-m-d');
        $emailData['numberAdults'] = $reservation->getAdultNumber();
        $emailData['numberChildren'] = $reservation->getChildNumber();
        $emailData['nameClient'] = $reservation->getClientId()->getFullName();
        $emailData['identityCardClient'] = $reservation->getClientId()->getIdentityCard();
        $emailData['emailClient'] = ($reservation->getClientId()->getEmail() instanceof EmailAddress) ?
            $reservation->getClientId()->getEmail()->toString() :
            $reservation->getClientId()->getEmail();
        $emailData['phoneClient'] = $reservation->getClientId()->getPhone();
        $emailData['genderClient'] = $reservation->getClientId()->getGender();
        $emailData['totalToPay'] =
            round($reservation->getTotalToPay() + $reservation->getTaxPay(),2);

        return $emailData;
    }

    /**
     * La siguiente función es para el manejo de la creación
     * de notificación para un cliente de tipo reserva.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     */
    private function setNotification($client, $status)
    {
        $notification = new Notification();
        $data = [
            "message" => $status == 2 ? "reservation.accepted" : "reservation.canceled",
            "reciver" => $client->getUser(),
            "type" => 0
        ];
        $notification->updateObject($data);
        $this->rf->get("Notification")->save($notification);
    }

    private function setPaymentInReservation($reservation,$transf)
    {
        $total = 0;
 
        foreach ($reservation->getPayments() as $payment) {
            if (($payment->getId() == $transf['paymentId']) && !empty($transf['amountTransferred'])) {
                if ($payment->getAmount() >= $transf['amountTransferred']) {
                    $payment->setAmountTransferred($transf['amountTransferred']);
                    $payment->setState(1);
                    $total = $payment->getAmountTransferred();
                }
            }
        }

        return $total;
    }

    private function generateInvoiceToAavv($reservation)
    {
        $rg = $reservation->getAavvReservationGroup();

        if (($reservation->getState() == 2) && ($reservation->isAavvAndTransferredPay()) && (empty($rg->getAavvInvoice()))) {

            $userrep = $this->rf->get('User');
            $adminId = $userrep->findByUserNameOrEmail('supradmin')->getId();

            $currentdate = new \DateTime();
            $createDate = new \DateTime();
            $moddate = $currentdate->modify('first day of next month');
            $due_date = $moddate->add(new \DateInterval('P4D'));
            $creationdate = $currentdate->format('Y-m-d');

            $currencyrep = $this->rf->get('CurrencyType');
            $currency = $currencyrep->find(148);

            $sequence_rep = $this->rf->get('NVCSequence');
            $sequence = $sequence_rep->findOneByArray(['name' => 'aavv_invoice']);
            $number = $sequence->getCurrentnext();
            if($sequence->getPrefix())
                $number = $sequence->getPrefix() . $number;
            $sequence->setCurrentnext($number + 1);
            $sequence_rep->save($sequence);

            $rgRep = $this->rf->get('AAVVReservationGroup');

            $repInvoice = $this->rf->get('AAVVInvoice');
            $invoice = new AAVVInvoice();
            $invoice
                ->setAavv($rg->getAavv())
                ->setTotalAmount($reservation->getTotalToPay() - $reservation->getTotalForAavv())
                ->setCurrencyType($currency)
                ->setType('ar_invoice')
                ->setDate($createDate)
                ->setDueDate($due_date)
                ->setNumber($number)
                ->setDescription('Factura ' . $creationdate)
                ->setStatus(1)
                ->setTax(1)
                ->setTaxRate(0.12)
                ->setCreatedAt($createDate)
                ->setCreatedBy($adminId)
                ->setUpdatedAt($createDate)
                ->setUpdatedBy($adminId);

            $rg->setAavvInvoice($invoice);

            //$repInvoice->save($invoice);
            $rgRep->save($rg);
        }
    }
}