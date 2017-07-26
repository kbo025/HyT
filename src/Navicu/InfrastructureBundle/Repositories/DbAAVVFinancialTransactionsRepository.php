<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions;
use Navicu\Core\Domain\Repository\AAVVFinancialTransactionsRepository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad AAVVFinancialTransactions
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class DbAAVVFinancialTransactionsRepository extends EntityRepository implements AAVVFinancialTransactionsRepository
{
    /**
     * Función para hacer la persistencia en la base de datos.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param AAVVFinancialTransactions $financialTransactions
     * @return Void
     */
    public function save($financialTransactions)
    {
        $this->getEntityManager()->persist($financialTransactions);
        $this->getEntityManager()->flush();
    }
}