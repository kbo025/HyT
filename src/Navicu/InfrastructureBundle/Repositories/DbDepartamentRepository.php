<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\DepartamentRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Departament
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 20/06/16
 */
class DbDepartamentRepository extends EntityRepository implements DepartamentRepository
{

}