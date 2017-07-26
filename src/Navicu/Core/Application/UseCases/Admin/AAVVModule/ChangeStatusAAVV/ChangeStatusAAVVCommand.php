<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ChangeStatusAAVV;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class ChangeStatusAAVVCommand extends CommandBase implements Command
{
    /**
     * slug de la agencia de viaje que se desea eliminar
     */
    protected $slug;

    /**
    * estado al que se quiere cambiar la agencia de viaje
    * 3: inactivo
    * 2: activo 
    */
    protected $status;
}