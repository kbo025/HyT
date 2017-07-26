<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\InventoryLogs\SearchOfLogs;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Application\Services\SecurityACL;
use Navicu\Core\Domain\Adapter\CoreValidator;

/**
 * Clase  para el manejo de la peticion de los logs de un establecimiento
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SearchOfLogsHandler implements Handler
{
	/**
     * Esta función es usada  para el manejo de la peticion de los logs de un establecimiento
     * @param SearchOfLogs $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();

		if (is_null($request["session"]))
			return new ResponseCommandBus(400, 'Ok', null);

		if ($request["session"]->getOwnerProfile())
			if (!SecurityACL::isSlugOwner($request["slug"], $request["session"]->getOwnerProfile()))
				return new ResponseCommandBus(400, 'Bad Request');

		$objLogsOwner = $rf->get('LogsOwner')->findBySlug($request["slug"]);

		if (count($objLogsOwner) == 0) {
			return new ResponseCommandBus(400, 'Ok', null);
		}

		$response = array();
		for ($lg = 0; $lg < count($objLogsOwner); $lg++) {
			array_push($response, $objLogsOwner[$lg]->getArray());
		}

        return new ResponseCommandBus(201, 'Ok', $response);
	}
}