<?php
namespace Navicu\Core\Domain\Repository;

/**
 *	@author Gabriel Camacho <kbo025@gmail.com>
 *	@author Currently Working: Juan Pablo Osorio V.
 */
interface BankTypeRepository
{
   /**
    *  busca un registro por su id
    *
    * @param string $id
    * @return Object
    */
    public function findById($id);
}
