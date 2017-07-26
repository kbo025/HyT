<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de NVCSequenceRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 27/10/2016
 */

interface NVCSequenceRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);
}