<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\SubdomainRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Subdomain
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 04/10/16
 */

class DbSubdomainRepository extends EntityRepository implements
    SubdomainRepository
{
    public function findBySlugUser($slug, $user)
    {
        return $this->createQueryBuilder('d')
            ->join('d.users', 'user')
            ->where('
                    d.slug = :slug and
                    user.id = :user
                ')
            ->setParameters([
                'slug' => $slug,
                'user' => $user
            ])
            ->getQuery()->getResult();
    }
}