<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 09/09/16
 * Time: 06:10 PM
 */

namespace Navicu\Core\Domain\Repository;


interface AAVVAddressRepository
{
    public function save($updateAddress);

    public function persistObject($address);

    public function flushObject();

    public function getAllAddress();
}