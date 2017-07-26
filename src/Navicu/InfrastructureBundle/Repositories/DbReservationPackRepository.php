<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ReservationPackRepository;

/**
 * La clase se declaran los métodos y funciones que implementan
 * el repositorio de la entidad ReservationPack
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 20/08/15
 */
class DbReservationPackRepository extends DbBaseRepository
    implements ReservationPackRepository
{

}