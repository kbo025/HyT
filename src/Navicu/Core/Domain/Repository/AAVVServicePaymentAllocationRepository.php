<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVServicePaymentAllocationRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 27/10/2016
 */

interface AAVVServicePaymentAllocationRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);
}