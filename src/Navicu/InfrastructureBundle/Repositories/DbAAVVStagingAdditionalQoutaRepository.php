<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\AAVVStagingAdditionalQuotaRepository;

class DbAAVVStagingAdditionalQoutaRepository extends DbBaseRepository implements AAVVStagingAdditionalQuotaRepository
{
    public function findQuotasToApply()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.valid_since = :date AND
                u.applied = false
                ')
            ->setParameters(
                array(
                    'date' => new \DateTime()
                )
            )->getQuery()->getResult();
    }

    public function findCurrentlyStaged()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.applied = false
                ')
            ->getQuery()->getResult();
    }

    public function deleteCurrentlyStaged($target = null)
    {
        $sql = 'u.applied = false';
        $parameters = [];

        if ($target) {
            $sql = $sql.' and u.targetid = :target';
            $parameters['target'] = $target;
        }

        return $this->createQueryBuilder('u')
            ->delete()
            ->where($sql)
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
    }

    public function findApplied()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.applied = true
                ')
            ->getQuery()->getResult();
    }
}