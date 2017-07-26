<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\UpdatePayment;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * comando para listado de pagos docimiciados a una agencia de viaje
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 10-11-2016
 */

class UpdatePaymentCommand extends CommandBase implements Command
{
    /**
     * el Id del pago
     */
    protected $paymentId;

    /**
     * el Id del pago
     */
    protected $type;

    /**
     * tipo de modificacion (actualizar referencia o cambiar estatus)
     */
    protected $modificationType;

    /**
     * numero de referencia a ser guardado
     */
    protected $reference;

    /**
     * nuevo estatus del pago
     */
    protected $status;
}