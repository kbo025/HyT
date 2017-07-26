<?php 
namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 *
 * La siguiente clase se encarga de los servicios que interacturan
 * en el nucleo con el motor de busqueda.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 24/06/2015
 */
class SearchEngineService
{
    private $rf;

    /**
    * Constructor del servicio
    *
    * @param RepositoryFactoryInterface $rf
    */
    public function __construct(RepositoryFactoryInterface $rf = null)
    { 
        $this->rf = $rf;
    }

	/**
	 * Función usada para buscar dentro del motor de busqueda
	 * las habitaciones y servicios de varios establecimiento o uno
	 * que concuerden con los requerimientos y se adapten a las
	 * restricciones del negocio. Manejo sin Fecha.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $request
	 * @return Array
	 */
	public function propertyByDateNull($request)
	{
		$sphinxQL = $this->rf->get('SERepository');
		$resp = null;
		$properties = $request["properties"];

		// Trabajando con las Habitaciones de un establecimiento.
		// Manejo de filtos para habitaciones.
		for ($p = 0; $p < count($properties); $p++) {
			// Convirtiendo la colección de habitaciones en un Array.
			$rooms = explode(",",str_replace(["}","{"], "", $properties[$p]["idrooms"]));
			$idProperty = $properties[$p]["id"];

			/*
			 * Filtro Rooms 
			 */
			for ($r = 0; $r < count($rooms); $r++) {

				$idRoom = $rooms[$r];

				// Busqueda de habitación por id.
				//
				// @param Id			Id de la Habitación
				// @return Array
				$room = $sphinxQL->resultRoomById($idRoom);
				
				$packages = explode(",",str_replace(["}","{"], "", $room[0]["idpackages"]));
				
				for ($i = 0; $i < count($packages); $i++) {
					$idPack = $packages[$i];

					// Busqueda del precio menor y mayor por Servicio
					// dado un rango de 30 dias.
					//
					// @param Id			Id de la Servicio
					// @return Array
					$prices = $sphinxQL->resultPriceRangebyPack($idPack);

					if ($prices) {
						$dpSpecific[$idPack]["sellrate"] = $prices ? $prices[0]["minprice"] : 0;
						$dpSpecific[$idPack]["priceExpensive"] = $prices ? $prices[0]["maxprice"] : 0;
						$dpSpecific[$idPack]["amountkid"] = isset($room[0]["amountratekid"]) ? $room[0]["amountratekid"] : null;
						$dpSpecific[$idPack]["variationtypekids"] = $room[0]["variationtypekids"];
						$dpSpecific[$idPack]["idpack"] = $idPack;

						if (empty($resp[$idProperty]["rooms"][$idRoom]["packages"]))
							$resp[$idProperty]["rooms"][$idRoom]["packages"] = [];

						array_push($resp[$idProperty]["rooms"][$idRoom]["packages"], $dpSpecific[$idPack]);
						$resp[$idProperty]["rooms"][$idRoom]["idRoom"] = $idRoom;
						$resp[$idProperty]["rooms"][$idRoom]["idTypeRoom"] = $room[0]["idroomtype"];
					}
				}
				if (!empty($resp[$idProperty]["rooms"])) {
					$resp[$idProperty]["idProperty"] = $idProperty;
					$resp[$idProperty]["idTypeProperty"] = $properties[$p]["idpropertytype"];
					$resp[$idProperty]["taxRate"] = $properties[$p]['taxrate'];
					$resp[$idProperty]["includeiva"] = $properties[$p]["includeiva"] == 1 ? true : false;
					$resp[$idProperty]["latitude"] = $properties[$p]["latitude"];
					$resp[$idProperty]["longitude"] = $properties[$p]["longitude"];
				}
			}
		}
		return $resp;
	}

	/**
	 * Función usada para buscar dentro del motor de busqueda
	 * las habitaciones y servicios de varios establecimiento
	 * que concuerden con los requerimientos y se adapten a las
	 * restricciones del negocio. Manejo con Fecha.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $request
	 * @return Array
	 */
	public function propertyByDate($request)
	{

		$sphinxQL = $this->rf->get('SERepository');
		$resp = null;
		$properties = $request["properties"];

		// Trabajando con las Habitaciones de un establecimiento.
		// Manejo de filtos para habitaciones.
		for ($p = 0; $p < count($properties); $p++) {

			// Convirtiendo la colección de habitaciones en un Array.
			$rooms = explode(",",str_replace(["}","{"], "", $properties[$p]["idrooms"]));
			$idProperty = $properties[$p]["id"];

			/*
			 * Filtro Rooms 
			 */
			$countRoom = count($rooms);
			for ($r = 0; $r < $countRoom; $r++) {

				// Busqueda de habitación por id.
				//
				// @param$rooms[$r]			Id de la Habitación
				// @return Array
				$room = $sphinxQL->resultRoomById($rooms[$r]);

				// Verifica si la capacidad de adulto y niño no supera para una habitacion cuando
				// la capacidad de habitacion busada por un usuario es 1.
				$capicity = $request["room"] == 1 ? $request["adult"] + $request["kid"] <= $room[0]["maxpeople"] : true;

				//FILTRO Rooms
				// Si la habitación tiene minPeople mayor al
				// numero de personas solicitadas
				if ($request["adult"] + $request["kid"] >= $room[0]["minpeople"] && $capicity ) {
					$auxRooms[$room[0]["id"]] = $room[0];
				} else {
					unset($rooms[$r]);
				}
				//Fin FILTRO Rooms
			}

			if (empty($rooms))
				continue;

			// Manejo de la disponibilidad de CutOff de los dailyRoom.
			// La diferencia de dias entre el dia actual y el primer dia
			// de reserva.
			$todayCutOff = $request["startDate"] - strtotime(date("Y-m-d"));
			$request["availabilityCutOff"] = $todayCutOff == 0 ? 0 : (( ( ($request["startDate"]-strtotime(date("Y-m-d"))) / 60 ) / 60 ) / 24);

			//Covirtiendo el Array de Habitaciones en String para
			//la consulta IN (n,n,n) del repositorio resultDailyRoom.
			$request["idRooms"] = '('.implode(',',$rooms).')';

			// Busqueda de los dailyRoom dada una colección de
			// habitaciones. Y otras restricciones.
			//
			// @param $request["startDate"]				Fecha incial
			// @param $request["idRooms"]				Colección idRoom
			// @param $request["dayCount"]				Dias de antelación
			// @param $request["availabilityCutOff"]	Disponibilidad continua
			// @return Array
			$dailyRooms = $sphinxQL->resultDailyRoomByRooms($request);
			if ($dailyRooms) {

				// Manejo del dailyRoom por habitación
				for ($dr = 0; $dr < count($dailyRooms); $dr++) {

					$idRoom = $dailyRooms[$dr]["idroom"];
					$validPack = false;

					$packages = explode(",",str_replace(["}","{"], "", $auxRooms[$idRoom]["idpackages"]));
					$request["idPackages"] = '('.implode(',',$packages).')';

					// Busqueda de los dailyPack dada una colección de
					// Servicios "Packages". Y otras restricciones.
					//
					// @param $request["startDate"]				Fecha incial
					// @param $request["idPackages"]			Colección idRoom
					// @param $request["dayCount"]				Dias de antelación
					// @return Array
					$dpSpecific = $sphinxQL->resultDailyPackByPackages($request);
					// Manejo del dailyPack por habitación
					for ($dp = 0; $dp < count($dpSpecific); $dp++) {

						$idPack = $dpSpecific[$dp]["idpack"];

						// Busqueda en el servicio "pack", una colección de
						// DailyPackages dado un rango de fecha.
						//
						// @param $request["startDate"]			Fecha inicial
						// @param $request["endDate"]			Fecha final
						// @param idPack						Id de un servicio
						// @return Array
						$dailyPackages = $sphinxQL->resultDailyPackByDateRank($request, $idPack);
						$maxNightAbsolute = null;
						$minNightAbsolute = null;
						$packSellRate = 0;
						$minAvailability = null;
						$ctaFlag = true;
                        //Variable que indica si existe al menos un daily en promocion para esta busqueda
                        $dpSpecific[$dp]["promotion"] = false;
						// Manejo de los dailyPackages por Servicios "Pack"
						for ($dps = 0; $dps < count($dailyPackages); $dps++) {

							// Busqueda de los repositorio para las entidades DailyRoom y DailPack
							$DrRepository = $this->rf->get('DailyRoom');
							$DpRepository = $this->rf->get('DailyPack');

							// Busqueda del id de session del usuario
							$sessionId = CoreSession::getSessionId();

							// Busqueda de las entidades dailyPack y dailyRoom
							$DPEntity = $DpRepository->find($dailyPackages[$dps]["id"]);
							$DREntity = $DrRepository->findOneByDateRoomId($idRoom, date("Y-m-d", $dailyPackages[$dps]["date"]));

							// Manejo de la disponibilidad bloqueada para un dailyPack y un dailyRoom
							$availabilityPack = $dailyPackages[$dps]['specificavailability'] - $DPEntity->getLockeAvailability([$sessionId]);
							$availabilityRoom = $DREntity->getAvailability() - $DREntity->getLockeAvailability([$sessionId]);

							// Si la disponibilidad bloqueada queda por arriba
							// de la disponibilidad de un dailyRoom
							if ($availabilityPack > $availabilityRoom)
								$dailyPackages[$dps]['specificavailability'] = $availabilityRoom;
							else
								$dailyPackages[$dps]['specificavailability'] = $availabilityPack;

							// Capturando la Maxima minima de la colección
							if ($dailyPackages[$dps]['maxnight'] < $maxNightAbsolute || !$maxNightAbsolute)
								$maxNightAbsolute = $dailyPackages[$dps]['maxnight'];

							// Capturando la Minima maxima de la colección
							if ($dailyPackages[$dps]['minnight'] > $minNightAbsolute || !$minNightAbsolute)
								$minNightAbsolute  = $dailyPackages[$dps]['minnight'];

							// Verifincando que no sea el ultimo dia para capturar los precio y disponibilidad
							if ($request["endDate"] != $dailyPackages[$dps]["date"]) {

								// Capturando el precio de los dailyPack en un rango de fecha
								$packSellRate = $packSellRate + $dailyPackages[$dps]['sellrate'];

								// Capturando la disponibilidad minima de los dailyPack en un rango de fecha
								if ($dailyPackages[$dps]['specificavailability'] < $minAvailability || is_null($minAvailability))
									$minAvailability = $dailyPackages[$dps]['specificavailability'];
							}

                            if(isset($dailyPackages[$dps]["promotion"])) {
                                if ($dailyPackages[$dps]["promotion"] == 't') {
                                    $dpSpecific[$dp]["promotion"] = true;
                                }
                            }
						}

						// Verifincando que el ultimo dia no tenga restricción de salida "CTD"
						if ($dailyPackages[$dps-1]["date"] == $request["endDate"])
							if ($dailyPackages[$dps-1]['ctd'] == 'f')
								$ctaFlag = false;
						// Verifincando cuando el ultimo esta incompleto
						if (count($dailyPackages) == $request["dayCount"])
							$ctaFlag = false;

						// Validar si el servicio cuanta con el minimo y maximo de noche absoluto
						// para los dias de recerva requeridos por el usuario
						if ($request["dayCount"] <= $maxNightAbsolute and
							$request["dayCount"] >= $minNightAbsolute and
							$ctaFlag == false and
							$minAvailability > 0) {

							$validPack = true;
							$resp[$idProperty]["rooms"][$idRoom]["idTypeRoom"] = $auxRooms[$idRoom]["idroomtype"];
							$resp[$idProperty]["rooms"][$idRoom]["maxPeople"] = $auxRooms[$idRoom]["maxpeople"];
							$resp[$idProperty]["rooms"][$idRoom]["minPeople"] = $auxRooms[$idRoom]["minpeople"];

							// Se incluye dentro en los dailyPack la variable de minimo de Disponibilidad
							$dpSpecific[$dp]["specificavailability"] = $minAvailability; 

							$dpSpecific[$dp]["sellrate"] = $packSellRate;
							$dpSpecific[$dp]["includeiva"] = $properties[$p]["includeiva"] == 1 ? true : false;
							$dpSpecific[$dp]["amountkid"] = $auxRooms[$idRoom]["amountratekid"];
							$dpSpecific[$dp]["variationtypekids"] = $auxRooms[$idRoom]["variationtypekids"];

							if(empty($resp[$idProperty]["rooms"][$idRoom]["packages"]))
								$resp[$idProperty]["rooms"][$idRoom]["packages"] = [];
							array_push($resp[$idProperty]["rooms"][$idRoom]["packages"], $dpSpecific[$dp]);

						}
					}

					if ($validPack) {

						// Busqueda en una habitación una colección de
						// DailyRooms dado un rango de fecha.
						//
						// @param $request["startDate"]			Fecha inicial
						// @param $request["endDate"]			Fecha final
						// @param $idRoom						Id de una habitación
						// @return Array
						$availabilities = $sphinxQL->resultDailyRoomByDateRank($request, $idRoom);

						// Busqueda dentro de una Colección de DailyRooms la
						// disponibilidad absoluta.
						$resp[$idProperty]["rooms"][$idRoom]["availability"] = min(array_column($availabilities, "availability"));

						$availabilityPackSum = array_sum(
							array_column(
								$resp[$idProperty]["rooms"][$idRoom]["packages"], 'specificavailability'
							)
						);

						if ($resp[$idProperty]["rooms"][$idRoom]["availability"] > $availabilityPackSum)
							$resp[$idProperty]["rooms"][$idRoom]["availability"] = $availabilityPackSum;

						$resp[$idProperty]["idTypeProperty"] = $properties[$p]["idpropertytype"];
						$resp[$idProperty]["idProperty"] = $idProperty;
						$resp[$idProperty]["includeiva"] = $properties[$p]["includeiva"] == 1 ? true : false;
						$resp[$idProperty]["latitude"] = $properties[$p]["latitude"];
						$resp[$idProperty]["longitude"] = $properties[$p]["longitude"];
						
						$resp[$idProperty]["rooms"][$idRoom]["idRoom"] = $idRoom;
					}
				}
			}
		}
		return $resp;
	}

    /**
	 * Función usada para depurar un string, necesario para el manejo
	 * correcto del motor de busqueda.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param String $word
	 * @return String
	 */
	public function wordClean($word)
	{
		$word = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$word
		);

		$word = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$word
		);

		$word = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$word
		);

		$word = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$word
		);

		$word = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$word
		);

		// Limpiar el string de simbolos.
		$words = preg_replace('([^A-Za-z0-9[:space:]])', "", $word);

		// Pasando string a array.
		$words = explode(" ", $words);

		// Eliminando palabras con longitud menor a 2
		$response =  array_filter(
			array_map(function ($w) {
				if (strlen($w) > 2)
					return $w;
				else
					return null;
			},
			$words)
		);

		// Retornando un string
		$response = implode(" ", $response);
        return $response == "" ? null : $response;
    }

	/**
	 * Función usada para devolver la información de un establecimiento.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Object $property
	 * @return void
	 */
	public function infoProperty($property)
	{
		$propertyRepository = $this->rf->get('Property')
			->find($property["idProperty"]);

		$data["name"] = $propertyRepository->getName();
		$data["slug"] = $propertyRepository->getSlug();
		$data["countryCode"] = $propertyRepository->getLocation()->getRoot()->getAlfa2();
		$data["type"] = CoreTranslator::getTranslator(
			$propertyRepository->getAccommodation()->getCode(),
			'propertytype'
		);

		$data["services"] = [];
		$services = $propertyRepository->getServices();
		for ($s = 0; $s < count($services); $s++) {
			if ($services[$s]->getType()->getLvl() == 0) {
				$root = $services[$s]->getType()->getTitle();
			} else {
				$root = $services[$s]->getType()->getRoot()->getTitle();
			}
			array_push(
				$data["services"], 
				array(
					"name"=>$services[$s]->getType()->getTitle(),
					"root"=>$root,
					"priority"=>$services[$s]->getType()->getPriority()
				)
			);
		}

		$data["star"] = $propertyRepository->getStar();
		$data["latitude"] = $propertyRepository->getCoordinates()["latitude"];
		$data["longitude"] = $propertyRepository->getCoordinates()["longitude"];
		$data["address"] = $propertyRepository->getAddress();
		$data["parish"] = $propertyRepository->getLocation()->getTitle();
		$data["municipality"] =$propertyRepository->getLocation()->getParent()->getTitle();
		$data["state"] = $propertyRepository->getLocation()->getParent()->getParent()->getTitle();
		$data["country"] = $propertyRepository->getLocation()->getParent()->getParent()->getParent()->getTitle();
		$data["profileImageName"] = ($propertyRepository->getProfileImage()) ? $propertyRepository->getProfileImage()->getImage()->getFileName() : null;

		$data["languages"] = [];
		$languages  = $propertyRepository->getLanguages();
		for ($l = 0; $l < count($languages); $l++) {
			array_push($data["languages"], $languages[$l]->getNative());
		}

		$data["prominent"] = $propertyRepository->getProminent();

		if (isset($property["sellRate"])) {
			$data["sellrate"] = $property["sellRate"];
		}

		if (isset($property["priceExpensive"])) {
			$data["priceExpensive"] = $property["priceExpensive"];
		}

		if (isset($property['promotion'])) {
            $data['promotion'] = $property['promotion'];
        }

		return $data;
	}

	/**
	 * Función usada para devolver los tipos de propiedades.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $types
	 * @return void
	 */
	public function propertyTypes(&$types)
	{
		$response = [];
		$auxTypes = array_unique($types);
		$keyAuxTypes = array_keys($auxTypes);
		$keyTypes = array_keys($types);
		for ($at = 0; $at < count($auxTypes); $at++) {
			$c = 0;
			$idType = $auxTypes[$keyAuxTypes[$at]];
			$propertyType = $this->rf->get('Accommodation')->getById($idType);
			for ($t = 0; $t < count($types); $t++) {
				if ($types[$keyTypes[$t]] == $idType) {
					$c += 1;
				}
			}
			array_push($response, array(
				"name" => $propertyType->getTitle(),
				"idType" => $types[$keyAuxTypes[$at]],
				"count" => $c
				)
			);
		}
		$types = $response;
	}

	/**
	 * Función usada para devolver los tipos de habitaciones.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $types
	 * @return void
	 */
	public function roomTypes(&$types)
	{
		$response = [];
		$keyTypes = array_keys($types);
		for ($t = 0; $t < count($types); $t++) {
			$propertyType = $this->rf->get('RoomType')->find($types[$keyTypes[$t]]);
			array_push($response, array(
				"name" => "undefined",
				"idType" => $types[$keyTypes[$t]]
				)
			);
		}
		$types = $response;
	}

	/**
	 * Función usada para el manejo de paginación dado una colección de
	 * establecimiento.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $properties
	 * @param Array $request
	 * @param Array $response
	 * @return void
	 */
	public function pagination(&$response, $request, $properties)
	{
		// Uso de Paginación.
		$resultPerPage =  20; // Numero de resultado por pagina
		$lowerLimit = ($request["page"]-1)*$resultPerPage;// limite inferior
		$upperLimit = (($request["page"]-1)*$resultPerPage)+$resultPerPage; // limite superior

		//si limite inferior es inferior a la cantidad de establecimientos encontrado
		if ($lowerLimit > $response["propertiesAmount"]) { 
			return 0;
		}

		//si limite inferior es superior a la cantidad de establecimientos encontrado
		if ($upperLimit > $response["propertiesAmount"]) {
			$upperLimit = $response["propertiesAmount"];
		}

		$response["properties"] = [];
		$keyRoom = array_keys($properties);
		// Manejo del conjunto de establecimiento.
		// Contrucción de los establecimientos para la paginación especificada.	
		for ($i = $lowerLimit; $i < $upperLimit; $i++) {

			$property = $this->infoProperty($properties[$keyRoom[$i]]);
			array_push($response["properties"], $property);

		}

		$pageTotal = (int)ceil($response["propertiesAmount"]/$resultPerPage);
		if ($pageTotal > 1) {
			$response["page"]["pageCount"] = $pageTotal;
			$response["page"]["next"] = $request["page"] + 1 <= $pageTotal ? $request["page"] + 1 : null;
			$response["page"]["previous"] = $request["page"] - 1 >= 1 ? $request["page"] - 1 : null;
			$response["page"]["current"] = $request["page"];
		}

	}
}
