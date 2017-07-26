<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\PaymentInfoProperty;
use Navicu\Core\Domain\Repository\PaymentInfoPropertyRepository;

/**
* se declaran los metodos y funciones que implementan
* el repositorio de la entidad PaymentInfoProperty
*
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
* @version 06/08/15
*/
class DbPaymentInfoPropertyRepository extends EntityRepository 
	implements PaymentInfoPropertyRepository
{
	public function save(PaymentInfoProperty $payment)
    {
		$this->getEntityManager()->persist($payment);
		$this->getEntityManager()->flush();
    }
}