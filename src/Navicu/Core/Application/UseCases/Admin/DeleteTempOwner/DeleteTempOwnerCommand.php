<?php
namespace Navicu\Core\Application\UseCases\Admin\DeleteTempOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;
/**
* Comando 'Eliminar un establecimiento en proceso de registro'
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 22/12/2015
     */

class DeleteTempOwnerCommand extends CommandBase implements Command
{

    /**
    * La variable representa el slug del establecimiento temporal
    */
    protected $id;
}