<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 26/04/16
 * Time: 11:40 AM
 */

namespace Navicu\Core\Domain\Repository;

/**
 * Interface DestinationsType
 * @package Navicu\Core\Domain\Repository
 */

interface DestinationsTypeRepository
{
    public function findOneByArray($array);

    public function getAll();

    public function save($profession);
}