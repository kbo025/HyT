<?php

namespace Navicu\Core\Application\UseCases\AAVV\Security\DeleteUser;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;
class DeleteUserCommand extends CommandBase implements Command
{
    /**
     * identificador del usuario a ser eliminado
     */
    protected $id;
}