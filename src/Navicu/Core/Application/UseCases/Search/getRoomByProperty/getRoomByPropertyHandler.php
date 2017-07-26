<?php
namespace Navicu\Core\Application\UseCases\Search\getRoomByProperty;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SearchEngineService;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Application\Services\RateCalculator;

/**
 * Clase para ejecutar el caso de uso RoomSearchOfProperty
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getRoomByPropertyHandler implements Handler
{
	/**
     * Metodo que hace uso del motor de busqueda para buscar las habitaciones
     * junto con sus servicios de un establecimiento por medio del motor de busqueda.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
        $SERepository = $rf->get('SERepository');
		new RateCalculator($rf, date("Y-m-d 00:00:00", $request["startDate"]));

		// Busqueda de los establecimientos dado un slug.
		//
		// @param $request['slug']			slug del establecimiento
		// @return Array
        $request["properties"] = $SERepository->resultPropertiesBySlug($request['slug']);
		if (!$request["properties"])
			return new ResponseCommandBus(400, 'OK',"Destiny No Found");

        if ($request['nullDate']) {
            $property = $this->searchByDateNull($request, $rf);
        } else {
            $property = $this->searchByDate($request, $rf);
        }

		if (!$property)
			return new ResponseCommandBus(400, 'OK',"Destiny No Found");

		$idProperty = array_column($property,"idProperty")[0];
		$rooms = array_column($property,"rooms")[0];
		$response = [];
		$keyRooms = array_keys($rooms);
		// Busca toda la información referente a la habitaciones encontradas.
		for ($r = 0; $r < count($rooms); $r++) {
			$roomEntity = $rf->get('Room')->find($keyRooms[$r]);

			$variationTypePeople = $roomEntity->getVariationTypePeople();
			$variationTypeKids = $roomEntity->getVariationTypeKids();
			$auxRooms = null;
			$auxRooms["idRoom"] = $keyRooms[$r];
			$auxRooms["name"] = $roomEntity->getName();
			$auxRooms["maxPeople"] = $roomEntity->getMaxPeople();
			$auxRooms["minPeople"] = $roomEntity->getMinPeople();
			$auxRooms["rules"]["kidPayAsAdult"] = $roomEntity->getKidPayAsAdult();
			$auxRooms["rules"]["increment"] = $roomEntity->getIncrement();
			$auxRooms["rules"]["incrementKid"] = $roomEntity->getIncrementKid();
			$auxRooms["size"] = $roomEntity->getSize();
			$auxRooms["availability"] = empty($rooms[$keyRooms[$r]]["availability"]) ? 0 : $rooms[$keyRooms[$r]]["availability"];
			$auxRooms["profileImage"] = array(
				"name" => $roomEntity->getProfileImage()->getImage()->getName(),
				"url" => $roomEntity->getProfileImage()->getImage()->getFileName(),
			);

			$images = $roomEntity->getImagesGallery();
			$auxRooms["images"] = array();
			for ($i = 0; $i < count($images); $i++) {
				$img["name"] = $images[$i]->getImage()->getName();
				$img["url"] = $images[$i]->getImage()->getFileName();
				array_push($auxRooms["images"], $img);
			}

			$services = $roomEntity->getFeatures();
			$auxRooms["services"] = array();
			for ($s = 0; $s < count($services); $s++) {
				$serv["name"] = $services[$s]->getFeature()->getTitle();
				array_push($auxRooms["services"], $serv);
			}

			$bedsRooms = $roomEntity->getBedRooms();
			$auxRooms["bedsRooms"] = array();
			for ($br = 0; $br < count($bedsRooms); $br++) {
				$beds = $bedsRooms[$br]->getBeds();
				$bedR["name"] = null;
				for ($b = 0; $b < count($beds); $b++) {
					if ($beds[$b]->getAmount() > 1) {
						$amountBedRoom = $beds[$b]->getAmount();
					} else {
						$amountBedRoom = null;
					}
					$bedR["name"] .= $amountBedRoom." ".$beds[$b]->getTypeString()." + ";
				}
				$bedR["id"] = $bedsRooms[$br]->getId();
				$bedR["name"] = substr($bedR["name"], 0, -3);
				array_push($auxRooms["bedsRooms"], $bedR);
			}

			$auxRooms["packages"] = array();
			$packages = $rooms[$keyRooms[$r]]["packages"];
			for ($p = 0; $p < count($packages); $p++) {

				$packEntity = $rf->get('Pack')->find($packages[$p]["idpack"]);
				$pack["id"] = $packages[$p]["idpack"];
				$pack["description"] =
					/*'('.$packEntity->getType()->getCode().') '.*/
					CoreTranslator::getTranslator(
						"description." . explode(
							".",
							$packEntity->getType()->getCode()
						)[2],
						"servicesType"
					);

				$pack["name"] = CoreTranslator::getTranslator($packEntity->getType()->getCode());
				$pack["availability"] = empty($packages[$p]["specificavailability"]) ? 0 : $packages[$p]["specificavailability"];
				$pack["cancellationPolicy"] = array();

                if (isset($packages[$p]['promotion']))
                    $pack["promotion"] = $packages[$p]['promotion'];
                else
                    $pack["promotion"] = false;

				$cancellationPolicy = $packEntity->getPackCancellationPolicies();
				for ($c = 0; $c < count($cancellationPolicy); $c++) {
					$cancellationEntity = $cancellationPolicy[$c]->getCancellationPolicy();
					$cancellation["id"] = $cancellationEntity->getId();
					$cancellation["name"] = CoreTranslator::getTranslator($cancellationEntity->getType()->getCode());
					$cancellation["variatioType"] = $cancellationEntity->getVariationType();
					$cancellation["variatioAmount"] = $cancellationEntity->getVariationAmount();

					$sellRate = $packages[$p]["sellrate"] + RateCalculator::calculateCancellationPolice($cancellationEntity, $packages[$p]["sellrate"]);

					$auxCountDay = !$request["nullDate"] ? $request["dayCount"] : 1;
					$cancellation["price"] = RateCalculator::calculateRateByPeople($roomEntity, $sellRate, $auxCountDay);
					$cancellation["pricebyKids"] = RateCalculator::calculateRateByKid($roomEntity, $sellRate, $auxCountDay);

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
					array_push($pack["cancellationPolicy"], $cancellation);
				}
				array_push($auxRooms["packages"], $pack);
			}
			array_push($response, $auxRooms);
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
}
