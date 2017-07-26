<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 25/10/16
 * Time: 03:16 PM
 */

namespace Navicu\Core\Domain\Repository;


interface AAVVBankPaymentsRepository
{
    public function findMovementsByAavv($aavvId, $initDate, $lastDate);
}