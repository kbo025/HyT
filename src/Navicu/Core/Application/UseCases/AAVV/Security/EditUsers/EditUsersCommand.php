<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\EditUsers;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;

class EditUsersCommand extends CommandBase implements Command
{
    /**
     * Array de usuarios a editar
     */
    protected $users;

    protected $personalized_interface;

    protected $personalized_mail;
}