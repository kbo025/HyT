<?php
namespace Navicu\Core\Application\UseCases\Search\GetDestinyOfLocation;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando hace uso del motor de busqueda para generar una lista de
 * destinos para cliente.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetDestinyOfLocationCommand implements Command
{
    public function __construct()
    {

    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @return  Array
     */
    public function getRequest()
    {
        return [];
    }
}
