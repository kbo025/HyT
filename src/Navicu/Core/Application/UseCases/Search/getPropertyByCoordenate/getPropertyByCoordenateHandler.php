<?php
namespace Navicu\Core\Application\UseCases\Search\getPropertyByCoordenate;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SearchEngineService;
use Navicu\Core\Application\Services\RateExteriorCalculator;

/**
 * Comando hace uso del motor de busqueda para generar un resultado
 * hoteles dependiendo de unas serie de coordenadas.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getPropertyByCoordenateHandler implements Handler
{
	/**
     * Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$coordenate = str_replace(["}","{"], "", $request["perimeter"]);
		$searchEngine = $rf->get('SERepository');
		$request["properties"] = $searchEngine->propertiesByCoordenate($coordenate);

		if ($request['nullDate']) {
			$response = $this->searchByDateNull($request, $rf);
		} else {
			$response = $this->searchByDate($request, $rf);
		}

//		$response['citySelected'] = $destiny->getTitle();

		return new ResponseCommandBus(200, 'OK', $response);
	}

	public function searchByDateNull($request, $rf)
	{
		$searchService = new SearchEngineService($rf);
		$resp = [];
		new RateExteriorCalculator($rf, null, date("Y-m-d 00:00:00", $request["startDate"]));

		// Busqueda de información (rooms, Packages, etc..)
		// de un conjunto de estableimientos dentro de motor
		// de busqueda dado un establecimiento.
		//
		// @param $request["properties"]	Establemientos
		// @param $request					Otros Parametros
		// @return Array
		$result = $searchService->propertyByDateNull($request);

		if (!$result)
			return null;

		foreach ($result as &$property) {

			$idProperty = $property["idProperty"];

			$packages = array_reduce(array_column($property["rooms"], "packages"), 'array_merge', array());

			$sellrates = array_column($packages,"sellrate");
			$minSellRate = [];
			foreach ($sellrates as $sellrate) {
				if ($sellrate != 0)
					array_push($minSellRate, $sellrate);
			}

			$sellrates = array_column($packages,"priceExpensive");

			if (empty($minSellRate)) {
				$minS = 0;
				$maxS = 0;
			} else {
				$minS = min($minSellRate);
				$maxS = (min($minSellRate) < max($minSellRate) ? max($minSellRate) : min($minSellRate)* 1.2);
				if (!$property['includeiva'])
					$minS = $minS * (1 + $property['taxRate']);
				$minS = RateExteriorCalculator::calculateRateChange($minS);
				$maxS = RateExteriorCalculator::calculateRateChange($maxS);

			}

			if ($minS != 0) {
				$properties[$idProperty]["minSellRate"] = $minS;
				$properties[$idProperty]["priceExpensive"] = $maxS;

				$propertyRepository = $rf->get('Property')->find($idProperty);
				$properties[$idProperty]["name"] = $propertyRepository->getName();
				$properties[$idProperty]["slug"] = $propertyRepository->getSlug();
				$properties[$idProperty]['coords']["isLocation"] = false;
				$properties[$idProperty]['coords']["latitude"] = $property["latitude"];
				$properties[$idProperty]['coords']["longitude"] = $property["longitude"];
				array_push($resp, $properties[$idProperty]);
			}

		}

		return $resp;
	}

	public function searchByDate($request, $rf)
	{
		$searchService = new SearchEngineService($rf);
		$resp = [];
		new RateExteriorCalculator($rf, null, date("Y-m-d 00:00:00", $request["startDate"]));

		// Busqueda de información (rooms, Packages, etc..)
		// de un conjunto de estableimientos dentro de motor
		// de busqueda dado un establecimiento.
		//
		// @param $request["properties"]	Establemientos
		// @param $request					Otros Parametros
		// @return Array
		$result = $searchService->propertyByDate($request);

		if (!$result)
			return null;

		foreach ($result as &$property) {

			$idProperty = $property["idProperty"];

			$totalAvailability = array_sum(array_column($property["rooms"], "availability"));
			$maxCapacityPeople = array_sum(array_column($property["rooms"], "maxPeople"));

			// Numero total de habitaciones disponibilidad de un establecimiento
			// contra el numero de habitaciones de la petición.
			if ($request["room"] < $totalAvailability || $request["adult"] <= $maxCapacityPeople) {

				$packages = array_reduce(array_column($property["rooms"], "packages"), 'array_merge', array());

				$minS = (int)min(array_column($packages,"sellrate"));
				$maxS = min(array_column($packages,"sellrate")) < max(array_column($packages,"sellrate")) ?
						max(array_column($packages,"sellrate")) : max(array_column($packages,"sellrate")) * 1.12;

				if ($minS != 0) {
					$properties[$idProperty]["minSellRate"] = RateExteriorCalculator::calculateRateChange($minS);
					$properties[$idProperty]["priceExpensive"] = RateExteriorCalculator::calculateRateChange($maxS);

					$propertyRepository = $rf->get('Property')->find($idProperty);
					$properties[$idProperty]["name"] = $propertyRepository->getName();
					$properties[$idProperty]["slug"] = $propertyRepository->getSlug();
					$properties[$idProperty]['coords']["isLocation"] = false;
					$properties[$idProperty]['coords']["latitude"] = $property["latitude"];
					$properties[$idProperty]['coords']["longitude"] = $property["longitude"];
					array_push($resp, $properties[$idProperty]);
				}
			}
		}

		return $resp;
	}
}
