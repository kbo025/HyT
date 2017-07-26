<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AdvanceSection;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class AdvanceSectionCommand extends CommandBase implements Command
{
    /**
    * slug del establecimiento
    */
    protected $slug;

    /**
    * inidica si el usuario es admin
    */
    protected $is_admin;
}