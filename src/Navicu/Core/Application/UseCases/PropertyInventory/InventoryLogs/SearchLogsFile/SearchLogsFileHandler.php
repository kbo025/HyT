<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\InventoryLogs\SearchLogsFile;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Clase para el manejo de la información contenida en un archivo logs
 * del establecimiento.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SearchLogsFileHandler implements Handler
{
	/**
     * Esta función es usada para el manejo de la información contenida en un archivo logs
	 * del establecimiento.
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$logsOwner = $rf->get('LogsOwner')->findByFileName($request["logFile"]);

		if (!$logsOwner) {
			return new ResponseCommandBus(400, 'Ok', null);
		}

		$reponse = $logsOwner->getArray();
		$reponse += $logsOwner->getOpenFile();

        return new ResponseCommandBus(201, 'Ok', $reponse);
	}
}