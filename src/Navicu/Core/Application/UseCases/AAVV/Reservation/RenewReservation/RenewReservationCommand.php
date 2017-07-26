<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\RenewReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* comando renovar reservacion bloqueada
 *
* @author Gabriel Camacho  <kbo025@gmail.com>
*/
class RenewReservationCommand Extends CommandBase implements Command
{
    protected $time;
}
