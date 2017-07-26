<?php
namespace Navicu\Core\Application\UseCases\SuggestionSearch;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SphinxService;

/**
 * Clase para ejecutar el caso de uso SuggestionSearch
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 06/05/2015
 */
class SuggestionSearchHandler implements Handler
{
	/**
     * Metodo que hace uso del motor de busqueda para generar una lista de
     * establecimiento y de lugares para la pagina de sugerencia.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{

		$sphinxService = new SphinxService();
		$sphinxQL = $rf->get('SphinxQL');
		$propertyRepository = $rf->get('Property');
		$request = $command->getRequest();

		$word = $sphinxService->wordClean($request["word"]);

		if ($word == "") {
			return null;
		}

		$aux["cities"] = $sphinxQL->cityAutocompleteQL(
								str_replace("@*", "@city", $word)
							);

		if ($aux["cities"]) {
			$response["cities"] = array();
			$response["cities"] += array( "list" =>$aux["cities"]);
			unset($response["cities"]["list"]["meta"]);
			$response["cities"] += array( "meta"=>$aux["cities"]["meta"]);
		}

		$aux["parishes"] = $sphinxQL->parishAutocompleteQL(
								str_replace("@*", "@parish", $word)
							);

		if ($aux["parishes"]) {
			$response["parishes"] = array();
			$response["parishes"] += array( "list" =>$aux["parishes"]);
			unset($response["parishes"]["list"]["meta"]);
			$response["parishes"] += array( "meta"=>$aux["parishes"]["meta"]);
		}

		$aux["municipalities"] = $sphinxQL->municipalityAutocompleteQL(
								str_replace("@*", "@municipality", $word)
							);

		if ($aux["municipalities"]) {
			$response["municipalities"] = array();
			$response["municipalities"] += array( "list" =>$aux["municipalities"]);
			unset($response["municipalities"]["list"]["meta"]);
			$response["municipalities"] += array( "meta"=>$aux["municipalities"]["meta"]);
		}

		$aux["states"] = $sphinxQL->stateAutocompleteQL(
								str_replace("@*", "@state", $word)
							);

		if ($aux["states"]) {
			$response["states"] = array();
			$response["states"] += array( "list" =>$aux["states"]);
			unset($response["states"]["list"]["meta"]);
			$response["states"] += array( "meta"=>$aux["states"]["meta"]);
		}

		$properties = $sphinxQL->propertyAutocompleteQL(
								str_replace("@*", "@name", $word)
							);

		if ($properties) {
			$response["properties"]["list"] = array();
			foreach ($properties as $property) {
				if (isset($property['id'])) {
					$auxProperty = $propertyRepository->findById($property['id']);
	
					$parish = $auxProperty->getLocation();
					$municipality = $parish->getParent();
					$state = $municipality->getParent();
	
					$aux["name"]= $auxProperty->getName();
					$aux["slug"]= $auxProperty->getSlug();
					$aux["countryCode"] = $auxProperty->getLocation()->getRoot()->getAlfa2();
					$aux["parish"]= $parish->getTitle();
					$aux["municipality"]= $municipality->getTitle();
					$aux["state"]= $state->getTitle();
					$aux["star"]= $auxProperty->getStar();
	
					array_push($response["properties"]["list"], $aux);
				} else {
					$response["properties"] += array("meta"=>$property);
				}
			}
		} else {
			$response["properties"] = null;
		}
		return $response;
	}
}
