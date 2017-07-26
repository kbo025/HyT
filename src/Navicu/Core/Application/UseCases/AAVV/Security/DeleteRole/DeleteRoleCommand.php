<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\DeleteRole;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;

class DeleteRoleCommand extends CommandBase implements Command
{
    /**
     * identificador de la agencia de viaje a la que se le hará
     */
    protected $id;
}