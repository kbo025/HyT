<?php
namespace Navicu\Core\Application\UseCases\Reservation\ProcessReservation;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Application\Services\InventoryService;
use Navicu\Core\Application\Services\LockedAvailabilityService;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Payment;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\DeniedReservation;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\Entity\ReservationPack;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure\GetResumenReservationStructureHandler;
use Navicu\Core\Domain\Model\ValueObject\PublicId;
use Navicu\Core\Domain\Model\Entity\Notification;
use Navicu\Core\Application\Services\RateExteriorCalculator;


/**
 * Caso de uso 'Procesar Reservacion'
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 03/09/2015
 */
class ProcessReservationHandler extends GetResumenReservationStructureHandler implements Handler
{

    /**
     *   instancia del repositoryFactory
     *
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * el comando que llama al caso de uso
     * @var Command command
     */
    protected $command;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * indica si la reserva es nueva o ya habia sido creada
     * @var boolean $isNew
     */
    protected $isNew;

    /**
     * Método Get del la interfaz del serivicio Email
     * @internal param EmailInterface $emailService
     * @return \Navicu\Core\Application\Contract\EmailInterface
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
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        try {
            $this->rf = $rf;
            $this->command = $command;
            $rpProperty = $this->rf->get('Property');
            $rpReservation = $this->rf->get('Reservation');
            $reservation = $rpReservation->findOneBy(['public_id' => $command->get('idReservation')]);

            //si la reserva no existe se interrumpe el proceso
            if (!$reservation)
                return new ResponseCommandBus(404,'not found',['message'=>'reservation_not_found']); 
                
            $clientReservation = $reservation->getClientId();
            $clientCommand = $this->command->get('user');

            //si el cliente que ejecutó el comando no esta logueado se interrumpe el proceso
            if(!$clientCommand && !$this->command->get('isAavv'))
                return new ResponseCommandBus(401,'unauthorized',['message'=>'unauthorized']);

            //si la reserva tiene cliente comparo que sea el dueño principal de la reserva, sino lo tiene le asigno el cliente actual 
            if ($clientReservation) {
                if ((!$this->command->get('isAavv')) && ($clientReservation->getId() != $clientCommand->getId()))
                    return new ResponseCommandBus(403,'forbidden',['message'=>'forbidden']);
            } else
                $reservation->setClientId($clientCommand);

            //obtengo los bloqueos de disponibilidad de la reserva
            $locks = $reservation->getLockeds();

            //cambio el estado de la reserva a pendiente por pago
            $reservation->setState(0);

            if ($this->command->get('decreaseAvailability')) {
                //si aun tengo disponibilidad
                $errors = $this->haveAvailability($reservation);
                if(!empty($errors))
                    return new ResponseCommandBus(404,'bad request',$errors);
            }

            //se procesan los pagos por medio del paymentgateway
            $success = false;
            $responsePayments = $this->getPayments($reservation,$success);
            $this->setStatusReservation($reservation);
            if ($success) {
                LockedAvailabilityService::unlockedAvailability($this->rf);
                if ($this->command->get('decreaseAvailability'))
                    InventoryService::decreaseInventoryReserves($reservation, $this->rf);
                $this->setNotification($reservation->getClientId(),$reservation->getStatus());
                if ($reservation->getStatus() == 0 or $reservation->getStatus() == 2)
                    $this->processDataEmail($reservation);
                $rpReservation->save($reservation);
                return new ResponseCommandBus(201, 'OK', $this->getReservationdata($reservation));
            }
            $this->processDeniedEmail($reservation);
            $rpReservation->save($reservation);
            return new ResponseCommandBus(400,'Bad Request',['errorStatus' => 3, 'message' => $responsePayments]);

        } catch (NotAvailableException $e) {

            return new ResponseCommandBus(
                400,
                'Bad Request',
                [
                    'errorStatus' => 1,
                    'attribute' => $e->getAttribute(),
                    'internal' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
        } catch (EntityValidationException $e) {

            return new ResponseCommandBus(
                400,
                'Bad Request',
                [
                    'errorStatus' => 1,
                    'attribute' => $e->getAttribute(),
                    'internal' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
        } catch (\Exception $e) {

            return new ResponseCommandBus(
                500,
                'Bad Request',
                [
                    'errorStatus' => 1,
                    'message' => 'Hubo un error en el proceso por favor intente mas tarde',
                    'internal' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }
    }

    /**
     * esta función recalcula la disponibilidad para la reserva
     *
     * @param $reservation
     * @throws EntityValidationException
     * @return array
     */
    private function haveAvailability($reservation)
    {
        //Retorna si los datos de la BD son iguales a los del formulario
        try {
            $error = [];
            new RateCalculator($this->rf);
            $advance = $reservation
                ->getClientId()
                ->getUser()
                ->getDisableAdvance();
            $advance = !empty($advance) ?
                0 :
                $this->command->get('paymentGateway')->getCutOff();

            $currency = $this->command->get('paymentGateway')->getCurrency();
            RateCalculator::calculateRateReservation (
                $this->rf,
                $reservation,
                $advance,
                (($reservation->getClientId()->getTypeIdentity() != 'j') && ($reservation->getClientId()->getTypeIdentity() != 'J') && ($currency == 'VEF'))  
            );

        } catch (NotAvailableException $e) {
            $error = [
                'error' => 'not_available',
                'message' => $e->getMessage(),
                'date' => $e->getDate()
            ];
        } catch (\Exception $e) {
            $error = [
                'error' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        };

        return $error;
    }

    /**
     * procesa el conjunto de pagos de una reserva
     *
     * @param $reservation
     * @param $success
     * @throws EntityValidationException
     * @return array
     */
    private function getPayments(Reservation $reservation, &$success)
    {
        $data = [];
        $payments = $this->completePaymentInfo($reservation,$this->command->get('payments'));
        $pgw = $this->command->get('paymentGateway');
        $payments = $pgw->processPayments($payments);
        $success = $pgw->isSuccess();
        $reservation->setPaymentType($pgw->getTypePayment());

        foreach ($payments as $payment) {
            $success = $payment['code'] == '201' && $payment['success'];
            $pago = new Payment();
            $rfBankType = $this->rf->get('BankType');
            $bank = isset($payment['bank']) ? $rfBankType->findById($payment['bank']) : null;
            $receiverBank = isset($payment['receivingBank']) ? $rfBankType->findById($payment['receivingBank']) : null;
            $pago = $pago->setCode($payment['code'])
                ->setDate(new \DateTime())
                ->setReference($payment['reference'])
                ->setAmount($payment['amount'])
                ->setReservation($reservation)
                ->setIpAddress($this->command->get('ip'))
                ->setHolder($payment['holder'])
                ->setHolderId($payment['holderId'])
                ->setState($payment['status'])
                ->setType($pgw->getTypePayment())
                ->setResponse($payment['response'])
                ->setBank($bank)
                ->setReceiverBank($receiverBank);

                if (isset($payment['currency'])) {
                    $pago
                        ->setAlphaCurrency($payment['currency'])
                        ->setDollarPrice($payment['dollarPrice'])
                        ->setNationalPrice($payment['nationalPrice']);
                }

                $reservation->addPayment($pago);
                $data[] = $payment;
        }
        return $data;
    }

    /**
     * esta funcion asigna un valor de status a la reserva dependiendo de las caracteristicas de la misma
     */
    private function setStatusReservation(Reservation $reservation)
    {
        $pgw = $this->command->get('paymentGateway');
        $status = $pgw->getStatusReservation($reservation);
        $reservation->setState($status);
    }

    /**
     * La siguiente función es para el manejo de la creación
     * de notificación para un cliente de tipo reserva.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     */
    private function setNotification($client, $status)
    {
        $notification = new Notification();

        $data = [
            "message" => $status == 2 ? "reservation.accepted" : "reservation.per-confirm",
            "reciver" => $client->getUser(),
            "type" => 0
        ];

        $notification->updateObject($data);
        $this->rf->get("Notification")->save($notification);
    }


    /**
     * se encarga de enviar el correo de reservas rechazadas
     * 
     */
    private function processDeniedEmail(Reservation $reservation)
    {
        /**
         * Este conjunto de datos se estan procesando para enviarlo a la vista
         * todavia no se encuentra lista esta funcionalidad
         */
        $property = $reservation->getPropertyId();
        $dataEmail['propertyName'] = $property->getName();
        $dataEmail['clientNames'] = $reservation->getClientId()->getFullName();
        $dataEmail['clientEmail'] = $reservation->getClientId()->getEmail();
        $dataEmail['clientPhone'] = $reservation->getClientId()->getPhone();
        if ($reservation->getPaymentType() == 2)
            $dataEmail['typePayment'] = true;

        $dataEmail['reservationDate']= $reservation->getReservationDate()->format('d-m-Y  H:i:s');
        $dataEmail['publicId']= $reservation->getPublicId();
        $dataEmail['checkInReservation'] = $reservation->getDateCheckIn()->format('d-m-Y');
        $dataEmail['checkOutReservation'] = $reservation->getDateCheckOut()->format('d-m-Y');
        $dataEmail['numberAdult'] = $reservation->getAdultNumber();
        $dataEmail['numberChild'] = $reservation->getChildNumber();

        $dataEmail['subTotal'] = $reservation->getTotalToPay(false); //tarifa del cliente sin iva
        $dataEmail['tax'] = $reservation->getTaxPay();
        $dataEmail['subTotalOwner'] = $reservation->getNetRate(false); //tarifa del hotelero sin iva
        $dataEmail['taxOwner'] = $reservation->getTaxNetRate();

        $dataEmail['totalRooms'] = 0;
        $dataEmail['packages'] = [];
        foreach ($reservation->getReservationPackages() as $currentPack) {
            $auxPack['roomName'] = $currentPack->getRoomName();
            $auxPack['namePack'] = CoreTranslator::getTranslator(
                $currentPack
                    ->getTypePack()
                    ->getCode()
            );
            $auxPack['pricePack'] = $currentPack->getPrice();
            $auxPack['pricePackOwner'] = $currentPack->getNetRate();
            $auxPack['numberPack'] = $currentPack->getNumberRooms();
            $auxPack['numberAdults'] = $currentPack->getNumberAdults();
            $auxPack['numberChildren'] = $currentPack->getNumberKids();
            $dataEmail['totalRooms'] = $dataEmail['totalRooms'] + $auxPack['numberPack'];
            $dataEmail['packages'][] =  $auxPack;
        }

        foreach ($reservation->getPayments() as $currentPayment) {
            $dataEmail['payments'][] = [
                'amount' => $currentPayment->getAmount(),
                'status' => $currentPayment->getStatus(),
                'holder' => $currentPayment->getHolder(),
                'holderId' => $currentPayment->getHolderId(),
                'type' => $currentPayment->getType(),
                'response' => $currentPayment->getResponse(),
            ];
        }

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

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
        $emailService->setViewParameters($dataEmail);

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:deniedReservation.html.twig');
        $emailService->setSubject('Reserva Rechazada - navicu.com');

        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }

    /**
     * Envio de solicitud al servidor de banesco para procesar el pago de parte del cliente
     *
     * @param Reservation $reservation
     * @param $payment
     * @throws EntityValidationException
     * @return Array
     */
    private function completePaymentInfo($reservation,$payments)
    {

        $response = [];
        $publicId = $reservation->getPublicId() instanceof PublicId ?
            $reservation->getPublicId()->toString() :
            $reservation->getPublicId();
        foreach ($payments as $payment)
        {
            $amount = $reservation->getTotalToPay();
            if (isset($payment['amount']))
                $amount = $payment['amount'];
            $amount = number_format(round($amount,2),2);
            $response[] = array_merge(
                $payment,
                [
                    'description' => "Pago de la reservación N° ".$publicId." por ".str_replace('.',',',(string)$amount)." a nombre ".($reservation->getClientId()->getGender()==0 ? 'del Sr. ' : 'de la Sra. ' ).$reservation->getClientId()->getFullName(),
                    'ip' => $this->command->get('ip'),
                    'checkInDate' => $reservation->getDateCheckIn(),
                    'amount' => $amount,
                    'date' => \date('d-m-Y'),
                ]
            );
        }
        return $response;
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

        $dataEmail['userToken'] = $reservation->getHashUrl();
        $dataEmail['username'] = $reservation->getClientId()->getUser()->getUsername();

        if ($reservation->getPaymentType() == 2)
            $dataEmail['typePayment'] = true;

        $dataEmail['propertyAddress'] = $property->getAddress();
        if ($reservation->getPropertyId()->getLocation()->getCityId())
            $cityOrState = $property->getLocation()->getCityId()->getTitle();
        else
            $cityOrState = $property->getLocation()->getParent()->getParent()->getTitle();
        $dataEmail['city'] = $cityOrState;
        $dataEmail['propertyStars'] = $property->getStar();
        $dataEmail['propertyPhone'] = $property->getPhones();
        $dataEmail['checkInReservation'] = $reservation->getDateCheckIn()->format('d-m-Y');
        $dataEmail['checkOutReservation'] = $reservation->getDateCheckOut()->format('d-m-Y');
        $dataEmail['numberAdult'] = $reservation->getAdultNumber();
        $dataEmail['numberChild'] = $reservation->getChildNumber();
        $dataEmail['specialRequests'] = $reservation->getSpecialRequest();

        $dataEmail['subTotal'] = $reservation->getTotalToPay(false); //tarifa del cliente sin iva
        $dataEmail['tax'] = $reservation->getTaxPay();
        $dataEmail['subTotalOwner'] = $reservation->getNetRate(false); //tarifa del hotelero sin iva
        $dataEmail['taxOwner'] = $reservation->getTaxNetRate();

        $coordinates = $property->getCoordinates();
        $dataEmail['propertyLatitude'] = $coordinates['latitude'];
        $dataEmail['propertyLongitude'] = $coordinates['longitude'];
        $dataEmail['propertyGps'] = CoordinatesGps::getGps($coordinates['latitude'], $coordinates['longitude']);

        //Condiciones adicionales del establecimiento
        $dataEmail['propertyAdditionalInfo'] = [];

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
            $maxCash = (!empty($maxCash) ? ' hasta ' . number_format($maxCash, 2, ',', '.'). ' Bs' : '');
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

            if (!empty($auxPack['bedsType'])) {
                $auxBeds = '';
                if (isset($auxPack['bedsType']['beds'])) {
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
            $auxPack['roomImage'] = str_replace(' ', '%20',
                $currentPack
                    ->getPackId()
                    ->getRoom()
                    ->getProfileImage()
                    ->getImage()
                    ->getWebPath());

            $auxPack['namePack'] = CoreTranslator::getTranslator(
                $currentPack
                    ->getTypePack()
                    ->getCode()
            );

            $auxPack['pricePack'] = $currentPack->getPrice();
            $auxPack['pricePackOwner'] = $currentPack->getNetRate();

            $auxPack['numberPack'] = $currentPack->getNumberRooms();
            $auxPack['numberAdults'] = $currentPack->getNumberAdults();
            $auxPack['numberChildren'] = $currentPack->getNumberKids();
	        if ($auxPack['numberChildren'] > 0) {
		        $auxPack['childrenAges'] = array();
	        	$childrenAges = $currentPack->getChildrenAge();
		        foreach ($childrenAges as $currentAge) {
		        	$auxPack['childrenAges'][] = $currentAge->getAge();
		        }
	        }
            $dataEmail['totalRooms'] = $dataEmail['totalRooms'] + $auxPack['numberPack'];

            array_push($dataEmail['packages'], $auxPack);
        }

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients([$dataEmail['clientEmail']]);
        $emailService->setViewParameters($dataEmail);

        if ($reservation->getStatus() == 0) {
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:preReservationConfirmation.html.twig');
            $emailService->setSubject('Reserva Pendiente de Pago - navicu.com');
        }
        else if ($reservation->getStatus() == 2) {
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationConfirmation.html.twig');
            $emailService->setSubject('Confirmación de la Reserva - navicu.com');
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
}
