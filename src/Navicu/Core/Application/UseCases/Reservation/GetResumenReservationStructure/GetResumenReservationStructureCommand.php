<?php
namespace Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class GetResumenReservationStructureCommand extends CommandBase implements Command
{
    /**
    *   id publico de la reserva
    *   @var string $id
    */
    protected $id;

    /**
     * indica si se muestra el pdf del hotelero o del cliente
     */
    protected $owner;

    /**
     *  indica si se debe enviar el email de reservacion al cliente o no
     *   @var boolean $sendMail
     */
    protected $sendEmail;

    /**
     *
     */
    protected $nationalCurrency;

    public function __construct( $id, $sendEmail = true, $owner = 0, $nationalCurrency = true )
    {
        $this->id = $id;
        $this->sendEmail = $sendEmail;
        $this->owner = $owner;
        $this->nationalCurrency = $nationalCurrency;
    }
}