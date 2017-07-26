<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 21/11/16
 * Time: 04:05 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\InvoicesAndServicesList;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class InvoicesAndServicesListCommand extends CommandBase implements Command
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