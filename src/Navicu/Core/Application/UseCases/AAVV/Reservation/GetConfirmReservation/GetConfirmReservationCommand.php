<?php

namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetConfirmReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class GetConfirmReservationCommand extends CommandBase implements Command
{

//    protected $aavv;

    protected $public_group_id;

    protected $location;

    protected $hash_url;

    protected $owner;
}