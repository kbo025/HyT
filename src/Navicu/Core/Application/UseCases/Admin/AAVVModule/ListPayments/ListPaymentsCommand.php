<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ListPayments;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * comando para listado de pagos docimiciados a una agencia de viaje
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 10-11-2016
 */
class ListPaymentsCommand extends CommandBase implements Command
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

    /**
     * datos para el ordenamiento
     *
     * @var array $order
     */
    protected $orderBy;
}