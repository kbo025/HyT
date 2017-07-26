<?php
namespace Navicu\Core\Application\UseCases\Admin\GetRoomData;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso GetRoomData (Obtener la información de una habitacion
 * de un establecimiento).
 *
 * Class GetRoomDataCommand
 * @package Navicu\Core\Application\UseCases\Admin\GetRoomData
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetRoomDataCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

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
        $this->slug = $data["slug"];
        $this->id = $data["id"];
    }

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'id' => $this->id
        );
    }
}