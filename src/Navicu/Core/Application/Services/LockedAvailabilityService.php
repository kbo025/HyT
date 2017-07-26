<?php
namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\NotAvailableException;
use Navicu\Core\Domain\Model\Entity\LockedAvailability;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Adapter\CoreSession;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * servicio que maneja el bloqueo y desbloqueo de la disponibilidad de las habitaciones
 */
class LockedAvailabilityService
{
    /**
     * instancia del repository factory
     */
    static private $rf;

    /**
     * constructor
     * @param RepositoryFactoryInterface $rf
     */
    public function __construc(RepositoryFactoryInterface $rf)
    {
        self::$rf = $rf;
    }

    /**
     * esta función bloquea la disponibilidad de una reserva mientras
     *
     * @param RepositoryFactoryInterface $rf
     * @param Reservation $reservation
     * @param int $time
     * @throws NotAvailableException
     * @return bool
     */
    public static function lockAvailability(RepositoryFactoryInterface $rf, Reservation $reservation, $time = 600)
    {
        //repositorio de Daily Pack y Daily Room
        //$rpDailyPack = self::$rf->get('DailyPack');
        //$rpDailyRoom = self::$rf->get('DailyRoom');
        $rpLocked = $rf->get('LockedAvailability');
        $dataPersist = [];
        $idSession = CoreSession::setSessionReservation();
        foreach($reservation->getReservationPackages() as $rp) {
            //consultar los daily pack del entre las fechas especificadas
            /*$dailyPackages = $rpDailyPack->findByDatesRoomId(
                self::$rp->getPackId()->getId(),
                $reservation->getDateCheckIn()->format('Y-m-d'),
                $reservation->getDateCheckOut()->format('Y-m-d')
            );*/
            $dailyPackages = $rp->getDailyPackages();

            /*$dailyRooms = $rpDailyRoom->findByDatesRoomId(
                self::$rp->getPackId()->getId(),
                $reservation->getDateCheckIn()->format('Y-m-d'),
                $reservation->getDateCheckOut()->format('Y-m-d')
            );*/
            $dailyRooms = $rp->getDailyRooms();

            $current = clone $reservation->getDateCheckIn();
            $i = 0;
            while ($current < $reservation->getDateCheckOut()) {

                if ($current != $dailyRooms[$i]->getDate() || $current != $dailyPackages[$i]->getDate())
                    throw new NotAvailableException($current->format('d-m-Y'),'date_not_match');

                $lock = new LockedAvailability();
                $lock
                    ->setNumberPackages($rp->getNumberRooms())
                    ->setBlockedDr($dailyRooms[$i])
                    ->setExpiry(strtotime("now")+$time)
                    ->setIdSession($idSession)
                    ->setBlockedDp($dailyPackages[$i])
                    ->setBlockedReservation($reservation);
                $i++;
                $current->modify('+1 day');
                $dataPersist[] = $lock;
            }
        }
        $rpLocked->save($dataPersist);
        return $idSession;
    }

    /**
     * esta función desbloquea la disponibilidad asignada al usuario que hizo la solicitud
     */
    public static function unlockedAvailability(RepositoryFactoryInterface $rf)
    {
        $sessions = CoreSession::getSessionReservation();
        $amount = 0;
        $rpLocked = $rf->get('LockedAvailability');
        foreach ($sessions as $session) {
            $amount = $amount + $rpLocked->deleteBySessions($session);
        }
        return $amount;
    }

    /**
     * esta funcion renueva el mensaje flash que contiene el id de sessionReservation
     */
    public static function renewAvailability($time = 600,RepositoryFactoryInterface $rf)
    {
        $session[] = CoreSession::getSessionReservation();
        $rpLocked = $rf->get('LockedAvailability');
        $rpLocked->renewSessionReservation($session,$time);
        CoreSession::renewSessionReservation($session);
        return true;
    }

    /**
     * esta fucnion elimina los bloqueos vencidos
     */
    public static function cleanExpiredLockAvailability(RepositoryFactoryInterface $rf)
    {
        $rpLocked = $rf->get('LockedAvailability');
        $rpLocked->cleanExpired();
        return true;
    }
}