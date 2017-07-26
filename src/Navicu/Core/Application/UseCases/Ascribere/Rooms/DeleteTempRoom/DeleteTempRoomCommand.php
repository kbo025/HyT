<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\DeleteTempRoom;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class DeleteTempRoomCommand extends CommandBase implements Command
{
    /**
    * slug del establecimiento
    */
    protected $slug;

    /**
    * indice de la habitacion a eliminar
    */    
    protected $index;

    /**
    * indica si el usuario es admin
    */
    protected $is_admin;
}