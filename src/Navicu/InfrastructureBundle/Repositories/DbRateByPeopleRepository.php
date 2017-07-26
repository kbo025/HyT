<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\RateByPeople;
use Navicu\Core\Domain\Repository\RateByPeopleRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidadRateByPeople
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbRateByPeopleRepository extends EntityRepository 
	implements RateByPeopleRepository
{
	/**
	 * La siguiente función retorna el monto de cobró por persona
	 * dado un número de personas
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $roomId
	 * @param $numberPeople
	 * @return mixed
	 */
	public function findAmountByRoomPeople($roomId, $numberPeople)
	{
		return $this->createQueryBuilder('p')
			->select('p.amount_rate as amountRate')
			->where('
				p.room = :roomId and
				p.number_people = :numberPeople
                ')
			->setParameters(
				array(
					'roomId' => $roomId,
					'numberPeople' => $numberPeople
				)
			)
			->getQuery()->getResult();
	}

    /**
     * La siguiente función retorna un objeto RateByPeople dada una habitacion
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $roomId
     * @param $numberPeople
     * @return mixed
     */
    public function findOneByRoomPeople($roomId, $numberPeople)
    {
        return $this->createQueryBuilder('p')
            ->where('
				p.room = :roomId and
				p.number_people = :numberPeople
                ')
            ->setParameters(
                array(
                    'roomId' => $roomId,
                    'numberPeople' => $numberPeople
                )
            )
            ->getQuery()->getSingleResult();
    }
}