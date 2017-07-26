<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 26/12/16
 * Time: 02:59 PM
 */

namespace Navicu\Core\Domain\Repository;


interface PaymentRepository
{
    public function updatePayments($paramToBuildSql);
}