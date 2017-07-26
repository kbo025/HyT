<?php 

namespace Navicu\Core\Domain\Contract;

/**
* La interfaz hace el uso de metodos para el manejo de la entidad
* User que se implentaran en los casos de usos.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
interface User
{
    /**
     * Metodo que hace uso de la entidad user para el registro de usuario
     * dado un conjunto de datos.
     * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param object $obj
	 */
    public static function setUser($data);

    /**
     * Metodo que hace uso del repositoryFactory para la persistencia
     * de un objeto user.
     * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param object $obj
	 */
    public static function save($user, $rf);
}