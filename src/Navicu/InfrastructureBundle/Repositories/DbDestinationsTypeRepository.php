<?php
/**
 * Created by Isabel Nieto.
 * Date: 26/04/16
 * Time: 11:37 AM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\DestinationsTypeRepository;

/**
 * se declaran los metodos y funciones que implementan
 * el repositorio de la entidad DestinationsType
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DbDestinationsTypeRepository extends EntityRepository implements DestinationsTypeRepository
{
    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }

    public function getAll()
    {
        return $this->findAll();
    }

    public function save($destinationsType)
    {
        $this->getEntityManager()->persist($destinationsType);
        $this->getEntityManager()->flush();
    }
}