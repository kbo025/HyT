<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 15/09/16
 * Time: 03:09 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\AAVVReservationGroupRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class DbAAVVReservationGroupRepository extends DbBaseRepository implements AAVVReservationGroupRepository
{
    public function findAllObjects()
    {
        return $this->findAll();
    }

    public function findWithPendingEmails($time)
    {

        $sql = 'SELECT r.* FROM reservation_group r 
                WHERE (abs(EXTRACT(EPOCH FROM (r.createdat - current_timestamp)))/60 > ?) AND r.status = 0';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('NavicuDomain:AAVVReservationGroup', 'u');

        $q = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $q->setParameter(1, $time);

        return $q->getResult();
    }
}