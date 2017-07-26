<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\AAVVAdditionalQuotaRepository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad AAVVAdditionalQuota
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class DbAAVVAdditionalQuotaRepository extends DbBaseRepository implements AAVVAdditionalQuotaRepository
{

    public function findAllObjects()
    {
        return $this->findAll();
    }
}
