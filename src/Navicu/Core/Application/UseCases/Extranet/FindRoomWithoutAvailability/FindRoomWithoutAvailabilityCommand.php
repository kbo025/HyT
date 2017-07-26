<?php
namespace Navicu\Core\Application\UseCases\Extranet\FindRoomWithoutAvailability;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando 'Obtener habitacion sin disponibilidad'
 * @author Jose Agraz <jaagraz@navicu.com>
 * @version 08/01/2016
 */

class FindRoomWithoutAvailabilityCommand implements Command
{


    /**
     * La variable representa el slug del establecimiento
     */
    protected $slug;


    /**
     *	Constructor de la clase
     *
     * 	@param string $word Palabra
     */
    public function __construct($slug = null)
    {

        $this->slug=$slug;
    }


    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return array(
            'slug'=>$this->slug

        );
    }


    /**
     * Devuelve el slug del establecimiento.
     *
     * @return Integer
     */

    public function getSlug()
    {

        return $this->slug;
    }

}