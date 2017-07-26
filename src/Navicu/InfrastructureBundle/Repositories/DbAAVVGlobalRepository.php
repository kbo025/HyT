<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 15/11/16
 * Time: 01:54 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;


use Navicu\Core\Domain\Repository\AAVVGlobalRepository;

class DbAAVVGlobalRepository extends DbBaseRepository implements AAVVGlobalRepository
{
    public function getParameter($name)
    {
        return $this->createQueryBuilder('p')
            ->where('
                p.name = :name

                ')
            ->setParameters(
                array(
                    'name' => $name
                )
            )->getQuery()->getOneOrNullResult()->getValue();
    }
}