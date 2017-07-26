<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\DropDailyRepository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad DropDaily
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class DbDropDailyRepository extends DbBaseRepository implements DropDailyRepository
{

    public function persistObject($dropDaily)
    {
        $this->getEntityManager()->persist($dropDaily);
    }

    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }
}
