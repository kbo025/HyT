<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad AAVVFinancialTransactions
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
interface AAVVFinancialTransactionsRepository
{
    /**
     * Función para hacer la persistencia en la base de datos.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param FinancialTransactions $financialTransactions
     * @return Void
     */
    public function save($financialTransactions);

}