<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\EditRolePermissions;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;

class EditRolePermissionsCommand extends CommandBase implements Command
{
    /**
     * identificador del rol a ser eliminado
     */
    protected $id;


    /**
     * Array que contiene el nuevo estado de los permisos del rol en cada modulo
     */
    protected $permissions;
}