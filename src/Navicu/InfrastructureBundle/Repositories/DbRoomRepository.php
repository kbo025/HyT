<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Repository\RoomRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Room
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
* @version 06/10/15
*/

class DbRoomRepository extends DbBaseRepository implements RoomRepository
{
    /**
     * Remueve Temporalmente de la cache una habitación.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Room
     */
    public function remove($room)
    {
        $this->getEntityManager()->remove($room);
    }


    /**
     * Función para retornar un Room dado un id.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Integer $id
     * @return Object Room
     */
    public function findById($id)
    {
        $response = $this->find($id);
        return $response ? $response : null;
    }

    /**
     * La siguiente función se encarga de consultar una habitación
     * dado un slug y un id de habitación
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $roomId
     * @version 20/11/2015
     *
     */
    public function findOneBySlugRoom($slug, $roomId)
    {
        return $this->createQueryBuilder('r')
            ->where('
                property.slug = :slug and
                r.id = :roomId
                ')
            ->setParameters(array(
                'slug' => $slug,
                'roomId' => $roomId
            ))
            ->join('r.property','property')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Funcion que retorna a partir del IdRoom que se consulta, la fecha en la cual posee dailys con
     * disponibilidad
     * 
     * @author Isabel Nieto. <isabelcnd@gmail.com>
     * @author Currently Working: Isabel Nieto
     *
     * @param object $today fecha actual que se hace la consulta
     * @param object $nextThreeMonth fecha actual mas 3 meses desde que se hizo la consulta
     * @param object $roomId id del room que se esta consultado en la fecha dada
     * @return array con las fechas en las que el pack tiene dailyPacks
     */
    public function findDailyRoomsAvailableGivenDateRange($today, $nextThreeMonth, $roomId)
    {
        return $this->createQueryBuilder('r')
            ->select('dr.date')
            ->join('r.daily_rooms', 'dr')
            ->where('
            r.id = :roomId and
            dr.date >= :today and 
            dr.date < :threeMonth and
            dr.is_completed = true and 
			dr.availability <> 0
            ')
            ->orderBy('dr.date', 'asc')
            ->setParameters(array(
                'today' => $today,
                'threeMonth' => $nextThreeMonth,
                'roomId' => $roomId
            ))
            ->getQuery()
            ->getResult();
    }

    public function findActiveRoomsByIdProperty($propertyId,$typeId)
    {
        return $this->createQueryBuilder('r')
            ->where('
                r.property = :propertyId and
                r.type = :typeId and
                r.is_active = true
                ')
            ->setParameters(
                [
                    'propertyId' => $propertyId,
                    'typeId' => $typeId,
                ]
            )
            ->getQuery()
            ->getResult();
    }
}