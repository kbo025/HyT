<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAffiliatesDetail;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Caso de uso para retornar el detalle de una agencia de viaje
 * dado un slug.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetAffiliatesDetailCommand extends CommandBase implements Command
{
    /**
     * @var string      Slug de una AAVV
     */
    protected $slug;

}
