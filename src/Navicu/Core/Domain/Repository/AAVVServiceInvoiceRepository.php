<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVServiceInvoiceRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 27/10/2016
 */

interface AAVVServiceInvoiceRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);

    public function findServiceInvoiceNotExpired();

    public function findServiceInvoiceExpired();
}