<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\IncludeIntoSession;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Clase para incluir dentro de la session de usuario ciertos
 * parametros del establecimiento.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class IncludeIntoSessionHandler implements Handler
{
	/**
     * Esta función es usada para incluir dentro de la session de usuario
     * ciertos parametros del establecimiento.
	 *
     * @param SlugSecurity $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();

		$slug = !is_null($request["slug"]) ? $request["slug"] : $request["properties"][0]->getSlug();
		$property = $rf->get('Property')->findBySlug($slug);

		if (CoreSession::isRole('ROLE_ADMIN') or
			CoreSession::isRole('ROLE_SALES_EXEC') or
			CoreSession::isRole('ROLE_TELEMARKETING'))
			$properties = [];
		else
			$properties = !is_null($request["properties"]) ? $request["properties"] : CoreSession::getUser()->getOwnerProfile()->getProperties();

		$propertiesCollection = [];
		foreach ($properties as $auxProperty) {
			array_push($propertiesCollection, ["slug" => $auxProperty->getSlug(), "name" => $auxProperty->getName()]);
		}

		CoreSession::set("currency", $property->getCurrency()->getSimbol());
		CoreSession::set("publicId", $property->getPublicId());
		CoreSession::set("rateType", $property->getRateType());
		CoreSession::set("count", count($properties));
		CoreSession::set("slug", $slug);
		CoreSession::set("name", $property->getName());
		CoreSession::set("properties", $propertiesCollection);

		return null;
	}
}