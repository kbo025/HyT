<?php
namespace Navicu\Core\Application\UseCases\Admin\RemoveRoom;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso RemoveRoom (Eliminar una habitacion
 * de un establecimiento).
 *
 * Class RemoveRoomCommand
 * @package Navicu\Core\Application\UseCases\Admin\RemoveRoom
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RemoveRoomCommand implements Command
{
    /**
     * @var Id de la habitación
     */
    private $id;

    /**
     * Constructor de la clase
     * @param $data
     */
    public function __construct($data)
    {
        $this->id = $data["id"];
    }

    public function getRequest()
    {
        return
            array(
                'id' => $this->id
        );
    }
}