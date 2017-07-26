<?php

namespace Navicu\InfrastructureBundle\Repositories;


use Navicu\Core\Domain\Repository\FlightRepository;

class DbFlightRepository extends DbBaseRepository implements FlightRepository
{
    public function findByReservationAndDirection($reservationId, $return)
    {
        return $this->createQueryBuilder('u')
            ->where('u.reservation = :reservationId
                              and u.return_flight = :return

                ')
            ->setParameters(
                array(
                    'reservationId' => $reservationId,
                    'return' => $return
                )
            )->getQuery()->getOneOrNullResult();
    }
}