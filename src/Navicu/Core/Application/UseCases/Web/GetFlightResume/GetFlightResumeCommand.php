<?php

namespace Navicu\Core\Application\UseCases\Web\GetFlightResume;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class GetFlightResumeCommand extends CommandBase implements Command
{
    protected $publicId;
}