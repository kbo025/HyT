<?php

namespace Navicu\Core\Application\UseCases\Web\FormatFlightData;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class FormatFlightDataCommand extends CommandBase implements Command 
{
    /**
    * datos de la busqueda
    */
    protected $search;

    /**
    * servicio de consultas y reservas de boleteria
    */
    protected $flightService;

    /**
    * instancia del servicio que maneja la sesion
    */
    protected $sessionService;
}