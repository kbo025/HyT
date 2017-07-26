<?php
namespace Navicu\Core\Application\UseCases\AAVV\Billing\ChangeStatusInvoice;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando para el cambio de status de una factura
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 14-10-2016
 */
class ChangeStatusInvoiceCommand extends CommandBase implements Command
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
     * el status al cual se quiere cambiar
     */
    protected $status;
}