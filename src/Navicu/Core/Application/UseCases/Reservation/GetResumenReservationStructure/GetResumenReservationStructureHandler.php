<?php
namespace Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

class GetResumenReservationStructureHandler implements Handler
{
    /**
     *   instancia del repositoryFactory
     * @var RepositoryFactory $rf
     */
   protected $rf;

    /**
     * comando
     */
    protected $command;

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
        $this->rf = $rf;
        $reservation_repository = $rf->get('Reservation');
        $reservation = $reservation_repository->findOneBy(array('public_id' => $command->get('id')));
        $this->command = $command;

        if (isset($reservation)) {
            if (is_null($reservation->getAavvReservationGroup())) {
                if (!$reservation->isForeignCurrency())
                    $reservation->roundReservation();
            }
            $data = $this->getReservationdata($reservation);
            $response = new ResponseCommandBus(201,'OK',$data);

        } else {
            $response = new ResponseCommandBus(400, 'Bad Requests', array('message' => 'Reservación no existe'));
        }

        return $response;
    }

    /**
     * @param Reservation $reservation
     * @return array
     */
    protected function getReservationdata(Reservation $reservation)
    {
        $response = array();
        $response['propertyImage'] = $reservation->getPropertyId()->getProfileImage()->getImage()->getFileName();
        // Si es para el hotelero
        if ($this->command->get('owner') == 1)
            $rate = 1-$reservation->getPropertyId()->getDiscountRate();
        // Si es para el usuario o para la aavv
        else
            $rate = 1;

        $coordenates = $reservation->getPropertyId()->getCoordinates();
        $response['propertyGps'] = CoordinatesGps::getGps($coordenates['latitude'],$coordenates['longitude']);
        $response['clientNames'] = $reservation->getClientId()->getFullName();
        $response['clientEmail'] = ($reservation->getClientId()->getEmail() instanceof EmailAddress) ?
            $reservation->getClientId()->getEmail()->toString() :
            $reservation->getClientId()->getEmail();
        $response['clientGender'] = $reservation->getClientId()->getGender() ? 0 : 1;
        $response['foreignCurrency'] = $reservation->isForeignCurrency();
        $response['propertyName'] = $reservation->getPropertyId()->getName();
        $response['propertySlug'] = $reservation->getPropertyId()->getSlug();
        $response['propertyAddress'] = $reservation->getPropertyId()->getAddress();
        $response['propertyStars'] = $reservation->getPropertyId()->getStar();
        $response['token'] = $reservation->getHashUrl();
        $response['propertyReservationPhone'] = '';

        foreach ($reservation->getPropertyId()->getContacts() as $contact) {
            if ($contact->getType() == 1) {
                $response['propertyReservationPhone'] = $contact->getPhone();
            }
        }

        $response['propertyLongitude'] = $reservation->getPropertyId()->getCoordinates()['longitude'];
        $response['propertyLatitude'] = $reservation->getPropertyId()->getCoordinates()['latitude'];
        $response['propertyAdditionalInfo'] = array();
        $response['payment'] = [];
        $response['statusReservation'] = $reservation->getState();
        $response['reservationPaymentType'] = $reservation->getPaymentType();
        foreach ($reservation->getPayments() as $currentPayment) {
            $response['payment'][] = $currentPayment->toArray();
        }

        $allIncluded = $reservation->getPropertyId()->getAllIncluded();
        if (!empty($allIncluded)) {
            $response['propertyAdditionalInfo'][] = 'Todo Incluido';
        }

        $child = $reservation->getPropertyId()->getChild();
        if (!empty($child)) {
            $response['propertyAdditionalInfo'][] = 'Acepta el hospedaje de niños';
        }

        $pets = $reservation->getPropertyId()->getPets();
        if (!empty($pets)) {
            $response['propertyAdditionalInfo'][] = 'Se permiten mascotas';
        }

        $debit = $reservation->getPropertyId()->getDebit();
        if (!empty($debit)) {
            $response['propertyAdditionalInfo'][] = 'Acepta tarjeta de debito como forma de pago';
        }

        $cc = $reservation->getPropertyId()->getCreditCard();
        if (!empty($cc)) {
            $cc = 'Acepta tarjeta de crédito como forma de pago';
            $cc = $cc . ($reservation->getPropertyId()->getCreditCardMc() ? ', Master Card' : '');
            $cc = $cc . ($reservation->getPropertyId()->getCreditCardVisa() ? ', Visa' : '');
            $cc = $cc . ($reservation->getPropertyId()->getCreditCardAmex() ? ', American Express' : '');
            $response['propertyAdditionalInfo'][] = $cc;
        }

        $cash = $reservation->getPropertyId()->getCash();
        if (!empty($cash)) {
            $cash = 'Acepta pagos en efectivo';
            $maxCash = $reservation->getPropertyId()->getMaxCash();
            $maxCash = (!empty($maxCash) ? ' hasta ' . $maxCash . 'Bs.' : '');
            $cash = $cash . $maxCash;
            $response['propertyAdditionalInfo'][] = $cash;
        }

        $beds = $reservation->getPropertyId()->getBeds();
        if (!empty($beds)) {
            $response['propertyAdditionalInfo'][] = 'Puede solicitar camas adicionales';
        }

        $cribs = $reservation->getPropertyId()->getCribs();
        if (!empty($cribs)) {
            $response['propertyAdditionalInfo'][] = 'Puede solicitar cunas';
        }

        $response['propertyCheckIn'] = $reservation->getPropertyId()->getCheckIn()->format('H:i');
        $response['propertyCheckOut'] = $reservation->getPropertyId()->getCheckOut()->format('H:i');
        foreach ($reservation->getPropertyId()->getPropertyFavoriteImages() as $favorite) {
            $response['propertyImages'][] = $favorite->getImage()->getWebPath();
        }

        $response['confirmationId'] = $reservation->getPublicId() instanceof PublicId ?
            $reservation->getPublicId()->toString() :
            $reservation->getPublicId();
        $response['checkinReservation'] = $reservation->getDateCheckIn()->format('d-m-Y');
        $response['checkoutReservation'] = $reservation->getDateCheckOut()->format('d-m-Y');
        $response['checkout'] = $reservation->getDateCheckOut()->format('Y-m-d');
        $response['numberAdults'] = $reservation->getAdultNumber();
        $response['numberChildren'] = $reservation->getChildNumber();
        $response['specialRequests'] = $reservation->getSpecialRequest();
        $response['numberPack'] = 0;

        $rooms = array();
        foreach ($reservation->getReservationPackages() as $currentReservationPack) {
            $response['numberPack']++;
            $name = $currentReservationPack->getRoomName();
            $key = array_search($name, array_column($rooms, 'roomName'));
            $pack = array();
            $pack['namePack'] = CoreTranslator::getTranslator($currentReservationPack
                ->getTypePack()
                ->getCode());
	        $childrenAges = $currentReservationPack->getChildrenAge();
	        if(!empty($childrenAges)) {
		        foreach ( $childrenAges as $currentChildrenAge ) {
			        $pack['childrenAges'][] = $currentChildrenAge->getAge();
		        }
	        }
            $pack['pricePack'] = $currentReservationPack->getPrice()/$rate;
            $pack['numberPack'] = $currentReservationPack->getNumberRooms();
            $pack['numberAdults'] = $currentReservationPack->getNumberAdults();
            $pack['numberChildren'] = $currentReservationPack->getNumberKids();
            $pack['namePolicyCancellation'] = CoreTranslator::getTranslator($currentReservationPack
                ->getTypeCancellationPolicy()
                ->getCode()
            );

            if ($key === false) {
                $room = array();
                $room['roomName'] = $name;
                //Images01
                $room['roomImage'] = $currentReservationPack
                    ->getPackId()
                    ->getRoom()
                    ->getProfileImage()
                    ->getImage()
                    ->getWebPath();
                $room['bedsType'] = '';
                $bedroom = $currentReservationPack->getBedroom();

                if(!empty($bedroom)) {
                    foreach ($bedroom['beds'] as $bed)
                        $room['bedsType'] = $room['bedsType'].
                            $bed['typeString'].
                            ($bed['amount']==1 ? '' : '('.$bed['amount'].') + ' );
                    trim($room['bedsType']);
                    trim($room['bedsType'], '+' );
                }

                $room['packages'] = [];
                $room['packages'][] = $pack;
                $rooms[] = $room;
            } else
                $rooms[$key]['packages'][] = $pack;
        }

        $response['reservedNights'] = ($reservation->getDateCheckIn()
            ->diff($reservation->getDateCheckOut())->days);
        $response['isForeignCurrency'] = !$reservation->isForeignCurrency() ? 1 : 2;
        $response['alphaCurrency'] = $reservation->isForeignCurrency() ?
            $reservation->getAlphaCurrency() :
            'Bs';
        $response['alphaCurrencyIso'] = $reservation->isForeignCurrency() ?
            $reservation->getAlphaCurrency() :
            'VEF';
        $response['rooms'] = $rooms;

        if ($reservation->isForeignCurrency())
            $reservation->setTotalToPay(
                $reservation->getCurrencyPrice()
            );

        // Cliente
        if ($this->command->get('owner') == 0) {
            $response['subTotal'] = $reservation->getTotalToPay(false); //tarifa del cliente sin iva
            $response['tax'] = $reservation->getTaxPay();
            $response['total'] = $response['subTotal'] + $response['tax'];
        }
        // Hotelero
        else if ($this->command->get('owner') == 1) {
            $response['subTotal'] = $reservation->getNetRate(false); //tarifa del hotelero sin iva
            $response['tax'] = $reservation->getTaxNetRate();
            $response['total'] = $response['subTotal'] + $response['tax'];
        }
        // AAVV
        else if ($this->command->get('owner') == 2) {
            $response['subTotal'] = $reservation->getNetRateAavv(false); //tarifa de la aavv sin iva
            $response['tax'] = $reservation->getTaxNetRateAavv();
            $response['total'] = $response['subTotal'] + $response['tax'];
        }



        /*$response['total'] = $reservation->getTotalToPay() * $rate;
        $response['tax'] = $reservation->getTaxPay() * $rate;
        $response['subTotal'] = $response['total'] - $response['tax'];*/

        // Para los pdf que son descargados siendo enviados por la aavv
        if (!is_null($reservation->getAavvReservationGroup())) {
            $response['sendAavv'] = true;
            $personalized = $reservation->getAavvReservationGroup()->getAavv()->getPersonalizedMail();
            if ($personalized) {
                // Si fue realizado la reserva por una aavv pero es el hotelero quien esta viendo el PDF
                if ($this->command->get('owner') == 1) {
                    $response['logo'] = $_SERVER['DOCUMENT_ROOT'] . '/images/logo-pdf-resume-reservation.png';
                }
                else {
                    $response['customize'] = $reservation->getAavvReservationGroup()->getAavv()->getCustomize();
                    if (!is_null($reservation->getAavvReservationGroup()->getAavv()->getdocumentByType('LOGO')))
                        $response['logo'] = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $reservation->getAavvReservationGroup()->getAavv()->getdocumentByType('LOGO')->getDocument()->getFileName();
                    else
                        $response['logo'] = $_SERVER['DOCUMENT_ROOT'] . '/images/logo-pdf-resume-reservation.png';
                }
            }
        }
        return $response;
    }
}
