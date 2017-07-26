<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Repository\BedroomRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Room
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 25/08/15
 */

class DbBedroomRepository extends EntityRepository implements BedroomRepository
{

}