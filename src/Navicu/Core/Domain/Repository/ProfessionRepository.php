<?php
/**
 * Created by Isabel Nieto.
 * User: user03
 * Date: 26/04/16
 * Time: 11:01 AM
 */
namespace Navicu\Core\Domain\Repository;


/**
 * Interface ProfessionRepository
 * @package Navicu\Core\Domain\Repository
 */

interface ProfessionRepository
{
    public function findOneByArray($array);

    public function getAll();

    public function save($profession);
}