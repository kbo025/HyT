<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\SelectTempRoom;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class SelectTempRoomCommand extends CommandBase implements Command
{
    /**
    * slug del establecimiento
    */
    protected $slug;

    /**
    * indice de la habitacion a seleccionar en el array d ehabitaciones
    */
    protected $index;

    /**
    * indica si el usuario es administrador o no
    */
    protected $is_admin;
}