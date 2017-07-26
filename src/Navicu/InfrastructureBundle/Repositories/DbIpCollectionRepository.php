<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\IpCollectionRepository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad IpCollection
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class DbIpCollectionRepository extends EntityRepository implements IpCollectionRepository
{
    /**
     * Esta función el pais con el rango al que pertenece
     * la IP enviada.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Integer $idRoom
     *
     * @return Object
     */
    public function findByIpRange($ip)
    {
        return $this->createQueryBuilder('ic')
            ->where('
                ic.ip_start <= :ip and
                ic.ip_end >= :ip
                ')
            ->setParameters([
                'ip' => $ip
            ])
            ->getQuery()->getOneOrNullResult();
    }
}
