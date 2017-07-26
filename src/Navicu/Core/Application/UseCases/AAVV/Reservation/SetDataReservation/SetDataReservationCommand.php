<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\SetDataReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Validar la disponibilidades de la reserva mediante AAVV
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 16/09/2016
 */
class SetDataReservationCommand extends CommandBase implements Command
{
    /**
     * @var array de los datos a validar en el inventario
     */
    protected $properties;

    /**
     * @var date fecha inicial de la reserva
     */
    protected $checkIn;

    /**
     * @var date fecha final de la reserva
     */
    protected $checkOut;

    /**
     * @var Localidad de la reserva
     */
    protected $location;

    /**
     * @var ip desde donde se hace la reserva
     */
    protected $ip;

    /**
     * @var reservation_type tipo de la reserva, 0 pre - 1 reserva
     */
    protected $reservation_type;
}
