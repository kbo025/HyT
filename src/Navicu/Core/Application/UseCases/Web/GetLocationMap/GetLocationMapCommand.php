<?php

namespace Navicu\Core\Application\UseCases\Web\GetLocationMap;

use Navicu\Core\Application\Contract\Command;

/**
 * Clase para ejecutar el caso de uso GetLocationMapCommand, para el manejo de las
 * localidades en el mapa.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetLocationMapCommand implements Command
{
    /**
     * @var date $data		Criterio de busqueda.
     */
    private $data;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return array(
            'data'=>$this->data);
    }
}