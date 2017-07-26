<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVInvoicePaymentsRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 03/11/2016
 */

interface AAVVInvoicePaymentsRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);
}