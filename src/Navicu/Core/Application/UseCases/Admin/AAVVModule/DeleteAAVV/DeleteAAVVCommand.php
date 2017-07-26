<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteAAVV;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class DeleteAAVVCommand extends CommandBase implements Command
{
    /**
     * slug de la agencia de viaje que se desea eliminar
     */
    protected $slug;
}
