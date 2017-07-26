<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\GetDetailInvoice;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * obtiene los detalles de una factura.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 14-10-2016
 */
class GetDetailInvoiceCommand extends CommandBase implements Command
{
    /**
     * el slug de AAVV a la que pertenece
     */
    protected $slug;

    /**
     * id de la factura
     */
    protected $idInvoice;


    /**
    * indica si la solicitud se esta haciendo desde admin
    */
    protected $isAdmin;
} 