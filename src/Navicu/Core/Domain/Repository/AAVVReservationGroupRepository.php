<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 15/09/16
 * Time: 03:06 PM
 */

namespace Navicu\Core\Domain\Repository;


interface AAVVReservationGroupRepository
{
    /**
     * @param $reservationGroup
     */
    public function save($reservationGroup);

    public function findOneByArray($array);

    public function findAllObjects();
}