<?php

namespace Navicu\Core\Domain\Repository;


/**
 * Interfaz de AAVVRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 06/09/2016
 */

interface AAVVRepository
{
    public function findOneByArray($array);

    public function findAllObjects();

    public function save($updateProfile);

    public function persistObject($aavv);

    public function flushObject();
}