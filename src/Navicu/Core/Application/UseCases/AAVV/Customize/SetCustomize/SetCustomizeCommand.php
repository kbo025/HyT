<?php
namespace Navicu\Core\Application\UseCases\AAVV\Customize\SetCustomize;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando de edición de colores personalizados
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/09/2016
 */
class SetCustomizeCommand extends CommandBase implements Command
{
    /**
     * @var array representa los datos a editar
     */
    protected $data;

}
