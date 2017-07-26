<?php
namespace Navicu\Core\Application\UseCases\Admin\GetRooms;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso GetRooms (Obtener las habitaciones de un establecimiento).
 *
 * Class GetRoomsCommand
 * @package Navicu\Core\Application\UseCases\Admin\GetRooms
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 26/10/2015
 */
class GetRoomsCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * Constructor de la clase
     * @param $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug
        );
    }
}