<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\Admin\GetDataAdmin;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando que obtiene los datos de un usuario admin
 * dado el id del usuario fos_user
 *
 * @author Freddy Contreras
 */
class GetDataAdminCommand extends CommandBase implements Command
{
    /**
     * @var integer id del usuario a buscar
     */
   protected $user_id;
}