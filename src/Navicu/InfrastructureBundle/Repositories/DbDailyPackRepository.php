<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Repository\DailyPackRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad DailyPack
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 28/08/15
 */
class DbDailyPackRepository extends DbBaseRepository implements DailyPackRepository
{
    /**
     * La siguiente función retorna el conjunto de dailyPack en un rango de fechas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>     *
     * @param $packId
     * @param $startDate
     * @param $endDate
     * @return mixed
     * @version 01/08/2015
     */
    public function findByDatesRoomId($packId, $startDate, $endDate)
    {
        return $this->createQueryBuilder('p')
            ->where('
                p.pack = :packId and
                p.date >= :start_date and
                p.date <= :end_date
                order by p.date
                ')
            ->setParameters(
                array(
                    'packId' => $packId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                )
            )
            ->getQuery()->getResult();
    }

    /**
     * La siguiente función retorna el conjunto de dailyPack en un rango de fechas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $packId
     * @param $startDate
     * @param $endDate
     * @param $select
     * 
     * @return mixed
     * @version 19/08/2015
     */
    public function findByDatesPackId($packId, $startDate, $endDate, $select = null)
    {
        $query = $this->createQueryBuilder('dp')
            ->where('
                dp.pack = :packId and
                dp.date >= :start_date and
                dp.date <= :end_date
                order by dp.date
                ')
            ->setParameters(
                array(
                    'packId' => $packId,
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
     * La siguiente función retorna un dailyPack
     * dado el id del Pack y una fecha
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $packId
     * @param $date
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByPackIdDate($packId, $date)
    {
        return $this->createQueryBuilder('dp')
            ->where('
                pack = :packId and
                dp.date = :date
                ')
            ->setParameters(array(
                'packId' => $packId,
                'date' => $date
            ))
            ->join('dp.pack','pack')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Esta función retorna los dailyPackages dado un Id de una habitación y
     * una fecha.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Integer $idRoom
     * @param \DateTime $ate
     * @param Integer $availability
     * @return Array
     */
    public function findByRoomDate($idRoom, $date)
    {
        return $this->createQueryBuilder('dp')
            ->select('dp')
            ->join('dp.pack', 'p')
            ->where('
                p.room = :roomId and
                dp.date = :date
                ')
            ->setParameters(array(
                'roomId' => $idRoom,
                'date' => $date
            ))
            ->getQuery()->getResult();
    }

    /**
     * Esta función retorna los dailyPackages dado un Id de una habitación y
     * un rango de fechas.
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @author Currently Working: Jose Agraz <jaagraz@navicu.com>
     *
     * @param Integer $idRoom
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Array
     * * @version 12/01/2016
     */
    public function findByRoomDateRange($idRoom, $startDate, $endDate)
    {
        return $this->createQueryBuilder('dp')
            ->select('dp')
            ->join('dp.pack', 'p')
            ->where('
                p.room = :roomId and
                dp.date >= :start_date and
                dp.date <= :end_date
                order by dp.date
                ')
            ->setParameters(array(
                'roomId' => $idRoom,
                'start_date' => $startDate,
                'end_date' => $endDate
            ))
            ->getQuery()->getResult();
    }

    /**
     * La función retorna el conjunto de DailyPacks, dado el Id de habitación y
     * una fecha
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Integer $idRoom
     * @param \DateTime $ate
     * @return Array
     */
    public function findPackagesByRoomIdDate($idRoom, $date)
    {
        return $this->createQueryBuilder('dp')
            ->select('dp')
            ->join('dp.pack', 'pack')
            ->join('pack.room','room')
            ->where('
                room.id = :roomId and
                dp.date = :date
                ')
            ->setParameters(array(
                'roomId' => $idRoom,
                'date' => $date

            ))
            ->getQuery()->getResult();
    }

    /**
     * Funcion para retornar los DailyPacks asociados al conjunto de roomId.
     *
     * @author Isabel Nieto. <isabelcnd@gmail.com>
     * @author Currently Working: Isabel Nieto
     * 
     * @param array $roomId conjunto de roomIds a buscar
     * @param array $date conjunto de fechas en las cuales aplicar el filtro de busqueda
     * @param string $today fecha actual en la que se esta haciendo la consulta.
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findDailyPacksGivenGroupOfRoomId($roomId, $date, $today)
    {
        return $this->createQueryBuilder('dp')
            ->leftJoin('dp.pack', 'p')
            ->leftJoin('p.room', 'r')
            ->where('
            r.id IN (:arrayOfRoomId) and
            dp.date IN (:dates) and 
            dp.date >= :today
            ')
            ->setParameters(array(
                'arrayOfRoomId' => $roomId,
                'dates' => $date,
                'today' => $today
            ))
            ->getQuery()
            ->getResult();
    }


    /**
     * Funcion para retornar los DailyPacks asociados al conjunto de packId.
     *
     * @author Isabel Nieto. <isabelcnd@gmail.com>
     * @author Currently Working: Isabel Nieto
     *
     * @param array $packId conjunto de roomIds a buscar
     * @param array $date conjunto de fechas en las cuales aplicar el filtro de busqueda
     * @param string $today fecha actual en la que se esta haciendo la consulta.
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findDailyPacksGivenGroupOfPackId($packId, $date, $today)
    {
        return $this->createQueryBuilder('dp')
            ->leftJoin('dp.pack', 'p')
            ->where('
            p.id IN (:arrayOfPackId) and
            dp.date IN (:dates) and 
            dp.date >= :today
            ')
            ->setParameters(array(
                'arrayOfPackId' => $packId,
                'dates' => $date,
                'today' => $today
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion encargada de obtener los dailyPacks de las habitaciones dada una fecha y una habitacion
     *
     * @param $currentDate
     * @param $roomId
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/09/2016
     */
    public function findDailyPacksGivenDateAndRoomId($currentDate, $roomId)
    {
        return $this->createQueryBuilder('dp')
            ->join('dp.pack', 'pk')
            ->leftjoin('pk.room', 'r')
            ->where('
            r.id = :roomId and
            dp.date = :currentDate and            
            dp.is_completed = true and 
			dp.specific_availability <> 0
            ')
            ->setParameters(array(
                'currentDate' => $currentDate,
                'roomId' => $roomId
            ))
            ->getQuery()
            ->getResult();
    }
}