<?php
namespace Navicu\Core\Application\UseCases\Web\getDataSiteMap;

use Navicu\Core\Application\Contract\Command;


/**
 * Comando para devolver el contenido del archivo siteMap.xml
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getDataSiteMapCommand implements Command
{
    /**
     * @var locations Arreglo de localidades con establecimiento dentro de la BD
     */
    private $locations;

    /**
     * @var Routing Manejo del servicio de symfony para el manejador de rutas
     */
    private $routing;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->locations = $data["locations"];
        $this->routing = $data["routing"];
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return [
            'locations' => $this->locations,
            'routing' => $this->routing
        ];
    }

}