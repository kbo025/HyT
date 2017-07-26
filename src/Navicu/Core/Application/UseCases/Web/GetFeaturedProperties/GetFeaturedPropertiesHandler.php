<?php

namespace Navicu\Core\Application\UseCases\Web\GetFeaturedProperties;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\RateExteriorCalculator;
use Navicu\Core\Domain\Adapter\CoreSession;

class GetFeaturedPropertiesHandler implements Handler
{

	/**
	 *  Ejecuta las tareas solicitadas
	 *
	 * @param Command $command
	 * @param RepositoryFactoryInterface $rf
	 *
	 * @return ResponseCommandBus
	 */
	public function handle( Command $command, RepositoryFactoryInterface $rf ) {

		$router = $command->get('router');

        new RateExteriorCalculator($rf, null, null);
		$propertyRepo = $rf->get('Property');

		$featured = $propertyRepo->getFeatured();

		$response = array();

		$currency = CoreSession::get("alphaCurrency");

		foreach ($featured as $property) {
			$structure = array();

			$structure['name'] = $property->getName();
			$structure['location'] = $property->getFeaturedLocation();
			$structure['stars'] = $property->getStar();
			$structure['slug'] = $property->getSlug();
			$structure['url'] = $router->generate('navicu_property_details', array('slug' => $property->getSlug()));
			$structure['promotion'] = $property->getPromoHome();
			$structure['currency'] = $currency;
            $minRate = $propertyRepo->getLowestPrice($property->getId())[0]['min'];
            if ($minRate != null) {
                $structure['price'] = RateExteriorCalculator::calculateRateChange($minRate);
            } else {
                $structure['price'] = null;
            }
            $response[] = $structure;



		}
		return new ResponseCommandBus(200, 'Ok', $response);
	}
}