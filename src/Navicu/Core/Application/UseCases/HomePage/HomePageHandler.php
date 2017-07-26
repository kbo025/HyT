<?php
namespace Navicu\Core\Application\UseCases\Prueba;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;

class PruebaHandler implements Handler
{
	public function handle(Command $command, RepositoryFactoryInterface $rf)
	{
		$request = $command->getRequest();

		if ($request['text'] == 'Esto es una prueba del CommandBus') {
			return new ResponseCommandBus(200, 'Manejador ejecutado');
		} else {
			return new ResponseCommandBus(500, 'Ese no es el texto esperado');
		}
	}
}