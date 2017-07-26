<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVProfileRepository
 * @author Mary sanchezs <msmarycarmen@gmail.com>
 * @version 28/05/2016
 */
interface NvcProfileRepository
{
    public function findOneByArray($array);

    public function save($updateProfile);
}