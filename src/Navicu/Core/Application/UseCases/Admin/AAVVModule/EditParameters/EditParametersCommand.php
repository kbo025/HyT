<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\EditParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class EditParametersCommand extends CommandBase implements Command
{
    /**
     * Indica si el porcentaje de credito viene como un numero entero
     *o un porcentaje
     */
    protected $typeCredit;
    /**
     * Nuevo monto de la cuota de mantenimiento
     */
    protected $quotaMaintenance;
    /**
     * Nuevo monto de la cuota por licencia de usuario
     */
    protected $quotaLicence;
    /**
     * Nuevo monto de la cuota por correos personalizados
     */
    protected $quotaEmail;
    /**
     * Nuevo monto de la cuota de mantenimiento
     */
    protected $nextQuotaMaintenance;
    /**
     * Nuevo monto de la cuota por licencia de usuario
     */
    protected $nextQuotaLicence;
    /**
     * Nuevo monto de la cuota por correos personalizados
     */
    protected $nextQuotaEmail;
    /**
     * Nuevo monto de la cuota por correos personalizados
     */
    protected $nextDate;
    /**
     * Array de tipos de deposito a ser guardados, editados o eliminados
     */
    protected $depositTypes;
}