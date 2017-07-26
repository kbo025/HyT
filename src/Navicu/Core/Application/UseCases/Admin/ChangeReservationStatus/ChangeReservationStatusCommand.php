<?php
namespace Navicu\Core\Application\UseCases\Admin\ChangeReservationStatus;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso ChangeReservationStatus (Cambiar el estado de una reserva)
 * @author Jose Agraz <jaagraz@navicu.com>
 * @version 15/02/2016
 */

class ChangeReservationStatusCommand extends CommandBase implements Command
{

    protected $idReservation;

    protected $reservationStatus;

    protected $arrayTransferred;

    protected $description;
}