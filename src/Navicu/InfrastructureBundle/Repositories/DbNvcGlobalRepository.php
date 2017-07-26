<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 21/12/16
 * Time: 04:18 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\NvcGlobalRepository;

class DbNvcGlobalRepository extends DbBaseRepository implements NvcGlobalRepository
{
    /**
     * Funcion encargada de devolver el valor del campo solicitado
     *
     * @param $name
     * @return array con el value solicitado
     */
    public function findOneByName($name) {
        return $this->createQueryBuilder('nG')
            ->select('nG.value')
            ->where('nG.name = :nameToFind')
            ->setParameters(['nameToFind' => $name])
            ->getQuery()
            ->getArrayResult();
    }
}