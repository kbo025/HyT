<?php

namespace Navicu\Core\Application\UseCases\Reservation\ReservationForm;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Commando de 'Formulario de reserva de un establecimiento'
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 24/08/2015
 */

class PropertyReservationFormCommand extends CommandBase implements Command
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var DateTime
     */
    protected $checkIn;

    /**
     * @var DateTime
     */
    protected $checkOut;

    /**
     * @var integer
     */
    protected $numberAdults;

    /**
     * @var integer
     */
    protected $numberChild;

    /**
     * @var string
     */
    protected $alphaCurrency;

    /**
     * @var array
     */
    protected $rooms;

    /**
     * @var array
     */
    protected $client;

    /**
     * @var string
     */
    protected $clientIp;
}