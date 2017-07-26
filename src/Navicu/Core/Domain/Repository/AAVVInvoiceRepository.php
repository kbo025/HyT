<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVInvoiceRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 20/10/2016
 */

interface AAVVInvoiceRepository
{
    public function findExpiredInvoicesToDate();

    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);

    public function getInvoicesBySlugAndFilters($filters);

    public function findInvoicesNotExpired();

    public function findInvoicesExpired();
}

