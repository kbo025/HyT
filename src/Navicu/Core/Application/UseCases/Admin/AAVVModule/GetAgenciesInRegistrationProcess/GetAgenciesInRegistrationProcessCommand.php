<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAgenciesInRegistrationProcess;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


/**
 * @author Gabriel Camacho
 * @version 13-10-2016
 */
class GetAgenciesInRegistrationProcessCommand extends CommandBase implements Command
{
    /**
     * string usado como un filtro generico
     *
     * @var string $word
     */
    protected $word;


    /**
     * datos para el ordenamiento
     *
     * @var array $order
     */
    protected $order;
}