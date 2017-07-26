<?php
namespace Navicu\Core\Application\UseCases\Admin\SetRoom;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso SetRoom (Creación y Edición de la habitacion de un establecimiento).
 *
 * Class SetRoomCommand
 * @package Navicu\Core\Application\UseCases\Admin\SetRoom
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SetRoomCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * @var Slug del establecimiento
     */
    private $room;

    /**
     * Constructor de la clase
     * @param $slug
     */
    public function __construct($data)
    {
        $this->slug = $data["slug"];
        $this->room =  $data["data"]["room"];
    }

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'room' => $this->room
        );
    }
}