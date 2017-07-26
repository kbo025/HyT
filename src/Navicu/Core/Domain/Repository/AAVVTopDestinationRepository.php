<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 14/10/16
 * Time: 01:56 PM
 */

namespace Navicu\Core\Domain\Repository;

/**
 * Interface AAVVTopDestiniesRepository
 * @package Navicu\Core\Domain\Repository
 */
interface AAVVTopDestinationRepository
{
    public function findOneByArray($array);

    public function findAllObjects();

    public function save($destiny);

    public function persistObject($destiny);

    public function flushObject();

    public function findTopDestinationOrderByDesc($aavvId);
}