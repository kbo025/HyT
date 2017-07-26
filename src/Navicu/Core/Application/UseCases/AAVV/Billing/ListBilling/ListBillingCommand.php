<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\ListBilling;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * comando para listado con filtros y ordenamiento de las facturas
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 14-10-2016
 */
class ListBillingCommand extends CommandBase implements Command
{
    /**
     * slug del aavv que se quiere consultar
     */
    protected $slug;

    /**
     * palabra usada para los filtros del listado
     */
    protected $word;
}