<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\GetDataOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * GetDataOwner
 *
 * Comando que obtiene los datos de un usuario admin
 * dado el id del usuario fos_user
 *
 * @author Freddy Contreras
 */
class GetDataOwnerCommand extends CommandBase implements Command
{
    /**
     * @var integer id del usuario a buscar
     */
    protected $user_id;
}