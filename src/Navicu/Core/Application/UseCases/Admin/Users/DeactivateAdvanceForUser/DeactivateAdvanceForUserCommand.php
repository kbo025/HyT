<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\DeactivateAdvanceForUser;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando para el cambio de status de una factura
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 23-11-2016
 */
class DeactivateAdvanceForUserCommand extends CommandBase implements Command
{

    /**
     * id del usuario al cual se le eliminará disponibilidad
     */
    protected $id;      

    /**
    * indica si se activara o se desactrivara
    */
    protected $deactivate;

    /**
     * id del usuario que solicitó desactivar la antelación para un usuario
     */
    protected $deactivateBy;

    /**
    * motivo de la desactivación
    */
    protected $reason;
}