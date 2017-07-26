<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de AAVVStagingAdditionalQuotaRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 17/11/2016
 */

interface AAVVStagingAdditionalQuotaRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);
}