<?php
namespace Navicu\Core\Application\UseCases\AAVV\Search\GetListProperty;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SearchEngineService;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Clase para ejecutar el caso de uso GetListProperty para buscar por
 * un destino dado los precios de las habitaciones junto con sus servicios
 * de un conjunto de establecimientos por medio del motor de busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetListPropertyHandler implements Handler
{
	/**
     * Metodo para ejecutar el caso de uso.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 * @return object
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$rpLocation = $rf->get('Location');
		$SERepository = $rf->get('SERepository');
        $anteriority = 7;

		if ($request["type"] == "property") {
			$request["properties"] = $SERepository->resultPropertiesBySlug($request["slug"]);
		} else {
			// Busqueda del destino.
			$destiny = $rpLocation->findOneByCountrySlugType(
				$request['countryCode'],
				$request['slug'],
				$request['type']
			);

			if (!$destiny)
				return new ResponseCommandBus(400, 'OK', "Destiny No Found");

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
		}

        if ($request['nullDate']) {
            $properties = $this->searchByDateNull($request, $rf);
        } else {
            $properties = $this->searchByDate($request, $rf);
        }

		if (!$properties)
			return new ResponseCommandBus(400, 'OK',"Destiny No Found");

		if (CoreSession::isRole('ROLE_ADMIN')) {
			$response["aavv"]["creditAvailable"] = 1000000000;
			$response["aavv"]["creditNvcGain"] = 0;
		} else {
            $aavv = CoreSession::getUser()->getAavvProfile()->getAavv();
			$response["aavv"]["creditAvailable"] = $aavv->getCreditAvailable();
			$response["aavv"]["creditNvcGain"] = $aavv->getNavicuGain() * 0.5;
            $response["aavv"]["showReservationButton"] = false;
            $response["aavv"]["showModalAnteriority"] = false;
			// Si la agencia contrato credito
			if ( !$aavv->getHaveCreditZero() )
			    $response["aavv"]["showReservationButton"] = true;

            // Si la aavv no se registro con credito disponible se verifica si el dia de la reserva es mayor a 7 desde hoy
			if ( $aavv->getHaveCreditZero() )  {
			    $today = new \DateTime("now 00:00:00");
                $reservationDate = date("Y-m-d", $request["startDate"]);
                $reservationDate = new \DateTime($reservationDate);

                // Restriccion para indicar que no puede hacer reservas sin una antelacion minima de X dias
                if (date_diff($today, $reservationDate)->days <= $anteriority)
                    $response["aavv"]["showModalAnteriority"] = true;
            }
		}
		$response["properties"] = [];
		foreach ($properties as $property) {
			array_push($response["properties"], $this->propertyInfo($property, $request, $rf));
		}

        return new ResponseCommandBus(200, 'OK', $response);
	}

    public function searchByDateNull($request, $rf)
    {
		$searchService = new SearchEngineService($rf);

		// Busqueda de información (rooms, Packages, etc..)
		// de un estableimiento dentro de motor de busqueda
		// dado un establecimiento.
		//
		// @param $request["properties"]	Establemiento
		// @param $request					Otros Parametros
		// @return Array
		$result = $searchService->propertyByDateNull($request);

		return $result;
	}

    public function searchByDate($request, $rf)
    {
		$searchService = new SearchEngineService($rf);

		// Busqueda de información (rooms, Packages, etc..)
		// de un estableimiento dentro de motor de busqueda
		// dado un establecimiento.
		//
		// @param $request["properties"]	Establemiento
		// @param $request					Otros Parametros
		// @return Array
		$result = $searchService->propertyByDate($request);

		return $result;
	}

	public function propertyInfo($property, $request, $rf)
	{
		$priorityCollection = new \SplPriorityQueue;
		new RateCalculator($rf, date("Y-m-d 00:00:00", $request["startDate"]));
		$idProperty = $property["idProperty"];
		$rooms = $property["rooms"];
		$propertyEntity = $rf->get('Property')->find($idProperty);
		$response["id"] = $idProperty;
		$response["name"] = $propertyEntity->getName();
		$response["tax"] = $propertyEntity->getTax();
		$response["taxRate"] = $propertyEntity->getTaxRate();
		$response["acceptsChildren"] = $propertyEntity->getChild();
		$response["slug"] = $propertyEntity->getSlug();
		$response["publicId"] = $propertyEntity->getPublicId();

		if ($propertyEntity->getLocation()->getCityId())
			$city = $propertyEntity->getLocation()->getCityId()->getTitle();
		else
			$city = $propertyEntity->getLocation()->getParent()->getTitle();

		$response["city"] = $city;
		$response["agePolicy"] = $propertyEntity->getAgePolicy();


		$response["rooms"] = [];
		$keyRooms = array_keys($rooms);
		// Busca toda la información referente a la habitaciones encontradas.
		for ($r = 0; $r < count($rooms); $r++) {
			$roomEntity = $rf->get('Room')->find($keyRooms[$r]);

			$auxRooms = null;
			$auxRooms["idRoom"] = $keyRooms[$r];
			$auxRooms["name"] = $roomEntity->getName();
			$auxRooms["maxPeople"] = $roomEntity->getMaxPeople();
			$auxRooms["minPeople"] = $roomEntity->getMinPeople();
			$auxRooms["rules"]["kidPayAsAdult"] = $roomEntity->getKidPayAsAdult();
			$auxRooms["rules"]["increment"] = $roomEntity->getIncrement();
			$auxRooms["rules"]["incrementKid"] = $roomEntity->getIncrementKid();
			$auxRooms["availability"] = empty($rooms[$keyRooms[$r]]["availability"]) ? 0 : (int)$rooms[$keyRooms[$r]]["availability"];

			$auxRooms["packages"] = array();
			$packages = $rooms[$keyRooms[$r]]["packages"];
			for ($p = 0; $p < count($packages); $p++) {

				$packEntity = $rf->get('Pack')->find($packages[$p]["idpack"]);
				$pack["id"] = $packages[$p]["idpack"];

				$pack["name"] = CoreTranslator::getTranslator($packEntity->getType()->getCode());
				$pack["availability"] = empty($packages[$p]["specificavailability"]) ? 0 : (int)$packages[$p]["specificavailability"];
				$pack["cancellationPolicies"] = array();

				$cancellationPolicy = $packEntity->getPackCancellationPolicies();
				for ($c = 0; $c < count($cancellationPolicy); $c++) {
					$cancellationEntity = $cancellationPolicy[$c]->getCancellationPolicy();
					$cancellation["id"] = $cancellationEntity->getId();
					$cancellation["name"] = CoreTranslator::getTranslator($cancellationEntity->getType()->getCode());
					$cancellation["variatioType"] = $cancellationEntity->getVariationType();
					$cancellation["variatioAmount"] = $cancellationEntity->getVariationAmount();

					$sellRate = $packages[$p]["sellrate"] + RateCalculator::calculateCancellationPolice($cancellationEntity, $packages[$p]["sellrate"]);

					$auxCountDay = !$request["nullDate"] ? $request["dayCount"] : 1;

					$cancellation["priceByAdults"] = [];
					$priceByAdults = RateCalculator::calculateRateByPeople($roomEntity, $sellRate, $auxCountDay);
					foreach ($priceByAdults as $price) {
						$auxPrice["amount"] = $price["amountPeople"];
						$auxPrice["price"] = $price["price"];
						array_push($cancellation["priceByAdults"], $auxPrice);
					}

					$cancellation["pricebyKids"] = [];
					$priceByKids = RateCalculator::calculateRateByKid($roomEntity, $sellRate, $auxCountDay);
					foreach ($priceByKids as $price) {
						$auxPrice["amount"] = $price["amountkid"];
						$auxPrice["price"] = $price["price"];
						array_push($cancellation["pricebyKids"], $auxPrice);
					}

					if ($cancellationPolicy[$c]->getCancellationPolicy()->getType()->getTitle() != "No Refundable") {
						$cancellation["variatioTypeRule"] = $cancellationPolicy[$c]->getCancellationPolicy()->getVariationTypeRule();
						$cancellation["rules"] = array();
						$rules = $cancellationPolicy[$c]->getCancellationPolicy()->getRules();
						for ($rr = 0; $rr < count($rules); $rr++) {
							array_push(
								$cancellation["rules"],
								array(
									"upperBound"=>$rules[$rr]->getUpperBound(),
									"bottomBound"=>$rules[$rr]->getBottomBound(),
									"variationAmount"=>$rules[$rr]->getVariationAmount(),
								)
							);
						}
					}
					array_push($pack["cancellationPolicies"], $cancellation);
				}
				$priority = $this->packOrder($packEntity->getType()->getCode());
				$priorityCollection->insert($pack, $priority);
			}

			while($priorityCollection->valid()){
				array_push($auxRooms["packages"], $priorityCollection->current());
				$priorityCollection->next();
			}
			array_push($response["rooms"], $auxRooms);
		}
		return $response;
	}

	public function packOrder($type)
	{
		$order = 7;
		switch ($type)
		{
			case "nvc.room.room_only":
				$order = 70;
				break;
			case "nvc.room.breakFast_included":
				$order = 60;
				break;
			case "nvc.room.half_meal_plan":
				$order = 50;
				break;
			case "nvc.room.full_meal_plan":
				$order = 40;
				break;
			case "nvc.room.all_included":
				$order = 30;
				break;
			case "nvc.room.half_meal_plus_trip_plan":
				$order = 20;
				break;
			case "nvc.room.full_meal_plus_trip_plan":
				$order = 10;
				break;
			default:
				$order = 0;
				break;

		}
		return $order;
	}
}
