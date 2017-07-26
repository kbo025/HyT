<?php

namespace Navicu\Core\Application\UseCases\Ascribere\RegisterTempServices;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* comando 'Registrar Servicios en espacio Temporal'
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 08/06/2015
*/

class RegisterTempServicesCommand extends CommandBase implements Command
{
    /**
    * el slug del establecimiento temporal
    **/
    protected $slug;

    /**
    * Conjunto de servicios
    **/
    protected $services;

    /**
    * inidica si el usuario es admin
    **/
    protected $is_admin;
}