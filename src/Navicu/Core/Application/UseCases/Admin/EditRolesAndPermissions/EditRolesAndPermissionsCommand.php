<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 13/02/17
 * Time: 01:28 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\EditRolesAndPermissions;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class EditRolesAndPermissionsCommand extends CommandBase implements Command
{
    protected $rolId;

    protected $permissionId;

    protected $value;
}