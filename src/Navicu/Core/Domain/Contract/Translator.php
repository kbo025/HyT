<?php 
namespace Navicu\Core\Domain\Contract;

/**
* La interfaz hace el uso de metodos para el traslator
* que se implentaran en los casos de usos.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
interface Translator
{
    /**
     * Metodo que hace uso del translator del framework.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param string $code
	 */
    public static function getTranslator($code);
}