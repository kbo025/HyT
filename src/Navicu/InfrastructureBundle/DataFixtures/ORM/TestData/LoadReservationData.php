<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 28/08/15
 * Time: 03:31 PM
 */

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationPack;

class LoadReservationData {
    /*for ($i=1; $i<4; $i++) {
        $reservation = new Reservation;
        $reservation->setDateCkeckIn(new DateTime());
        $reservation->setDateCkeckOut(new DateTime());
        $reservation->setChildNumber(rand(1,$i));
        $reservation->setAdultNumber(rand(1,$i));
        $reservation->setSpecialRequest('SpecialRequestSpecialRequestSpecialRequestSpecialRequestSpecialRequest');
        $reservation->setTotalToPay();
        for (j=1;j<4;j++) {
            $reservationPack = new ReservationPack();
            $reservationPack->setNumberRooms();
            $reservationPack->setPrice();
            $reservationPack->setNumberPeople();
            $reservation->addReservationsPackages($reservationPack);
        }
        $reservation->setTaxPay();
    }*/
}