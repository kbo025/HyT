<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Repository\LocationTypeRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad LocationType
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 28/01/16
 */

class DbLocationTypeRepository extends EntityRepository implements LocationTypeRepository
{

}