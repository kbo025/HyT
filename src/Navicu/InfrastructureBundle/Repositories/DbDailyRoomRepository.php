<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Domain\Repository\DailyRoomRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad DailyRoom
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbDailyRoomRepository extends DbBaseRepository implements DailyRoomRepository
{
    /**
     * La función retorna un conjunto de dailyRoom dado un rango de fechas y una habitación
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $roomId
     * @param $startDate
     * @param $endDate
     * @param $select
     * 
     * @return mixed
     * @version 16/09/2015
     */
    public function findByDatesRoomId($roomId, $startDate, $endDate, $select = null)
    {
        $query = $this->createQueryBuilder('dr')
            ->where('
                dr.room = :roomId and
                dr.date >= :start_date and
                dr.date <= :end_date
                order by dr.date
                ')
            ->setParameters(
                array(
                    'roomId' => $roomId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                )
            );
        
        if (is_null($select)) {
            return $query->getQuery()->getResult();
        } else {
            return $query->select($select)->getQuery()->getResult();
        }
    }

    /**
     * La siguiente función retorna un dailyRoom
     * dado el id del Room y una fecha
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $date
     * @param $roomId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @version 13/10/2015
     */
    public function findOneByDateRoomId($roomId, $date)
    {
        return  $this->createQueryBuilder('p')
            ->where('
                room.id = :roomId and
                p.date = :date
                order by p.date
                ')
            ->join('p.room', 'room')
            ->setParameters(array(
                'roomId' => $roomId,
                'date' => $date
            ))
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Funcion para retornar los DailyRoom dado un conjunto de roomId
     *
     * @author Isabel Nieto. <isabelcnd@gmail.com>
     * @author Currently Working: Isabel Nieto
     *
     * @param array $roomId conjunto de roomIds a buscar
     * @param array $date conjunto de fechas en las cuales aplicar el filtro de busqueda
     * @param string $today fecha actual en la que se esta haciendo la consulta.
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findDailyRoomsGivenGroupOfRoomId($roomId, $date, $today)
    {
        return $this->createQueryBuilder('dr')
            ->leftJoin('dr.room', 'r')
            ->where('
            r.id IN (:arrayOfRoomId) and
            dr.date IN (:dates) and 
            dr.date >= :today
            ')
            ->setParameters(array(
                'arrayOfRoomId' => $roomId,
                'dates' => $date,
                'today' => $today
            ))
            ->getQuery()
            ->getResult();
//                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * Funcion para buscar los dailyRoom que no poseen disponibilidad los proximos tres meses desde la fecha actual
     * 
     * @param array $propertyId conjunto de id de los properties
     * @param object $today fecha actual de la consulta a la base de datos
     * @param object $nextThreeMonth fecha dentro de tres meses desde la fecha actual
     * @return array conjunto de hoteles con fechas vencidad desde la fecha actual hasta dentro de tres meses
     */
    public function findDailyRoomsTheNextThreeMonth($propertyId, $today, $nextThreeMonth)
    {
        return $this->createQueryBuilder('dr')
            ->join('dr.room','r')
            ->where('
            dr.date >= :today and
            dr.date <= :threeMonth   and
            dr.availability IS NULL
            ')
            ->join('r.property', 'p')
            ->andWhere('p.id IN (:arrayOfPropertyId)')
            ->orderBy('dr.date','ASC')
            ->setParameters(array(
                'arrayOfPropertyId' => $propertyId,
                'today' => $today,
                'threeMonth' => $nextThreeMonth
            ))
            ->getQuery()
            ->getResult();
//            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); /*Para devolverlo como un array y no como un objeto*/
    }
}