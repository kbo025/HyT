<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AcceptTermsAndConditions;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class AcceptTermsAndConditionsCommand extends CommandBase implements Command
{
    /**
    * slug del establecimiento
    */
    protected $slug;

    /**
    * indica si los terminos y condiciones fueron aceptados
    */
    protected $accepted;

    /**
    * ip del cliente
    */
    protected $client_ip;

    /**
    * indica si el usuario es administrador
    */
    protected $is_admin;

    /**
    * taza de descuento asignada al establecimiento
    */
    protected $discount_rate;

    /**
    * dias de creditos asignados al establecimiento
    */
    protected $credit_days;

	public function __construct($data = null)
	{
        $this->is_admin = false;
        $this->discount_rate = 0.3;
        $this->credit_days = 30;
        Parent::setAtrributes($data);
	}
}