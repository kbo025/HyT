<?php
namespace Navicu\Core\Application\UseCases\RoomSearchOfProperty;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SphinxService;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Application\Services\RateCalculator;

/**
 * Clase para ejecutar el caso de uso RoomSearchOfProperty
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RoomSearchOfPropertyHandler implements Handler
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

		if (!$request) {
			return null;
		}

		$sphinxService = new SphinxService($rf);
		$sphinxQL = $rf->get('SphinxQL');
		$request["destiny"] = null;

		$repositoryProperty = $rf->get('Property')->findBySlug($request["slug"]);

		if (!$repositoryProperty) {
			return null;
		}

		// Buscar las habitaciones cuando la petición viene sin fecha inicial.
		if ($request["startDate"] == "") {

			$request["startDate"] = strtotime(date("Y-m-d"));
			$request["endDate"] = strtotime ( '+90 day' , strtotime ( date("Y-m-d") ) );

			$repositoryRooms = $repositoryProperty->getRooms();

			$rooms = [];
			for ($r = 0; $r < count($repositoryRooms); $r++) {
				if ($repositoryRooms[$r]->getIsActive()) {
					$packages = $repositoryRooms[$r]->getPackages();

					for ($p = 0; $p < count($packages); $p++) {
						//busqueda del precio minimo de los establecimiento dado una fecha
						//de 30 dias y los id de los establecimiento en la busqueda anterior.
						$request["idPack"] = $packages[$p]->getId();
						$auxSearch = $sphinxQL->resultPackSellRateByDestineQL($request);

						$idProperty = $auxSearch ? $auxSearch[0]["idproperty"] : $repositoryProperty->getId();
						$idRoom = $auxSearch ? $auxSearch[0]["idroom"] : $repositoryRooms[$r]->getId();
						if (empty($rooms[$idRoom])) {
							$rooms[$idRoom] = [];
							$rooms[$idRoom]["idProperty"] = $idProperty;
							$rooms[$idRoom]["availability"] = 0;
							if(empty($rooms[$idRoom]["pakages"])){
								$rooms[$idRoom]["pakages"] = [];
							}
						}
						$auxSearch[0]["sellrate"] = isset($auxSearch[0]["sellrates"]) ? $auxSearch[0]["sellrates"] : null;
						$auxSearch[0]["specificavailability"] = 0;
						$auxSearch[0]["idpack"] = $packages[$p]->getId();
						if (isset($auxSearch[0]["includeiva"])) {
							$auxSearch[0]["includeiva"] = $auxSearch[0]["includeiva"] == 't' ? true : false;
						} else {
							$auxSearch[0]["includeiva"] = false;
						}

						array_push($rooms[$idRoom]["pakages"], $auxSearch[0]);
					}
				}
			}
		} else {
			$request["slug"] = "idProperty = '".$repositoryProperty->getId()."' and";

			// Busqueda de las Habitaciones y servicios de un establecimiento.
			$rooms = $sphinxService->searchAlgorit($request);
		}

		if (empty($rooms)) {
			return null;
		}

		$response = [];
		$keyRooms = array_keys($rooms);
		// Busca toda la información referente a la habitaciones encontradas.
		for ($r = 0; $r < count($rooms); $r++) {
			$roomEntity = $rf->get('Room')->find($keyRooms[$r]);
			$variationTypePeople = $roomEntity->getVariationTypePeople();

			// --
			$roomName = $roomEntity->getName();
			$roomType = $roomEntity->getType()->getParent();
			$roomTypeCode = !is_null($roomType) ? $roomType->getCode() : $roomEntity->getType()->getCode();
			if (strcmp($roomTypeCode, 'nvc.room.type.apartment') == 0) {
				$roomName = preg_replace("/habitaci(ó|o)n /i", '', $roomName);
			}
			// --

			$auxRooms = null;
			$auxRooms["idRoom"] = $keyRooms[$r];
			$auxRooms["name"] = $roomName;
			$auxRooms["maxPeople"] = $roomEntity->getMaxPeople();
			$auxRooms["minPeople"] = $roomEntity->getMinPeople();
			$auxRooms["size"] = $roomEntity->getSize();
			$auxRooms["availability"] = $rooms[$keyRooms[$r]]["availability"];
			$auxRooms["profileImage"] = [
				"name" => $roomEntity->getProfileImage()->getImage()->getName(),
				"url" => $roomEntity->getProfileImage()->getImage()->getFileName(),
			];

			$images = $roomEntity->getImagesGallery();
			$auxRooms["images"] = [];
			for ($i = 0; $i < count($images); $i++) {
				$img["name"] = $images[$i]->getImage()->getName();
				$img["url"] = $images[$i]->getImage()->getFileName();
				array_push($auxRooms["images"], $img);
			}

			$services = $roomEntity->getFeatures();
			$auxRooms["services"] = [];
			for ($s = 0; $s < count($services); $s++) {
				$serv["name"] = $services[$s]->getFeature()->getTitle();
				array_push($auxRooms["services"], $serv);
			}

			$bedsRooms = $roomEntity->getBedRooms();
			$auxRooms["bedsRooms"] = [];
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

			$auxRooms["packages"] = [];
			$packages = $rooms[$keyRooms[$r]]["pakages"];
			for ($p = 0; $p < count($packages); $p++) {
				$packEntity = $rf->get('Pack')->find($packages[$p]["idpack"]);
				$iva = ($packages[$p]["includeiva"] == false) ? 1 + $packEntity->getRoom()->getProperty()->getTaxRate() : 1;
				$pack["id"] = $packages[$p]["idpack"];
				$pack["name"] = CoreTranslator::getTranslator($packEntity->getType()->getCode());
				$pack["availability"] = $packages[$p]["specificavailability"];
				$pack["cancellationPolicy"] = [];
				$cancellationPolicy = $packEntity->getPackCancellationPolicies();

				for ($c = 0; $c < count($cancellationPolicy); $c++) {
					$cancellation["id"] = $cancellationPolicy[$c]->getCancellationPolicy()->getId();
					$cancellation["name"] = CoreTranslator::getTranslator($cancellationPolicy[$c]->getCancellationPolicy()->getType()->getCode());
					$cancellation["variatioType"] = $cancellationPolicy[$c]->getCancellationPolicy()->getVariationType();
					$cancellation["variatioAmount"] = $cancellationPolicy[$c]->getCancellationPolicy()->getVariationAmount();

					switch ($cancellationPolicy[$c]->getCancellationPolicy()->getVariationType()) {
						case 1:// Por Porcentaje.
							$sellRate = $packages[$p]["sellrate"] * $cancellationPolicy[$c]->getCancellationPolicy()->getVariationAmount();;
							break;
						case 2:
							$sellRate = $cancellationPolicy[$c]->getCancellationPolicy()->getVariationAmount();
							break;
					}

					$sellRate = $packages[$p]["sellrate"] + $sellRate;
					$cancellation["price"] = [];

					$ratesByPeople = $roomEntity->getRatesByPeoples();
					for ($rp = 0; $rp < count($ratesByPeople); $rp++) {
						if ($variationTypePeople == 1) { // Por Porcentaje.
							$pricePerPerson = $ratesByPeople[$rp]->getAmountRate() * $sellRate * $iva;
						} else { // Por entero.
							$auxCountDay = isset($request["countDays"]) ? $request["countDays"] : 1;
							$pricePerPerson = round(RateCalculator::calculateClientRate(
									$ratesByPeople[$rp]->getAmountRate()*$auxCountDay,
									$roomEntity->getProperty()
								),
								2
							);
						}

						array_push(
							$cancellation["price"],
							[
								"amountPeople" => $ratesByPeople[$rp]->getNumberPeople(),
								"price" => is_null($packages[$p]["sellrate"]) ? 0 : round(($sellRate * $iva)  + $pricePerPerson, 2)
							]
						);
					}

					if ($cancellationPolicy[$c]->getCancellationPolicy()->getType()->getTitle() != "No Refundable") {
						$cancellation["variatioTypeRule"] = $cancellationPolicy[$c]->getCancellationPolicy()->getVariationTypeRule();
						$cancellation["rules"] = [];
						$rules = $cancellationPolicy[$c]->getCancellationPolicy()->getRules();
						for ($rr = 0; $rr < count($rules); $rr++) {
							array_push(
								$cancellation["rules"],
								[
									"upperBound"=>$rules[$rr]->getUpperBound(),
									"bottomBound"=>$rules[$rr]->getBottomBound(),
									"variationAmount"=>$rules[$rr]->getVariationAmount(),
								]
							);
						}
					}
					array_push($pack["cancellationPolicy"], $cancellation);
				}
				array_push($auxRooms["packages"], $pack);
			}
			array_push($response, $auxRooms);
		}

		return $response;
	}
}

/* End of file */