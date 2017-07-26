<?php

namespace Navicu\Core\Application\UseCases\Web\getDestinationsFooter;

use Navicu\Core\Application\Contract\Command;

/**
 * Clase para ejecutar el caso de uso getDestinationsFooterCommand, para el manejo del
 * listado de destinos habilitados por los establecimiento dentro de la BD.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getDestinationsFooterCommand implements Command
{
    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data = null)
    {
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return [];
    }
}