<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\SlugSecurity;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Application\Services\SecurityACL;

/**
 * Clase para el manejo de la seguridad por el uso de slug.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SlugSecurityHandler implements Handler
{
	/**
     * Esta función es usada para el manejo de la seguridad por el uso de slug.
	 *
     * @param SlugSecurity $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
        $ownerDaily = SecurityACL::isSlugOwner($request["slug"], $request["session"]);
		
		return $ownerDaily;
	}
}