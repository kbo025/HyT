<?php

namespace Navicu\Core\Application\UseCases\AAVV\Logs\GetAavvLogDetails;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;


class GetAavvLogDetailsCommand extends CommandBase implements Command
{
    protected $date;

    protected $user_id;

    protected $module;

    protected $aavv_id;
}