<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\AavvLogRepository;


class DbAavvLogRepository extends EntityRepository implements AavvLogRepository
{
    public function save($log)
    {
        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();
    }

    public function findLogs($id)
    {
        return $this->createQueryBuilder('u')
            ->where('
					u.aavv_id = :id OR
					u.entity_id = :id
				')
            ->setParameters(
                array(
                    'id' => $id
                )
            )->getQuery()->getResult();


    }

    public function getLogHeaders($id)
    {
        $sql = "select to_char(date::DATE,'DD-MM-YYYY') as date, user_id,
  (select fullname from aavv_profile where id = (select aavv_profile_id from fos_user where id = aavvlogs.user_id )) as user,
  entity as module from aavvlogs where (aavv_id = :id or (entity_id = :id and entity = 'AAVV'))
  AND (select fullname from aavv_profile where id = (select aavv_profile_id from fos_user where id = aavvlogs.user_id )) is not null
   GROUP BY date::DATE, user_id, entity, user";

        $params['id'] = $id;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getLogDetails($date, $user_id, $entity, $aavv_id)
    {
        $sql = "
            select date::TIME ,type , field, oldvalue, newvalue from aavvlogs
            where date_trunc('day', date) = :date and user_id = :user_id 
            and entity = :entity and (aavv_id = :aavv_id or entity_id = :aavv_id);
        ";

        $params['date'] = $date;
        $params['user_id'] = $user_id;
        $params['entity'] = $entity;
        $params['aavv_id'] = $aavv_id;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}