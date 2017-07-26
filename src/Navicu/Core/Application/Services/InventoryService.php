<?php

namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Adapter\RepositoryFactory;
use Navicu\Core\Domain\Model\Entity\ReservationPack;

class InventoryService extends RateCalculator
{
    private $rf;

    /**
    * Constructor del servicio
    *
    * @param RepositoryFactoryInterface $rf
    */
    public function __construct()
    { 
        $this->rf = new RepositoryFactory;
    }

    /**
     * Función usada para retornar la disponibilidad de los
     * dailyPack y dailyRoom partiendo del cambio de estado
     * de una reserva a cancelada.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param Reservation $reservation
     * @internal param Array $request
     * @return bool
     */
    public function increasedInventoryReserves(Reservation $reservation)
    {
        $rpDailyPack = $this->rf->get('DailyPack');
        $rpDailyRoom = $this->rf->get('DailyRoom');

        $checkIn = $reservation->getDateCheckIn()->format('Y-m-d');
        $checkOut = $reservation->getDateCheckOut();

		// Restando un dia a la fecha de salida.
		date_sub($checkOut, date_interval_create_from_date_string('1 days'));
		$checkOut = $checkOut->format('Y-m-d');

        foreach ($reservation->getReservationPackages() as $reservationPack) {

            $numberRooms = $reservationPack->getNumberRooms();
            $pack = $reservationPack->getPackId();

            $groupDailyPack = $rpDailyPack->findByDatesRoomId($pack->getId(), $checkIn, $checkOut);

            $room = $pack->getRoom();
            $groupDailyRoom = $rpDailyRoom->findByDatesRoomId($room->getId(), $checkIn, $checkOut);

			for ($dp = 0; $dp < count($groupDailyPack); $dp++) {
                $groupDailyPack[$dp]->setSpecificAvailability(
                    $groupDailyPack[$dp]->getSpecificAvailability () +
                    $numberRooms
                );
                $rpDailyPack->save($groupDailyPack[$dp]);
            }

			for ($dr = 0; $dr < count($groupDailyRoom); $dr++) {    
                $groupDailyRoom[$dr]->setAvailability(
                    $groupDailyRoom[$dr]->getAvailability () +
                    $numberRooms
                );
                $rpDailyRoom->save($groupDailyRoom[$dr]);
            }

        }
        return true;
    }

    /**
     * consigue la mayor antelacion entre un conjunto de habitaciones en un par de fechas determinadas
     *
     * @param RepositoryFactoryInterface $rf
     * @param $rooms
     * @param $checkInDate
     * @param $checkOutDate
     *
     * @return int
     */
    public static function maxAdvance(RepositoryFactoryInterface $rf,$rooms,$checkInDate,$checkOutDate)
    {
        $maxAdvanced = 0;

        $checkInDate = new \DateTime($checkInDate);
        $checkOutDate = new \DateTime($checkOutDate);

        $rpDailyRoom = $rf->get('DailyRoom');
        foreach ($rooms as $room) {
            $dailyRooms = $rpDailyRoom->findByDatesRoomId (
                $room['idRoom'],
                $checkInDate,
                $checkOutDate
            );

            foreach($dailyRooms as $dr)
                if ($dr->getDate()!=$checkOutDate && $maxAdvanced < $dr->getCutOff())
                    $maxAdvanced = $dr->getCutOff();
        }
        return $maxAdvanced;
    }

    /**
     * esta funcion decerementa el inventario de una reserva
     *
     * @param Reservation $reservation
     * @param RepositoryFactoryInterface $repFac
     * @throws NotAvailableException
     */
    public static function decreaseInventoryReserves(Reservation $reservation, RepositoryFactoryInterface $repFac)
    {
        $repDailyPack = $repFac->get('DailyPack');
        foreach ($reservation->getReservationPackages() as $rp) {

            $dailyRooms = $rp->getDailyRooms();
            $dailyPackages = $rp->getDailyPackages();

            $current = clone $reservation->getDateCheckIn();
            $i = 0;

            while ($current < $reservation->getDateCheckOut()) {

                if ($current != $dailyRooms[$i]->getDate() || $current != $dailyPackages[$i]->getDate())
                    throw new NotAvailableException($current->format('d-m-Y'),'date_not_match');

                $dailyRooms[$i]->setAvailability(
                    $dailyRooms[$i]->getAvailability() - $rp->getNumberRooms()
                );

                $dailyPackages[$i]->setSpecificAvailability(
                    $dailyPackages[$i]->getSpecificAvailability() - $rp->getNumberRooms()
                );

                $dps = $repDailyPack->findByRoomDate($dailyRooms[$i]->getRoom()->getId(), $current);
                foreach ($dps as $dp) {
                    if ($dp->getSpecificAvailability() > $dailyRooms[$i]->getAvailability() && $dp->getId() != $dailyPackages[$i]->getId()) {
                        $dp->setSpecificAvailability(
                            $dailyRooms[$i]->getAvailability()
                        );
                        $persist[] = $dp;
                    }
                }

                $persist[] = $dailyRooms[$i];
                $persist[] = $dailyPackages[$i];
                $current->modify('+1 day');
                $i++;
            }
        }
        $repDailyPack->save($persist);
    }
}