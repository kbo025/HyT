<?php
namespace Navicu\Core\Application\UseCases\Search\PropertySearchDetails;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SearchEngineService;

/**
 * Clase para ejecutar el caso de uso PropertySearchDetails
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PropertySearchDetailsHandler implements Handler
{
	/**
     * Caso de uso usado para la busqueda de la información de un establecimiento
     * necesaria para llenar la ficha del establecimiento en Busqueda de destino.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$searchEngineService = new SearchEngineService($rf);

		$repositoryProperty = $rf->get('Property')->findBySlugCountryCode($request["slug"], $request["countryCode"]);

		if (!$repositoryProperty){
			return new ResponseCommandBus(404,'Not Found');
		}

		$aux["idProperty"]=$repositoryProperty->getId();
		$property = $searchEngineService->infoProperty($aux);
		unset($property["profileImageName"]);
		$language = $property["languages"];
		unset($property["languages"]);
		unset($property["prominent"]);
		$property["amountRooms"] = $repositoryProperty->getAmountRoom();
		$property["openingYear"] = $repositoryProperty->getOpeningYear();
		$property["renewalYear"] = $repositoryProperty->getRenewalYear();

		$propertyFavoritesImages = $repositoryProperty->getPropertyFavoriteImages();
		$profileImage = $repositoryProperty->getProfileImage();

		$images = [];
		for ($i = 0; $i < count($propertyFavoritesImages); $i++) {
			$document = $propertyFavoritesImages[$i]->getImage();
			$name = $propertyFavoritesImages[$i]->getImage()->getName();

			if ($document->getPropertyImagesGallery()) {
				$type = $document->getPropertyImagesGallery()->getPropertyGallery()->getType()->getTitle();
				if ($type == "Restaurante") {
					if (strpos($name, "Restaurante") === false) {
						$name = "Restaurante ".$name;
					}
				}
			}

			array_push(
				$images,
				[
					"url" => $propertyFavoritesImages[$i]->getImage()->getFileName(),
					"name" => $name,
					"orderGallery" => $propertyFavoritesImages[$i]->getOrderGallery()
				]
			);
		}

		// Aplicando el ordenamiento de las imagenes
		usort($images, function ($a, $b) {
			return $a['orderGallery'] - $b['orderGallery'];
		});

		$property["images"] = $images;

		$property["servicesSession"] = [];
		$services = $repositoryProperty->getServices();

		if ($services) {
			// Colección de servicios entre los basicos y los no basicos.
			for ($s = 0; $s < count($services); $s++) {
				//if ($s ==19)
				//	array_push($property["servicesSession"], count($services[$s]->getSalons()));
				if ($services[$s]->servicesOthers()) {// Para servicios Restaurant, Bar o Salon
				
					if (empty($property["servicesSession"]["servicesOthers"])){
						$property["servicesSession"]["servicesOthers"] = [];
					}
				
					$root = $services[$s]->getType()->getTitle();
					if (empty($property["servicesSession"]["servicesOthers"][$root])){
						$property["servicesSession"]["servicesOthers"][$root] = [];
					}
				
					switch ($services[$s]->getType()->getType()) {
						case 3: //Restaurant
							$othersServices = $services[$s]->getRestaurants();
							$typeServices = "restaurants";
							break;
						case 2: //Bar
							$othersServices = $services[$s]->getBars();
							$typeServices = "bars";
							break;
						case 6: //Salon
							$othersServices = $services[$s]->getSalons();
							$typeServices = "salons";
							break;
					}
				
					for ($o = 0; $o < count($othersServices); $o++) {
						$auxServices = null;
						$auxServices["name"] = $othersServices[$o]->getName();
						$auxServices["description"] = $othersServices[$o]->getDescription();
						switch ($typeServices) {
							case "restaurants":
								$auxServices["foodType"] = $othersServices[$o]->getType()->getTitle();
								$auxServices["breakFastTime"] = $othersServices[$o]->getBreakfastTime();
								$auxServices["lunchTime"] = $othersServices[$o]->getLunchTime();
								$auxServices["type"] = "restaurant";
								$auxServices["dinnerTime"] = $othersServices[$o]->getDinnerTime();
								$auxServices["dietaryMenu"] = $othersServices[$o]->getDietaryMenu();
								$auxServices["buffetCar"] = $othersServices[$o]->getBuffetCartaString();
								$auxServices["schedules"] = $othersServices[$o]->getSchedule();
								break;
							case "salons":
								$auxServices["capacity"] = $othersServices[$o]->getCapacity();
								$auxServices["type"] = $othersServices[$o]->getTypeString();
								$auxServices["naturalLight"] = $othersServices[$o]->getNaturalLight();
								$auxServices["size"] = $othersServices[$o]->getSize();
								break;
							case "bars":
								$auxServices["food"] = $othersServices[$o]->getFood();
								$auxServices["foodType"] = $auxServices["food"] ?
                                    $othersServices[$o]->getFoodType()->getTitle() :
                                    '';
								$auxServices["minAge"] = $othersServices[$o]->getMinAge();
								$auxServices["type"] = $othersServices[$o]->getTypeString();
								$auxServices["schedules"] = $othersServices[$o]->getSchedule();
								break;
						}
						array_push($property["servicesSession"]["servicesOthers"][$root], $auxServices);
					}
				
				} else {// Para servicios basicos
				
					if (!isset($property["servicesSession"]["servicesBasic"])){
						$property["servicesSession"]["servicesBasic"] = [];
					}
				
					if ($services[$s]->getType()->getLvl() == 0) {
						$idRoot = $services[$s]->getType()->getId();
						$root = $services[$s]->getType()->getTitle();
					} else {
						$idRoot = $services[$s]->getType()->getRoot()->getId();
						$root = $services[$s]->getType()->getRoot()->getTitle();
					}
				
					if (!isset($property["servicesSession"]["servicesBasic"][$idRoot])){
						$property["servicesSession"]["servicesBasic"][$idRoot] = [];
						$property["servicesSession"]["servicesBasic"][$idRoot] += ["name" => $root, "position" => $idRoot];
					}
				
					switch($lvl = $services[$s]->getType()->getLvl()) {
						case 0:
						case 1:
							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"])) {
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"] = [];
							}
							if ($services[$s]->getSchedule()) {
								$dataService = $services[$s]->getSchedule();
								$dataService["opening"] = date("h:i A", strtotime($dataService["opening"]));
								$dataService["closing"] = date("h:i A", strtotime($dataService["closing"]));
							} else {
								$dataService = null;
							}
							array_push(
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"] ,
								[
									"name" => $services[$s]->getType()->getTitle(),
									"cost" => !$services[$s]->getFree(),
									"data" => $dataService,
									"position" => count($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"])
								]
							);
							break;
						case 2:
							$idParent = $services[$s]->getType()->getParent()->getId();
							
							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot][$idParent])){
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent] = [];

								$parent = $services[$s]->getType()->getParent()->getTitle();
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent] += ["name"=>$parent, "position"=>$idParent];
							}

							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent]["subServices"])) {
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent]["subServices"] = [];
							}

							array_push(
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent]["subServices"],
								[
									"name" => $services[$s]->getType()->getTitle(),
									"cost" => !$services[$s]->getFree(),
									"data" => $services[$s]->getSchedule(),
									"position" => count($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent]["subServices"])
								]
							);
							break;
						case 3:
							$idParent = $services[$s]->getType()->getParent()->getId();
							$idParent2 = $services[$s]->getType()->getParent()->getParent()->getId();

							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2])){
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2] = [];

								$parent2 = $services[$s]->getType()->getParent()->getParent()->getTitle();
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2] += ["name"=>$parent2, "position"=>$idParent2];
							}

							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent])){
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent] = [];

								$parent = $services[$s]->getType()->getParent()->getTitle();
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent] += ["name"=>$parent, "position"=>$idParent];
							}

							if (!isset($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent]["subServices"])) {
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent]["subServices"] = [];
							}

							array_push(
								$property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent]["subServices"],
								[
									"name" => $services[$s]->getType()->getTitle(),
									"cost" => !$services[$s]->getFree(),
									"data" => $services[$s]->getSchedule(),
									"position" => count($property["servicesSession"]["servicesBasic"][$idRoot]["subServices"][$idParent2]["subServices"][$idParent]["subServices"])
								]
							);
							break;
					}
				}
			}
		}

		$property["information"] = [];
		$information["accommodation"] = $repositoryProperty->getAccommodation()->getTitle();
		$information["designViewProperty"] = $repositoryProperty->getDesignViewProperty();
		$information["allIncluded"] = $repositoryProperty->getAllIncluded();
		$information["checkIn"] = $repositoryProperty->getCheckIn()->format('g:i A');
		$information["checkOut"] = $repositoryProperty->getCheckOut()->format('g:i A');
		$information["checkInAge"] = $repositoryProperty->getCheckInAge();
		$information["extraBeds"] = $repositoryProperty->getBeds();
		$information["bedsAdditionalCost"] = $repositoryProperty->getBedsAdditionalCost();
		$information["pets"] = $repositoryProperty->getPets();
		$information["petsAdditionalCost"] = $repositoryProperty->getPetsAdditionalCost();
		$information["laguages"] = $language;

        $information["description"] = $repositoryProperty->getDescription();
        $information["additionalInfo"] = $repositoryProperty->getAdditionalInfo();

		$information["tax"] = $repositoryProperty->getTax();
		$information["taxRate"] = $repositoryProperty->getTaxRate() * 100;
		$information["cityTax"] = $repositoryProperty->getCityTax();
		$information["conditionAge"] = [
			"child" => $repositoryProperty->getChild(),
            "agePolicy" => $repositoryProperty->getAgePolicy(),
			"cribs" => $repositoryProperty->getCribs(),
			"cribsAdditionalCost" => $repositoryProperty->getCribsAdditionalCost(),
			"cribsMax" => $repositoryProperty->getCribsMax()
		];

		$propertyCancellationPolicies = $repositoryProperty->getPropertyCancellationPolicies();
		$information["cancellationPolicies"] = [];
		for ($s = 0; $s < count($propertyCancellationPolicies); $s++) {
			if ($propertyCancellationPolicies[$s]->getCancellationPolicy()->getType()->getTitle() == "No Refundable") {
				array_push(
					$information["cancellationPolicies"],
					[
						"name"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getType()->getTitle(),
						"variationAmount"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getVariationAmount(),
						"variationType"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getVariationType()
					]
				);
			} else {
				$rules = $propertyCancellationPolicies[$s]->getCancellationPolicy()->getRules();
				$auxRules = [];
				for ($r = 0; $r < count($rules); $r++) {
					array_push(
						$auxRules,
						[
							"upperBound"=>$rules[$r]->getUpperBound(),
							"bottomBound"=>$rules[$r]->getBottomBound(),
							"variationAmount"=>$rules[$r]->getVariationAmount(),
						]
					);
				}
				array_push(
					$information["cancellationPolicies"],
					[
						"name"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getType()->getTitle(),
						"variationAmount"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getVariationAmount(),
						"variationType"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getVariationType(),
						"variationTypeRules"=>$propertyCancellationPolicies[$s]->getCancellationPolicy()->getVariationTypeRule(),
						"rules"=>$auxRules
					]
				);
			}
		}

		if ($repositoryProperty->getCash()) {
			$information["conditionPayment"]["cash"] = $repositoryProperty->getMaxCash();
		}
		$information["conditionPayment"]["debit"] = $repositoryProperty->getDebit();
		$information["conditionPayment"]["creditCard"] = $repositoryProperty->getCreditCard();
		$information["conditionPayment"]["creditCardAmerican"] = $repositoryProperty->getCreditCardAmex();
		$information["conditionPayment"]["creditCardMaster"] = $repositoryProperty->getCreditCardMc();
		$information["conditionPayment"]["creditCardVisa"] = $repositoryProperty->getCreditCardVisa();
		
		$property["information"] = $information;

		$property["gallery"] = [];
		$rooms = $repositoryProperty->getRooms();
		for ($r = 0; $r < count($rooms); $r++) {
			if ($rooms[$r]->getIsActive()) {
				$images = $rooms[$r]->getImagesGallery();
				if ($images) {
					$auxRooms["images"] = [];
					for ($i = 0; $i < count($images); $i++) {
						$img["name"] = $images[$i]->getImage()->getName();
						$img["url"] = $images[$i]->getImage()->getFileName();
						$img["orderGallery"] = $images[$i]->getOrderGallery();
						array_push($auxRooms["images"], $img);
					}
	
					// Aplicando el ordenamiento de las imagenes
					usort($auxRooms["images"], function ($a, $b) {
						return $a['orderGallery'] - $b['orderGallery'];
					});
	
					array_push(
						$property["gallery"],
						[
							"name" => $rooms[$r]->getName(),
							"type" => "RoomImage",
							"images" => $auxRooms["images"]
						]
					);
				}
			}
		}

		$gallery = $repositoryProperty->getPropertyGallery();

		for ($g = 0; $g < count($gallery); $g++) {
			$imagesGallery = $gallery[$g]->getImagesGallery();
			if (count($imagesGallery) > 0) {
				$images = [];
				for ($s = 0; $s < count($imagesGallery); $s++) {
					array_push(
						$images,
						[
							"name" => $imagesGallery[$s]->getImage()->getName(),
							"url" => $imagesGallery[$s]->getImage()->getFileName(),
							"orderGallery" => $imagesGallery[$s]->getOrderGallery()
						]
					);
				}

				// Aplicando el ordenamiento de las imagenes
				usort($images, function ($a, $b) {
					return $a['orderGallery'] - $b['orderGallery'];
				});

				array_push(
					$property["gallery"],
					[
						"name" => $gallery[$g]->getType()->getTitle(),
						"type" => "galleryType",
						"images" => $images
					]
				);
			}
		}

		return new ResponseCommandBus(200, 'Ok', $property);
	}
}