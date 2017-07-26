<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad AAVVAdditionalQuota
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
interface AAVVAdditionalQuotaRepository
{
    public function findOneByArray($array);

    public function save($updateProfile);

    public function delete($updateProfile);

    public function persistObject($additionalQuota);

    public function flushObject();

    public function findAllObjects();
}