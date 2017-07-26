<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 18/11/16
 * Time: 03:06 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class DeleteParametersCommand extends CommandBase implements Command
{

    /**
     * Id del parametro a ser eliminado
     */
    protected $id;
}