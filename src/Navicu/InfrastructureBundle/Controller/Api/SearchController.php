<?php

namespace Navicu\InfrastructureBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\Search\GetDestinyOfLocation\GetDestinyOfLocationCommand;

/**
 * Clase SearchController
 *
 * REST API: Se define una clase y una serie de funciones necesarios para
 * el manejo de las peticiones relacionadas con Motor de Busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class SearchController extends Controller
{
	/**
	 * Esta función retorna la lista de los destinos.
	 * Rest Api
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @param Request $request
	 * @return Array
	 */
	public function asyncDestinyOfLocationAction(Request $request)
	{
		if ($request->isMethod("OPTIONS"))
			return new Response('OK',200);

		if ($request->isXmlHttpRequest()) {

			$command = new GetDestinyOfLocationCommand();
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else {
			return new Response('Not Found',404);
		}
	}
}
