<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 15/11/16
 * Time: 09:57 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Reservation\SendConfirmationEmails;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Adapter\CoreTranslator;

class SendConfirmationEmailsHandler implements Handler
{

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
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();

        $domain = $request['domain'];

        $rgroup_rep = $rf->get('AAVVReservationGroup');

        $globals_rep = $rf->get('AAVVGlobal');

        //Parametro que indica cuanto retrrasar el envio de correos
        $delay = $globals_rep->getParameter('emailDelay');


        $groups = $rgroup_rep->findWithPendingEmails($delay);

        $reservationsArray = [];

        foreach ($groups as $group){
            $reservations = $group->getReservation();
            foreach ($reservations as $reservation) {
                $reservationsArray[] = $reservation;
            }
            $group->setStatus(1);
            $rgroup_rep->save($group);
        }

        if (count($reservationsArray) > 0)
            $this->sendEmailToClient($reservationsArray, true, $domain);

        return new ResponseCommandBus(200, 'ok');
    }

    private function sendEmailToClient($arrayOfReservations, $send_email, $domain)
    {
        $aavv = $arrayOfReservations[0]->getAavvReservationGroup()->getAavv();
        foreach ($arrayOfReservations as $objReservation) {
            $newReservation = $objReservation;
            $guestEmail = $newReservation->getClientId()->getEmail();

            if ( ($aavv->getPersonalizedMail()) AND (!is_null($send_email)) AND ($send_email) )
                $this->processDataEmail($newReservation, $guestEmail, $domain);
        }
    }

    private function processDataEmail($reservation, $guestEmail, $domain)
    {
        $property = $reservation->getPropertyId();
        $publicId = $reservation->getPublicId();
        $aavv = $reservation->getAavvReservationGroup()->getAavv();
        // Datos del email para el property
        $dataEmail['propertyName'] = $property->getName();
        $dataEmail['propertyImage'] = str_replace(' ', '%20', $property->getProfileImage()->getImage()->getFileName());
        $dataEmail['clientNames'] = $reservation->getClientId()->getFullName();
        $dataEmail['clientGender'] = $reservation->getClientId()->getGender();

        $dataEmail['sendOwner'] = false;
        $dataEmail['sendAavv'] = true;
        $dataEmail['publicReservationId'] = empty($publicId) ?
            null :
            $publicId instanceof PublicId ?
                $publicId->toString() :
                $publicId;

        // Incluimos los variables del correo para el cliente
        $dataEmail['clientEmail'] = $guestEmail;
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

        // Si esta marcada la opcion de enviar el correo al cliente y ademas tiene la opcion
        // de personalizacion de correo la aavv
        $emailService->setRecipients([$dataEmail['clientEmail']]);
        $dataEmail['customize'] = $aavv->getCustomize();

        if (!is_null($aavv->getdocumentByType('LOGO'))) {
            $dataEmail['logo'] = $domain .
                '/uploads/images/images_original/' .
                $aavv->getdocumentByType('LOGO')->getDocument()->getFileName();
        }
        $emailService->setViewParameters($dataEmail);

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationConfirmation.html.twig');
        $emailService->setSubject('Confirmación de la Reserva - navicu.com');

        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
}