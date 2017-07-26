<?php
namespace Navicu\Core\Application\UseCases\Reservation\CalculateRateReservation;

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
class CalculateRateReservationHandler implements Handler
{

    /**
     * instancia del repositoryFactory
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
        $this->command = $command;
        $rpProperty = $this->rf->get('Property');
        $rpReservation = $this->rf->get('Reservation');
        $property = $rpProperty->findOneBy(['slug' => $command->get('slug')]);
        if (isset($property)) {
            try {
                $reservation = $this->getReservation();
                $reservation->setPropertyId($property);
                $errorsRoom = $this->getRoomsData($command->get('rooms'), $reservation);
                if (empty($errorsRoom)) {
                    $reservation->roundReservation();
                    $data = $this->getReservationdata($reservation);
                    $response = new ResponseCommandBus(201, 'OK', $data);
                } else {
                    $response = new ResponseCommandBus(
                        400,
                        'Bad Request',
                        array_merge(['errorStatus' => 0],$errorsRoom)
                    );
                }
            } catch (EntityValidationException $e) {
                $response = new ResponseCommandBus(
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
                $response = new ResponseCommandBus(
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
        } else {
            $response = new ResponseCommandBus(404,'not found',['message'=>'Establecimiento no existe']);
        }
        return $response;
    }

    private function getReservation()
    {
        $reservation = new Reservation();
        $reservation
            //->setReservationDate(new \DateTime(date("Y-m-d H:m:s")))
            //->setPaymentType($this->command->get('paymentGateway')->getTypePayment())
            ->setDateCheckIn(new \DateTime($this->command->get('checkinReservation')))
            ->setDateCheckOut(new \DateTime($this->command->get('checkoutReservation')));
            //->setSpecialRequest($this->command->get('clientLiked'))
            //->setGuest($this->command->get('guest'));
        return $reservation;
    }

    /**
     * La siguiente función se encarga de obtener los datos de las habitaciones y servicios
     * dada la busqueda para la reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param $rooms
     * @param $reservation
     * @throws EntityValidationException
     * @return boolean
     * @version 01/09/2015
     */
    private function getRoomsData($rooms, $reservation)
    {
        $error = [];
        $response['rooms'] = [];
        $rpRoom = $this->rf->get('Room');
        $rpPack = $this->rf->get('Pack');
        $rpDailyRoom = $this->rf->get('DailyRoom');

        $totalAdults = 0;
        $totalKids = 0;
        foreach ($rooms  as $currentRoom) {
            $room = $rpRoom->findOneBy(['id' => $currentRoom['idRoom']]);

            if(!$room || $room->getProperty()->getId() != $reservation->getPropertyId()->getId())
                throw new EntityValidationException('rooms',\get_class($this),'not_exist_room');

            $bedRooms = null;

            foreach ($room->getBedrooms() as $currentBedroom)
                if ($currentBedroom->getId() == $currentRoom['idBedsType'])
                    $bedRooms = $currentBedroom;

            $checkin = $this->command->get('checkinReservation');
            $checkout = $this->command->get('checkoutReservation');
            if( strtotime($checkin) < strtotime(date('d-m-Y')) )
                throw new EntityValidationException('checkinReservation',\get_class($this),'expired_date_checkin');

            if( strtotime($checkout) <= strtotime($checkin) )
                throw new EntityValidationException('checkoutReservation',\get_class($this),'expired_date_checkout');

            foreach ( $currentRoom['packages'] as $currentPack) {

                $reservationPack = new ReservationPack();
                $reservationPack->setBedroom(isset($bedRooms) ? $bedRooms->toArray() : null);
                $pack = $rpPack->findOneBy(['id' => $currentPack['idPack']]);

                if (!$pack || $pack->getRoom()->getId() != $room->getId())
                    throw new EntityValidationException('rooms',\get_class($this),'not_exist_pack');

                $reservationPack->setTypePack($pack->getType());
                $reservationPack->setTypeRoom($room->getType());
                $reservationPack->setPackId($pack);

                $policyCancellation = null;
                foreach ($reservation->getPropertyId()->getPropertyCancellationPolicies() as $pcp)
                    if($pcp->getCancellationPolicy()->getId()==$currentPack['idCancellationPolicy'])
                        $policyCancellation = $pcp;

                if(!isset($policyCancellation))
                    throw new EntityValidationException('rooms',\get_class($this),'not_exist_cancellation_policy');

                $reservationPack->setPropertyCancellationPolicyId($policyCancellation);

                $reservationPack->setTypeCancellationPolicy(
                    $policyCancellation
                        ->getCancellationPolicy()
                        ->getType()
                );
                $reservationPack->setCancellationPolicy(
                    $policyCancellation
                        ->getCancellationPolicy()
                        ->toArray()
                );
                $numberChildren = !empty($currentPack['numberChildren']) ? $currentPack['numberChildren'] : 0;
                $totalAdults = $totalAdults + ($currentPack['numberPeople'] * $currentPack['numberPack']);
                $totalKids =  $totalKids + ($numberChildren * $currentPack['numberPack']);
                $reservationPack->setNumberRooms($currentPack['numberPack']);
                $reservationPack->setNumberAdults($currentPack['numberPeople']);
                $reservationPack->setNumberKids($numberChildren);
                $reservationPack->setReservationId($reservation);
                $reservation->addReservationPackage($reservationPack);
            }
        }

        $reservation
            ->setChildNumber($totalKids)
            ->setAdultNumber($totalAdults);

        $currency = CoreSession::get('alphaCurrency');

        //Retorna si los datos de la BD son iguales a los del formulario
        try {
            new RateCalculator($this->rf);
            RateCalculator::calculateRateReservation (
                $this->rf,
                $reservation,
                0, //default cutoff
                $this->command->get('typeIdentity') != "J" && $this->command->get('typeIdentity') != "j" && ($currency = 'VEF')//condicion de cliente
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

    public function getReservationData($reservation) {

        new RateExteriorCalculator($this->rf,null,$reservation->getDateCheckIn());

        $total = $reservation->getTotalToPay();
        $subtotal = $reservation->getTotalToPay(false);
        $tax = $total - $subtotal;

        return [
            'total' => RateExteriorCalculator::calculateRateChange($total),
            'subTotal' => RateExteriorCalculator::calculateRateChange($subtotal),
            'tax' => RateExteriorCalculator::calculateRateChange($tax),
            'recalculated' => RateCalculator::$recalculated,
        ];
    }
}
