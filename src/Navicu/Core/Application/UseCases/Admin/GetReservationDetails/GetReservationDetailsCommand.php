<?php
namespace Navicu\Core\Application\UseCases\Admin\GetReservationDetails;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso GetDetailsReservation (Obtener detalles de una reserva)
 *
 * Class GetDetailsReservationCommand
 * @package Navicu\Core\Application\UseCases\Admin\GetDetailsReservation
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 27/10/2015
 */
class GetReservationDetailsCommand extends CommandBase implements Command
{
    
    /**
     * @var id publico de la reserva
     */
    protected $publicId;

    /**
     * @var id publico de la reserva
     */
    protected $userSession;

    /**
     * indica si el usuario que intenta acceder al detalle de la reservca es el hotelero o es admin
     */
    protected $owner;
}