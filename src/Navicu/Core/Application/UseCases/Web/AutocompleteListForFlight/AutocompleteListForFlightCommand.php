<?php

namespace Navicu\Core\Application\UseCases\Web\AutocompleteListForFlight;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class AutocompleteListForFlightCommand extends CommandBase implements Command
{
    /**
     * palabra que servira de referencia para la busqueda
     */
    protected $word;

    /**
     * codigo iso de dos digitos que identifica al pais
     */
    protected $country;
}