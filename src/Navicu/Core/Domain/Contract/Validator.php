<?php 

namespace Navicu\Core\Domain\Contract;

/**
* La interfaz hace el uso de metodos para el validator
* que se implentaran en los casos de usos.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
interface Validator
{
    /**
     * Metodo que hace uso del validator del framework.
     * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param object $obj
	 */
    public static function getValidator($obj);
}