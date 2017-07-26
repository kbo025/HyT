<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Repository\PackRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Pack
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbPackRepository extends EntityRepository implements PackRepository
{
    /**
     * Almacena en BD toda la información referente al Pack
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Pack
     */
    public function save(Pack $pack)
    {
        $this->getEntityManager()->persist($pack);
        $this->getEntityManager()->flush();
    }

    /**
     * Función para retornar un Pack dado un id.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Integer $id
     * @return Object Pack
     */
    public function findById($id)
    {
        $response = $this->find($id);
        return $response ? $response : null;
    }

    /**
     * Funcion que retorna a partir del IdPack que se consulta, la fecha en la cual posee dailys con
     * disponibilidad
     *
     * @author Isabel Nieto. <isabelcnd@gmail.com>
     * @author Currently Working: Isabel Nieto
     * 
     * @param object $today fecha actual que se hace la consulta
     * @param object $nextThreeMonth fecha actual mas 3 meses desde que se hizo la consulta
     * @param object $packId id del pack que se esta consultado en la fecha dada
     * @return array con las fechas en las que el pack tiene dailyPacks
     */
    public function findDailyPacksAvailableGivenDateRange($today, $nextThreeMonth, $packId)
    {
        return $this->createQueryBuilder('p')
            ->select('dp.date')
            ->join('p.daily_packages', 'dp')
            ->where('
            p.id = :packId and
            dp.date >= :today and 
            dp.date < :threeMonth and
            dp.is_completed = true and 
			dp.specific_availability <> 0
            ')
            ->orderBy('dp.date', 'asc')
            ->setParameters(array(
                'today' => $today,
                'threeMonth' => $nextThreeMonth,
                'packId' => $packId
            ))
            ->getQuery()
            ->getResult();
    }
}