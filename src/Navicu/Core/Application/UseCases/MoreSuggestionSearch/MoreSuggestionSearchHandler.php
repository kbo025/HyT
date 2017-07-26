<?php
namespace Navicu\Core\Application\UseCases\MoreSuggestionSearch;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\SphinxService;

/**
 * Clase para ejecutar el caso de uso MoreSuggestionSearch
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 06/05/2015
 */
class MoreSuggestionSearchHandler implements Handler
{
	/**
     * Metodo que hace uso del motor de busqueda para generar una lista de
     * mas sugerencia.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{

		$request = $command->getRequest();
		$sphinxService = new SphinxService();
		$sphinxQL = $rf->get('SphinxQL');

		$word = $sphinxService->wordClean($request["word"]);

		if ($word == "") {
			return null;
		}

		switch ($request["type"]) {
			case 5: //"properties":
				$properties = $sphinxQL->propertyAutocompleteQL(
								str_replace("@*", "@property", $word),
								false
							);
				if ($properties) {
					$response = array();
					foreach ($properties as $property) {
						if (isset($property['id'])) {
							$auxProperty = $propertyRepository->findById($property['id']);

							$parish = $auxProperty->getLocation();
							$municipality = $parish->getParent();
							$state = $municipality->getParent();

							$aux["name"]= $auxProperty->getName();
							$aux["slug"]= $auxProperty->getSlug();
							$aux["parish"]= $parish->getTitle();
							$aux["municipality"]= $municipality->getTitle();
							$aux["state"]= $state->getTitle();
							$aux["star"]= $auxProperty->getStar();

							array_push($response["properties"]["list"], $aux);
						} else {
							$response += array("meta"=>$property);
						}
					}
				} else {
					$response = null;
				}
				break;
			case 4: //"cities":
				$response = $sphinxQL->cityAutocompleteQL(
								str_replace("@*", "@city", $word),
								false
							);
				break;
			case 3: //"parishes":
				$response = $sphinxQL->parishAutocompleteQL(
								str_replace("@*", "@parish", $word),
								false
							);
				break;
			case 2: //"municipalities":
				$response = $sphinxQL->municipalityAutocompleteQL(
								str_replace("@*", "@municipality", $word),
								false
							);
				break;
			case 1: //"states":
				$response = $sphinxQL->stateAutocompleteQL(
								str_replace("@*", "@state", $word),
								false
							);
				break;
			default:
				$response = null;
				break;
		}

		return $response;
	}
}
