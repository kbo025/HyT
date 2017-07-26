<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad AAVVCreditNVC
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
interface AAVVCreditNVCRepository
{
    public function findOneByArray($array);

    public function save($obj);

    public function delete($obj);
}