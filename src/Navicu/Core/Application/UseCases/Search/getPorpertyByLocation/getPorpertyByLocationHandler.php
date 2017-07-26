<?php
namespace Navicu\Core\Application\UseCases\Search\getPorpertyByLocation;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SearchEngineService;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Application\Services\RateExteriorCalculator;

/**
 * Metodo que hace uso del motor de busqueda para generar un resultado
 * de destinos + hoteles dependiendo de las preferencias del usuario.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getPorpertyByLocationHandler implements Handler
{
	/**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{

		$request = $command->getRequest();
        $rpLocation = $rf->get('Location');
        $SERepository = $rf->get('SERepository');

        // Busqueda del destino.
        $destiny = $rpLocation->findOneByCountrySlugType(
            $request['countryCode'],
            $request['slug'],
            $request['type']
        );

		if (!$destiny)
			return new ResponseCommandBus(400, 'OK',"Destiny No Found");

		$request["idDestiny"] = $destiny->getId();
		$request["lvlDestiny"] = $destiny->getlvl();

		// Manejo de restricción para la busqueda de hoteles que acepten niños.
		$request["acceptsChild"] = $request["kid"] > 0 ? " and child = 1" : null;

		// Busqueda de los establecimientos dado un Destino.
		//
		// @param $request["alfa"]			Destino ALFA
		// @param $request["lvlDestiny"]	Nivel del destino
		// @param $request["idDestiny"]		Id del destino
		// @param $request["acceptsChild"]  Restricción para la busqueda de niño
		// @return Array
		$request["properties"] = $SERepository->resultPropertiesByDestiny($request);

        if ($request['nullDate']) {
            $response = $this->searchByDateNull($request, $rf);
        } else {
            $response = $this->searchByDate($request, $rf);
        }

		$response['citySelected'] = $destiny->getTitle();
		return new ResponseCommandBus(200, 'OK', $response);
	}

    public function searchByDateNull($request, $rf)
    {
		$searchService = new SearchEngineService($rf);
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

		$roomsTypes = [];
		$propertiesTypes = [];
		foreach ($result as &$property) {

			$idProperty = $property["idProperty"];
			$propertyEntity = $rf->get("Property")->find($idProperty);

			$packages = array_reduce(array_column($property["rooms"], "packages"), 'array_merge', array());

			$minSellRate = array_diff(array_column($packages,"sellrate"),[0]);
			$minSellRate= !empty($minSellRate) ? min($minSellRate) : 0;
			$maxSellRate = array_diff(array_column($packages,"priceExpensive"),[0]);
			$maxSellRate = !empty($maxSellRate) ? max($maxSellRate) : 0;
            $promotion = in_array('t',array_column($packages, "promotion"));

			/**
			 * Busqueda de aumento minimo y maximo por niño para una habitación.
			 */
			if ($request["kid"] > 0) {
				$minAmountKid = [];
				$maxAmountKid = [];
				foreach ($packages as $pack) {
					if ($pack["sellrate"] == $minSellRate)
						array_push($minAmountKid, $pack["amountkid"]);
					if ($pack["priceExpensive"] == $maxSellRate)
						array_push($maxAmountKid, $pack["amountkid"]);
				}
				$minAmountKid = min($minAmountKid);
				$maxAmountKid = max($maxAmountKid);

				if ($pack["variationtypekids"] == 1) {
					$minSellRate = $minSellRate + ($minSellRate * $minAmountKid);
					$maxSellRate = $maxSellRate + ($maxSellRate * $maxAmountKid);
				}else {
					$minSellRate = $minSellRate + $minAmountKid;
					$maxSellRate = $maxSellRate +  $maxAmountKid;
				}
			}

			$minSellRate = RateCalculator::calculateClientRate($minSellRate, $propertyEntity);
			$maxSellRate = RateCalculator::calculateClientRate($maxSellRate, $propertyEntity);

			$maxSellRate = $minSellRate < $maxSellRate ?	$maxSellRate : $maxSellRate * 1.13;

			$minSellRate = RateExteriorCalculator::calculateRateChange($minSellRate);
			$maxSellRate = RateExteriorCalculator::calculateRateChange($maxSellRate);

			$properties[$idProperty]["sellRate"] = $minSellRate;
			$properties[$idProperty]["priceExpensive"] = $maxSellRate;
            $properties[$idProperty]["promotion"] = $promotion;

			$properties[$idProperty]["idProperty"] = $idProperty;
			array_push($propertiesTypes, $property["idTypeProperty"]);
			array_push($roomsTypes, array_column($property["rooms"], "idTypeRoom"));

		}
		// Tipos de establecimientos encontrados
		$response["propertiesTypes"] = $propertiesTypes;
        $searchService->propertyTypes($response["propertiesTypes"]);
		// Tipos de habitaciones encontradas
		$response["roomsTypes"] = array_unique(array_reduce($roomsTypes, 'array_merge', array()));
		// Cantidad de establecimientos Encontrados
		$response["propertiesAmount"] = count($properties);
        $searchService->roomTypes($response["roomsTypes"]);
		// Manejo de paginación con los establecimientos encontrados.
        $searchService->pagination($response, $request, $properties);

        return $response;
    }

    public function searchByDate($request, $rf)
    {
		$searchService = new SearchEngineService($rf);
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

		$roomsTypes = [];
		$propertiesTypes = [];
		foreach ($result as &$property) {

			$idProperty = $property["idProperty"];
			$propertyEntity = $rf->get("Property")->find($idProperty);

			$totalAvailability = array_sum(array_column($property["rooms"], "availability"));
			$maxCapacityPeople = array_sum(array_column($property["rooms"], "maxPeople"));

			// Numero total de habitaciones disponibilidad de un establecimiento
			// contra el numero de habitaciones de la petición.
			if ($request["room"] < $totalAvailability || $request["adult"] <= $maxCapacityPeople) {

				$packages = array_reduce(array_column($property["rooms"], "packages"), 'array_merge', array());
				$minSellRate = min(array_column($packages,"sellrate"));
				$maxSellRate = max(array_column($packages,"sellrate"));
                $promotion = in_array(true,array_column($packages, "promotion"));

				/**
				 * Busqueda de aumento minimo y maximo por niño para una habitación.
				 */
				if ($request["kid"] > 0) {
					$minAmountKid = [];
					$maxAmountKid = [];
					foreach ($packages as $pack) {
						if ($pack["sellrate"] == $minSellRate)
							array_push($minAmountKid, $pack["amountkid"]);
						if ($pack["sellrate"] == $maxSellRate)
							array_push($maxAmountKid, $pack["amountkid"]);
					}
					$minAmountKid = min($minAmountKid);
					$maxAmountKid = max($maxAmountKid);

					if ($pack["variationtypekids"] == 1) {
						$minSellRate = $minSellRate + ($minSellRate * $minAmountKid);
						$maxSellRate = $maxSellRate + ($maxSellRate * $maxAmountKid);
					}else {
						$minSellRate = $minSellRate + $minAmountKid;
						$maxSellRate = $maxSellRate +  $maxAmountKid;
					}
				}

				$minSellRate = RateCalculator::calculateClientRate($minSellRate, $propertyEntity);
				$maxSellRate = RateCalculator::calculateClientRate($maxSellRate, $propertyEntity);

				$maxSellRate = $minSellRate < $maxSellRate ?	$maxSellRate : $maxSellRate * 1.13;

				$minSellRate = RateExteriorCalculator::calculateRateChange($minSellRate);
				$maxSellRate = RateExteriorCalculator::calculateRateChange($maxSellRate);

				$properties[$idProperty]["sellRate"] = $minSellRate;
				$properties[$idProperty]["priceExpensive"] = $maxSellRate;
				$properties[$idProperty]["idProperty"] = $idProperty;
                $properties[$idProperty]["promotion"] = $promotion;
				array_push($propertiesTypes, $property["idTypeProperty"]);
				array_push($roomsTypes, array_column($property["rooms"], "idTypeRoom"));
			}
		}

		// Tipos de establecimientos encontrados
		$response["propertiesTypes"] = $propertiesTypes;
        $searchService->propertyTypes($response["propertiesTypes"]);
		// Tipos de habitaciones encontradas
		$response["roomsTypes"] = array_unique(array_reduce($roomsTypes, 'array_merge', array()));
		// Cantidad de establecimientos Encontrados
		$response["propertiesAmount"] = count($properties);
        $searchService->roomTypes($response["roomsTypes"]);

		// Manejo de paginación con los establecimientos encontrados.
        $searchService->pagination($response, $request, $properties);
        return $response;
    }
}
