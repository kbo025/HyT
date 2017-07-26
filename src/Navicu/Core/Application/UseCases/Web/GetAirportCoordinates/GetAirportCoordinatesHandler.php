<?php

namespace Navicu\Core\Application\UseCases\Web\GetAirportCoordinates;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetAirportCoordinatesHandler implements Handler {

	/**
	 *  Ejecuta las tareas solicitadas
	 *
	 * @param Command $command
	 * @param RepositoryFactoryInterface $rf
	 *
	 * @return ResponseCommandBus
	 */
	public function handle( Command $command, RepositoryFactoryInterface $rf ) {

		$airportRepo = $rf->get('Airport');

		$airports = $airportRepo->findByLocation(1);

		$response = array();

		foreach ($airports as $airport) {
			$structure = array();

			$structure['name'] = $airport->getName();
			$temp = array();
			$temp['latitude'] = $airport->getLat();
			$temp['longitude'] = $airport->getLon();
			$structure['coords'] = $temp;

			$response[] = $structure;
		}

		return new ResponseCommandBus(200, 'OK', $response);
	}
}