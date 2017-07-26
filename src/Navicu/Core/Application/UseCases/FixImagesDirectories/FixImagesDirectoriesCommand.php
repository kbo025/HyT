<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 05/04/17
 * Time: 10:08 AM
 */

namespace Navicu\Core\Application\UseCases\FixImagesDirectories;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class FixImagesDirectoriesCommand extends CommandBase implements Command
{
    protected $basePath;
}