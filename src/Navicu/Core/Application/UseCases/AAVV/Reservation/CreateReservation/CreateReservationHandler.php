<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\CreateReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\AAVVAccountingService;
use Navicu\Core\Application\Services\CommandBus;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Application\Services\InventoryService;
use Navicu\Core\Application\Services\LockedAvailabilityService;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure\GetResumenReservationStructureHandler;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Adapter\NotAvailableException;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVReservationGroup;
use Navicu\Core\Domain\Model\Entity\ChildrenAge;
use Navicu\Core\Domain\Model\Entity\DeniedReservation;
use Navicu\Core\Domain\Model\Entity\Payment;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationPack;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\PublicId;
use Navicu\Core\Application\UseCases\Reservation\setClientProfile\setClientProfileCommand;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateReservationHandler extends GetResumenReservationStructureHandler implements Handler
{

    protected $command;
    protected $rf;
    protected $isNew;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * @return EmailInterface
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
     * Ejecuta el caso de uso para realizar una reserva y enviar el correo electronico tener
     * saldo positivo la agencia y disponibilidad de habitaciones
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        try {
            $response = [];
            $this->command = $command;
            $this->rf = $rf;
            $rpReservationGroup = $this->rf->get('AAVVReservationGroup');
            $aavvRf = $this->rf->get('AAVV');
            $reservationGroup = $rpReservationGroup->findOneByArray(['public_id' => $this->command->get('groupId')]);

            if (is_null($reservationGroup))
                return new ResponseCommandBus(404,'reservation_group_not_found');

            $aavv = $reservationGroup->getAavv();

            $rpClientProfiel = $this->rf->get('ClientProfile');
            $creditAvailable = $reservationGroup->getAavv()->getCreditAvailable();

            foreach ($reservationGroup->getReservation() as $currentReservation) {
                $this->assignClient($currentReservation);
                $this->checkAvailavility($currentReservation);
            }

            $success = true;
            $total = $totalAAVV = 0;
            $response['payments'] = [];
            foreach ($reservationGroup->getReservation() as $currentReservation) {
                $total = $total + $currentReservation->getTotaltoPay();
                $totalAAVV += abs($currentReservation->getTotaltoPay() - $currentReservation->getTotalForAavv());
                // Si no es una pre-reserva
                $pgw = $this->command->get('paymentGateway');
                $currentReservation->setPaymentType($pgw->getTypePayment());
                if ($this->command->get('reservation_type') == 2)
                    $response['payments'][] = $this->getPayments($currentReservation, $success, $total);
                $this->setStatusReservation($currentReservation);
            }

            $rpReservationGroup->save($reservationGroup);

            // Obtenemos el state del grupo de la reserva
            $reservationStatus = $this->getStatusReservation($reservationGroup);

            // Decrementamos las cantidades de Dr y Dp realizados en la reserva.
            // Desbloqueamos a su vez las reservas realizadas
            $this->unlockAndDecrementReservations($reservationGroup->getReservation());

            // Generamos una nota de credito a favor de navicu
            $this->generateMovementForAavv($aavv, $reservationGroup->getReservation());

            //Descontamos del credito disponible
            AAVVAccountingService::balanceCalculator($aavv);

            $sendClientEmail = $this->command->get('send_email');
            //Enviamos los correos al cliente si la aavv tiene el servicio disponible
            $this->sendEmailToClient(
                $reservationGroup->getReservation(),
                $this->command->get('domain'),
                $sendClientEmail,
                $reservationStatus
                );

            // Indicar que se esta "desactivando" la agencia por falta de credito
//            if ($aavv->getCreditAvailable() < 0) {
//                $aavv->setDeactivateReason(3);
//
//                // Se le envia un correo a la agencia de viajes con la notificacion de "desactivacion"
//                $this->sendDeactivateEmailToAAVV($aavv);
//                $aavvRf->save($aavv);
//            }

            // Retornamos la informacion con el conjunto de reservas realizadas exitosamente
            $publicIdReservation = $reservationGroup->getPublicId() instanceof PublicId ?
                $reservationGroup->getPublicId()->toString() :
                $reservationGroup->getPublicId();
            $responseData['publicIdReservation'] = $publicIdReservation;
            $responseData['location'] = $this->command->get('location');
            $responseData['locationSlug'] = $this->command->get('locationSlug');
            // Variable enviada a frontEnd para cambiar el tipo de reserva visualmente
            $responseData['reservation_type'] = $reservationStatus;

            // Enviamos el correo de la confirmacion de la reserva al personal navicu y a la aavv
            $this->sendEmailAAVV(
                $reservationGroup,
                $totalAAVV,
                $responseData['location'],
                $this->command->get('domain'),
                $reservationStatus
            );

            return new ResponseCommandBus(200, 'OK', $responseData);

        } catch (NotAvailableException $e) {

            $msj['date'] = $e->getDate();
            $msj['message'] = $e->getMessage();
            $error['error'] = $msj;

            return new ResponseCommandBus(400, 'Something went wrong', $error );

        } catch (\Exception $e) {

            $msj['file'] = $e->getFile();
            $msj['line'] = $e->getLine();
            $msj['message'] = $e->getMessage();
            $error['error'] = $msj;

            return new ResponseCommandBus(500, 'Something went wrong', $error );
        };
    }

    /**
     * esta función recalcula la disponibilidad para la reserva
     *
     * @param $reservation
     * @throws EntityValidationException
     * @return array
     */
    private function checkAvailavility($reservation) 
    {
        //Retorna si los datos de la BD son iguales a los del formulario
        $reservation->setState(0);
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
    }

    /**
     * Funcion encargada de devolver el cliente que esta realizando la reserva, bien sea porque existe o porque
     * tiene que crearlo de 0
     *
     * @param $guestData
     * @param $rf
     * @version 2016/09/26
     * @author Isabel Nieto
     * @return ResponseCommandBus|void
     */
    private function assignClient($reservation)
    {
        $clientData = $this->getClientData($reservation->getPublicId());
        $clientProfile = $this->rf
            ->get('ClientProfile')
            ->findOneByArray(['email' => $clientData['guestEmail']]);

        if (is_null($clientProfile)) {
            global $kernel;
            $email = new EmailAddress($clientData["guestEmail"]);
            $phone = new Phone($clientData["guestPhone"]);
            $data = [
                'email' => $clientData['guestEmail'],
                'phone' => $clientData['guestPhone'],
                'fullName' => $clientData['guestFullName'],
                'identityCard' => $clientData['guestDocumentId'],
                'gender' => null,
                'redSocial' => null,
                'pass1' => "a123456",
                'pass2' => "a123456"  
            ];
            $command = new setClientProfileCommand($data, true);
            $response = $kernel->getContainer()->get('CommandBus')->execute($command);

            // Si el correo del usuario no se creo correctamente
            if (!$response->isOk());//lanzar una excepcion
            $clientProfile = $this->rf
                ->get('ClientProfile')
                ->findOneByArray(['email' => $clientData['guestEmail']]);
        }
        $reservation->setClientId($clientProfile);
    }

    private function getClientData($rservationId)
    {
        $properties = $this->command->get('properties');
        $response = false;
        $i = 0;
        while (!$response && $i < count($properties)) {
            $j=0;
            while (!$response && $j < count($properties[$i]['reservations'])) {
                if ($properties[$i]['reservations'][$j]['public_id'] == $rservationId)
                    $response = $properties[$i]['reservations'][$j];
                $j++;
            }
            $i++;
        }
        return $response;
    }

    /**
     * Funcion encargada de realizar el pago de la reserva y descontarlo del total de la agencia de viajes
     *
     * @param Reservation $newReservation , objeto de tipo reservation que esta siendo creado
     * @param $success bool, parametro que indica si fue exitoso el proceso o no
     * @param $totalAmount integer sumatoria de todas las reservas hasta el momento
     * @return array con la informacion del payment
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 19/09/2016
     */
    private function getPayments(Reservation $newReservation, &$success, $totalAmount)
    {
        $data = [];

        // Armamos parte de la informacion a ser validada y mostrada en la factura
        $payment = $this->completePaymentInfo($newReservation, $totalAmount);
        $pgw = $this->command->get('paymentGateway');
        $payment = $pgw->processPayments($payment);

        $success = $pgw->isSuccess();
        $success = $payment['code'] == '201' && $payment['success'];
        $pay = new Payment();

        // Transformamos el public Id
        $publicId = $newReservation->getAavvReservationGroup()->getPublicId();
        $publicId = $publicId instanceof PublicId ?
            $publicId->toString() :
            $publicId;

        // Creamos el pago
        $pay = $pay->setCode($payment['code'])
            ->setDate(new \DateTime())
            ->setReference($publicId)
            ->setAmount($payment['amount'])
            ->setReservation($newReservation)
            ->setState($payment['status'])
            ->setType($pgw->getTypePayment())
        ;
        if (isset($payment['currency'])) {
            $pay
                ->setAlphaCurrency($payment['currency'])
                ->setDollarPrice($payment['dollarPrice'])
                ->setNationalPrice($payment['nationalPrice']);
        }
        $newReservation->addPayment($pay);
        $data[] = $payment;

        return $data;
    }

    /**
     * Se arma la respuesta/estructura en base a la reserva que se esta realizando
     *
     * @param $newReservation
     * @param $totalAmount integer monto total del conjunto de reservas (utilizado solo para aavv)
     * @return array
     */
    private function completePaymentInfo($newReservation, $totalAmount)
    {
        $publicId = ($newReservation->getPublicId() instanceof PublicId) ?
            $newReservation->getPublicId()->toString() :
            $newReservation->getPublicId();

        $amount = $newReservation->getTotalToPay();

        $amount = number_format(round($amount,2),2);
        $response = [
                'description' => "Pago de la reservación N° ".$publicId.
                    " por ".str_replace('.',',',(string)$amount).
                    " a nombre ".($newReservation->getClientId()->getGender()==0 ? 'del Sr. ' : 'de la Sra. ' ).
                    $newReservation->getClientId()->getFullName(),
                'ip' => $this->command->get('ip'),
                'checkInDate' => $newReservation->getDateCheckIn(),
                'amount' => $amount,
                'date' => \date('d-m-Y'),
                'aavv' => CoreSession::getUser()->getAavvProfile()->getAavv(),
                'total_amount_per_aavv' => $totalAmount,
            ];
        return $response;
    }

    /**
     * Funcion encargada de realizar el desbloqueo de la disponibilidad y el decremento en la cantidad
     * de habitaciones que quedan luego de realizar la reserva
     *
     * @param $arrayOfReservations array, $arrayOfReservations['success] -> [reservation] y [cancellationP]
     */
    private function unlockAndDecrementReservations($arrayOfReservations)
    { 
        LockedAvailabilityService::unlockedAvailability($this->rf);

        foreach ($arrayOfReservations as $objReservation) {
            // Decrementamos las disponibilidades
            InventoryService::decreaseInventoryReserves($objReservation, $this->rf);
        }
    }

    /**
     * Funcion para generar por cada reserva los movimientos financieros a la aavv
     *
     * @param $aavv
     * @param $arrayOfReservations
     * @param $rf
     * @version 28/09/2016
     */
    private function generateMovementForAavv($aavv, $arrayOfReservations)
    {
        new AAVVAccountingService($this->rf);
        foreach ($arrayOfReservations as $newReservation) {
            $response['amount'] = $newReservation->getTotalToPay(false) + $newReservation->getTaxPay(); //tarifa del cliente sin iva
            $response['sign'] = "+";
            $publicId = $newReservation->getPublicId() instanceof PublicId ?
                $newReservation->getPublicId()->toString() :
                $newReservation->getPublicId();
            $msj = "aavv.successfully_processed_reservation_code";
            $response['description'] = $msj.$publicId;
            AAVVAccountingService::setMovement($response, $aavv);
        }
    }

    /**
     * Funcion encargada de realizar el envio de correos si la aavv tiene credito disponible
     * a partir del objeto $arrayOfReservations que contiene un sub arreglo llamado 'success'
     * y dentro de el dos objetos, 'reservation' es el reservation y 'cancellationPolicy' los
     * datos enviados desde frontend, es decir $request
     *
     * @param $arrayOfReservations array, $arrayOfReservations['success] -> [reservation] y [cancellationP]
     * @param $domain string Dominio desde el cual se esta realizando la peticion del logo
     * @param bool $sendClientEmail si quiere el cliente recibir el correo
     * @param int $reservation_type 0 pre-reserva, 2 reserva
     * @return int
     */
    private function sendEmailToClient($arrayOfReservations, $domain, $sendClientEmail = false, $reservation_type)
    {
        foreach ($arrayOfReservations as $reservation) {
            $property = $reservation->getPropertyId();
            $publicId = $reservation->getPublicId();
            $aavv = $reservation->getAavvReservationGroup()->getAavv();
            // Datos del email para el property
            $dataEmail['propertyName'] = $property->getName();
            $dataEmail['propertyImage'] = str_replace(' ', '%20', $property->getProfileImage()->getImage()->getFileName());
            $dataEmail['clientNames'] = $reservation->getClientId()->getFullName();
            $dataEmail['clientGender'] = $reservation->getClientId()->getGender();

            $dataEmail['publicReservationId'] = empty($publicId) ?
                null :
                $publicId instanceof PublicId ?
                    $publicId->toString() :
                    $publicId;

            // Incluimos las variables del correo para el cliente
            $dataEmail['clientEmail'] = $reservation->getClientId()->getEmail();
            $dataEmail['userToken'] = $reservation->getHashUrl();
            $dataEmail['username'] = $reservation->getClientId()->getUser()->getUsername();

            // Informacion referente al property
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
            $dataEmail['subTotalAavv'] = $reservation->getNetRateAavv(false); //tarifa de la aavv sin iva
            $dataEmail['taxAavv'] = $reservation->getTaxNetRateAavv();

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

                if (isset($bedroom)) {
                    foreach ($bedroom['beds'] as $bed)
                        $auxPack['bedsType'] = $auxPack['bedsType'] .
                            $bed['typeString'] .
                            ($bed['amount'] == 1 ? '' : '(' . $bed['amount'] . ') + ');
                    trim($auxPack['bedsType']);
                    trim($auxPack['bedsType'], '+');
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
                $dataEmail['totalRooms'] = $dataEmail['totalRooms'] + $auxPack['numberPack'];

                array_push($dataEmail['packages'], $auxPack);
            }

            $emailService = $this->getEmailService();
            $emailService->setConfigEmail('first_mailer');
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationConfirmation.html.twig');
            $emailService->setSubject('Confirmación de la Reserva - navicu.com');
            $emailService->setEmailSender('info@navicu.com');


            $dataEmail['customize'] = $aavv->getCustomize();

            if (!is_null($aavv->getdocumentByType('LOGO'))) {
                $dataEmail['logo'] = $domain .
                    '/uploads/images/images_original/' .
                    $aavv->getdocumentByType('LOGO')->getDocument()->getFileName();
            }

            // Si es reserva
            if ($reservation_type == 2) {
                $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationConfirmation.html.twig');
                $emailService->setSubject('Confirmación de la Reserva - navicu.com');
            } else { //pre-reserva
                $emailService->setViewRender('NavicuInfrastructureBundle:Email:preReservationConfirmation.html.twig');
                $emailService->setSubject('Reserva Pendiente de Pago - navicu.com');
            }
            $emailService->setEmailSender('info@navicu.com');

            // TODO: (descomentar) Si esta marcada la opcion de enviar el correo al cliente, mejorar la logica del get personalized mail
            /*if ($aavv->getPersonalizedMail() AND $sendClientEmail)
                $emailService->setRecipients([$dataEmail['clientEmail']]);
            $emailService->setViewParameters($dataEmail);
            $emailService->sendEmail();*/

            // si solo se debe enviar al staf navicu
            // Envio de correo al hotelero
            foreach($property->getContacts() as $contact) {
                if($contact->getEmailReservationReceiver())
                    $dataEmail['ownerEmail'][] = $contact->getEmail();
            }

            $emailService->setRecipients($dataEmail['ownerEmail']);
            $dataEmail['sendOwner'] = true;
            $emailService->setViewParameters($dataEmail);
            $emailService->sendEmail();

            // Envio de correo al staff navicu
            $dataEmail['sendOwner'] = false;
            $dataEmail['sendAavv'] = true;
            if ($aavv->getPersonalizedMail()) {
                $dataEmail['customize'] = $aavv->getCustomize();

                if (!is_null($aavv->getdocumentByType('LOGO'))) {
                    $dataEmail['logo'] = $domain .
                        '/uploads/images/images_original/' .
                        $aavv->getdocumentByType('LOGO')->getDocument()->getFileName();
                }
            }

            $emailService->setRecipients([
                'mmarchan@navicu.com',
                'yzuzolo@gmail.com',
                'jacastro@navicu.com',
                'dmora@navicu.com',
                'eblanco@navicu.com'
            ]);

            $emailService->setViewParameters($dataEmail);
            $emailService->sendEmail();

            // TODO: Envio del correo al cliente, mejorar la logica del get personalized mail
            /*if ($aavv->getPersonalizedMail() AND $sendClientEmail)
                $emailService->setRecipients([$dataEmail['clientEmail']]);*/

            /*$emailService->setViewParameters($dataEmail);
            $emailService->sendEmail();*/

            // Envio de correo a la AAVV
            $dataEmail['sendOwner'] = true;
            foreach ($aavv->getAavvProfile() as $currentProfile)
                if ($currentProfile->getConfirmationemailreceiver())
                    $recipients[] = $currentProfile->getEmail();

            if (isset($recipients)) {
                $emailService->setRecipients($recipients);

                $emailService->setViewParameters($dataEmail);
                $emailService->sendEmail();
            }
            $dataEmail = [];
        }
    }

    /**
     * Funcion encargada de enviar un correo a la agencia de viajes cuando se ha quedado sin credito
     *
     * @param $aavv object, agencia de viajes que realiza la reserva
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 24/11/2016
     */
    private function sendDeactivateEmailToAAVV($aavv)
    {
        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');
        $emailService->setViewParameters(['deactivateAavv' => true, 'agencyName' => $aavv->getCommercialName()]);

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:insufficientCreditAAVV.html.twig');
        $emailService->setSubject('Crédito insuficiente');
        $emailService->setEmailSender('info@navicu.com');

        $recipients = [];
        foreach ($aavv->getAavvProfile() as $currentProfile)
            $recipients[] = $currentProfile->getEmail();

        $emailService->setRecipients($recipients);
        $emailService->sendEmail();
        $aavv->setSentEmailForInsufficientCredit(true);

    }

    /* Cambiamos el status de la reserva*/
    private function setStatusReservation(Reservation $newReservation)
    {    
        $pgw = $this->command->get('paymentGateway');
        $status = $pgw->getStatusReservation($newReservation);
        $newReservation->setState($status);
    }

    /* Funcion para obtener el estado de las reservas */
    private function getStatusReservation(AAVVReservationGroup $reservationGroup)
    {
        $status = 0;
        $reservations = $reservationGroup->getReservation();
        foreach ($reservations as $reservation) {
            $pgw = $this->command->get('paymentGateway');
            $status = $pgw->getStatusReservation($reservation);
        }
        return $status;
    }

    /**
     * La siguiente función de enviar el correo personalizado a la aavv y al staf navicu
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param AAVVReservationGroup $reservationGroup
     * @param null $totalToPay lo que pagara la aavv por el grupo de reservas
     * @param $slugLocation
     * @param $domain string Dominion de la aavv
     * @param int $reservation_type 0 pre-reserva, 1 reserva
     */
    private function sendEmailAAVV(AAVVReservationGroup $reservationGroup, $totalToPay = null, $slugLocation, $domain, $reservation_type)
    {
        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

        $location = $this->rf->get('Location')->findOneBy(['slug' => $slugLocation]);

        if (!is_null($reservationGroup->getAavv()->getdocumentByType('LOGO'))) {
            $data['logo'] = $domain .
                '/uploads/images/images_original/' .
                $reservationGroup->getAavv()->getdocumentByType('LOGO')->getDocument()->getFileName();
        }

        $data = [
            'isCustomize' => $reservationGroup->getAavv()->getPersonalizedMail(),
            'customize' => $reservationGroup->getAavv()->getCustomize(),
            'reservationGroup' => $reservationGroup,
            'totalToPay' => $totalToPay,
            'location' => $location ? $location->getTitle() : null,
            'domain' => $domain,
            'reservationType' => $reservation_type
        ];

        $emailService->setViewParameters($data);

        // si es una reserva
        if ($reservation_type == 2)
            $emailService->setSubject('Confirmación de la Reserva');
        else //pre-reserva
            $emailService->setSubject('Reserva Pendiente de Pago');

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:aavvReservation.html.twig');
        $emailService->setEmailSender('info@navicu.com');

        $recipients = [];
        // TODO: (Descomentar) Envio al perfil de la agencia de viajes momentaneamnete comentado
        /*foreach ($reservationGroup->getAavv()->getAavvProfile() as $currentProfile)
            if ($currentProfile->getConfirmationemailreceiver())
                $recipients[] = $currentProfile->getEmail();

        $emailService->setRecipients($recipients);
        $emailService->sendEmail();*/

        $emailService->setRecipients([
            'yzuzolo@gmail.com',
            'jacastro@navicu.com',
            'mcontreras@navicu.com',
            'mmarchan@navicu.com'
        ]);

        $emailService->sendEmail();
    }
}
