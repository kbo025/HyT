<?php
namespace Navicu\Core\Application\UseCases\AutoCompleteDestinationSearch;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SphinxService;

/**
 * Clase para ejecutar el caso de uso AutoCompleteDestinationSearch
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 06/05/2015
 */
class AutoCompleteDestinationSearchHandler implements Handler
{
	/**
     * Metodo que hace uso del motor de busqueda para generar una lista de
     * establecimiento y de lugares para el autocompletado.
	 *
     * @param CreateTempOwnerCommand $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{

		$sphinxService = new SphinxService();
		$sphinxQL = $rf->get('SphinxQL');
		$request = $command->getRequest();
		$priorityCollection = new \SplPriorityQueue;
		$response["data"] = array();
		$flag = 0;

		$word = $sphinxService->wordClean($request["word"]);

		$cities = $sphinxQL->cityAutocompleteQL(
								str_replace("@*", "@city", $word)
							);

		$parishes = $sphinxQL->parishAutocompleteQL(
								str_replace("@*", "@parish", $word)
							);

		$municipalities = $sphinxQL->municipalityAutocompleteQL(
								str_replace("@*", "@municipality", $word)
							);

		$states = $sphinxQL->stateAutocompleteQL(
								str_replace("@*", "@state", $word)
							);

		$properties = $sphinxQL->propertyAutocompleteQL(
								str_replace("@*", "@name", $word)
							);

		unset($cities["meta"]);
		unset($states["meta"]);

		if ($parishes) {
			$c = 0;
			unset($parishes["meta"]);
			foreach ($parishes as $parish) {
				if ($parish["coincidence"] != "t") {
					$priority = $sphinxService->priorityPosition($parish["type"], $c);
					unset($parish["count"]);
					$priorityCollection->insert($parish, $priority);
					$c ++;
				} else {
					$this->concatParent($states, $cities, $parish, 1);
				}
			}
			$flag = 1;
		}

		if ($municipalities) {
			$c = 0;
			unset($municipalities["meta"]);
			foreach ($municipalities as $municipality) {
				if ($municipality["coincidence"] != "t") {
					$priority = $sphinxService->priorityPosition($municipality["type"], $c);
					unset($municipality["count"]);
					$priorityCollection->insert($municipality, $priority);
					$c ++;
				} else {
					$this->concatParent($states, $cities, $municipality, 2);
				}
			}
			$flag = 1;
		}

		if ($cities) {
			$c = 0;
			foreach ($cities as $city) {
				if ($city["coincidence"] != "t") {
					if (empty($city["subLevel"])) {
						$city["subLevel"] = array();
					}
					$repositoryLocation = $rf->get('Location')->find($city["id"])->getParent()->getChildren();
					for ($l = 0; $l < count($repositoryLocation); $l++) {
						if ($repositoryLocation[$l]->getLvl() == 2 and count($city["subLevel"]) <= 4 and $this->notCoincidence($city["subLevel"], $repositoryLocation[$l]->getId())) {
							$auxCity["id"] = $repositoryLocation[$l]->getId();
							$auxCity["municipality"] = $repositoryLocation[$l]->getTitle();
							$auxCity["typology"] = $repositoryLocation[$l]->getParent()->getType();
							$auxCity["type"] = 2;
							array_push($city["subLevel"], $auxCity);
						}
					} 

					$priority = $sphinxService->priorityPosition($city["type"], $c);
					unset($city["count"]);
					$priorityCollection->insert($city, $priority);
					$c ++;
				} else {
					$this->concatParent($states, $cities, $city, 3);
				}
			}
			$flag = 1;
		}

		if ($properties) {
			$c = 0;
			unset($properties["meta"]);
			if ($properties) {
				$propertyRepository = $rf->get('Property');
				$auxResponse = array();
				foreach ($properties as $property) {
					if (isset($property['id'])) {
						$auxProperty = $propertyRepository->findById($property['id']);

						$parish = $auxProperty->getLocation();
						$municipality = $parish->getParent();
						$state = $municipality->getParent();

						$aux["name"]= $auxProperty->getName();
						$aux["slug"]= $auxProperty->getSlug();
						$aux["countryCode"] = $auxProperty->getLocation()->getRoot()->getAlfa2();
						$aux["municipality"]= $municipality->getTitle();
						$aux["state"]= $state->getTitle();
						$aux["type"] = $property["type"];

						array_push($auxResponse, $aux);
					}
				}
			}
			foreach ($auxResponse as $property) {
				$priority = $sphinxService->priorityPosition($property["type"], $c);
				$priorityCollection->insert($property, $priority);
				$c ++;
			}
			$flag = 1;
		}
	
		if ($states) {
			$c = 0;
			foreach ($states as $state) {
				if (empty($state["subLevel"])) {
					$state["subLevel"] = array();
				}
				$priority = $sphinxService->priorityPosition($state["type"], $c);
				$repositoryLocation = $rf->get('Location')->find($state["id"])->getChildren();
				for ($l = 0; $l < count($repositoryLocation); $l++) {
					if ($repositoryLocation[$l]->getLvl() == 4 and count($state["subLevel"]) <= 4 and $this->notCoincidence($state["subLevel"], $repositoryLocation[$l]->getId())) {
						$auxCity["id"] = $repositoryLocation[$l]->getId();
						$auxCity["city"] = $repositoryLocation[$l]->getTitle();
						$auxCity["typology"] = $repositoryLocation[$l]->getParent()->getType();
						$auxCity["type"] = 4;
						array_push($state["subLevel"], $auxCity);
					}
				}
				unset($state["count"]);
				$priorityCollection->insert($state, $priority);
				$c ++;
			}
			$flag = 1;
		}
	
		if ($flag == 1) {
			$c = 0;
			$priorityCollection->top();
			while($priorityCollection->valid()){
				if ($c <= 4) {
					array_push($response["data"], $priorityCollection->current());
				}
				$c ++;
				$priorityCollection->next();
			}
		} else {
			return null;
		}
		

		return $response;
	}

    /**
     * Esta función es usada para comparar un arreglo de destinos con un destino
     * y decir si el destino o no es igual.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Array $subLevel
     * @param integer $idDestiny
     * @return Boolean
     */
	public function notCoincidence($subLevel, $idDestiny)
	{
		$ban = true;
		for ($s = 0; $s < count($subLevel); $s++) {
			if ((int)$subLevel[$s]["id"] == $idDestiny) {
				$ban = false;
			}
		}
		return $ban;
	}

    /**
     * Esta función es usada para concatenar en forma de subnivel
     * una serie de destinos dado su parentesto con el nombre
     * del padre.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Array $states
     * @param Array $cities
     * @param Array $child
     * @param Integer $lvl
     * @return Boolean
     */
	public function concatParent(&$states, &$cities, $child, $lvl)
	{
		$x = 0;
		switch ($lvl) {
			case 1:
				$name = $child["parish"];
				break;
			case 2:
				$name = $child["municipality"];
				break;
			case 3:
				$name = $child["city"];
				break;
		}

		for ($s = 0; $s < count($states); $s++) {
			if ($states[$s]["state"] == $name) {
				if (empty($states[$s]["subLevel"])) {
					$states[$s]["subLevel"] = array();
				}
				unset($child["subLevel"]);
				array_push($states[$s]["subLevel"], $child);
				$x = 1;
			}
		}

		if ($lvl != 3) {
				
			for ($c = 0; $c < count($cities); $c++) {
				if ($cities[$c]["city"] == $name) {
					if (empty($cities[$c]["subLevel"])) {
						$cities[$c]["subLevel"] = array();
					}
					array_push($cities[$c]["subLevel"], $child);
				}
			}
		}
	}
}
