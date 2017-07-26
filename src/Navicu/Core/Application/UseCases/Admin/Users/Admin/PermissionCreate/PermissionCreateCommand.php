<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Admin\PermissionCreate;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;
/**
 * Comando es usado para listar los usuarios del sistema
 * dado un conjunto de parametros de busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PermissionCreateCommand extends CommandBase implements Command
{
    /**
     * @var string          Tipo de lista usuario
     */
    protected  $roleName;

}