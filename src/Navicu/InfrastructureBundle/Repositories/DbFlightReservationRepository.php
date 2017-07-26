<?php


namespace Navicu\InfrastructureBundle\Repositories;


use Navicu\Core\Domain\Repository\FlightReservationRepository;

class DbFlightReservationRepository extends DbBaseRepository implements FlightReservationRepository
{
	public function findByPublicId($id)
	{
		return $this->createQueryBuilder('u')
		            ->where('
                u.public_id = :id

                ')
		            ->setParameters(
			            array(
				            'id' => $id
			            )
		            )->getQuery()->getOneOrNullResult();
	}
}