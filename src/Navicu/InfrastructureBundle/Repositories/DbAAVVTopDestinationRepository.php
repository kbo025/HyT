<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 14/10/16
 * Time: 01:57 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;


use Navicu\Core\Domain\Repository\AAVVTopDestinationRepository;

class DbAAVVTopDestinationRepository extends DbBaseRepository implements AAVVTopDestinationRepository
{
    public function findAllObjects()
    {
        return $this->findAll();
    }

    public function persistObject($destiny)
    {
        $this->getEntityManager()->persist($destiny);
    }

    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Funcion encargada de listar los destinos mas buscados por la aavv
     *
     * @param string $aavvId, identificador de la aavv
     * @return array
     * @version 18/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function findTopDestinationOrderByDesc($aavvId)
    {
        return $this->createQueryBuilder('td')
            ->leftJoin('td.location','l')
            ->where('
            td.aavv = :aavvId
            ')
            ->orderBy('td.number_visits', 'desc')
            ->setParameters(array(
                'aavvId' => $aavvId
            ))
            ->getQuery()
            ->setMaxResults(5)
            ->getResult();
    }
}