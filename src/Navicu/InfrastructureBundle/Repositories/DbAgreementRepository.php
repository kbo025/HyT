<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Agreement;
use Navicu\Core\Domain\Repository\AgreementRepository;

/**
* se declaran los metodos y funciones que implementan
* el repositorio de la entidad Agreement
*
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
* @version 07/08/15
*/
class DbAgreementRepository extends EntityRepository 
	implements AgreementRepository
{
	
}