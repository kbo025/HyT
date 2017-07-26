<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 26/04/16
 * Time: 11:40 AM
 */

namespace Navicu\Core\Domain\Repository;


/**
 * Interface HobbiesRepository
 * @package Navicu\Core\Domain\Repository
 */

interface HobbiesRepository
{
    public function findOneByArray($array);

    public function getAll();

    public function save($hobbies);
}