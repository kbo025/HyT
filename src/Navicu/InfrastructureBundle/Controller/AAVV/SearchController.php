<?php

namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\AAVV\Search\GetListProperty\GetListPropertyCommand;
use Navicu\Core\Application\UseCases\AAVV\Search\GetTopDestination\GetTopDestinationCommand;
use Navicu\Core\Application\UseCases\Web\ForeignExchange\SetAlphaCurrency\SetAlphaCurrencyCommand;

/**
 * Clase SearchController
 *
 * Se define una clase y una serie de funciones necesarios para el manejo de
 * las peticiones relacionadas con Motor de Busqueda en Agencia de Viaje.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class SearchController extends Controller
{
	/**
     * Esta función retorna un listado de resultado del motor de busqueda
     * con los hoteles relacionados a un destino que el usuario a escogido.
     * Petición Asincrona.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     */
	public function asyncListSearchAction(Request $request)
    {
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);
			$command = new GetListPropertyCommand($data);
			$responseCommand = $this->get('CommandBus')->execute($command);

            if ($responseCommand->getStatusCode() == 200) {
				$response = $responseCommand->getData();
				$response["page"] = $this->get('Pagination')->pagination($response["properties"], $data["page"], 20);
				$response["search"] = $data;
				return new JsonResponse($response);
			} else {
				return new JsonResponse(null, 404);
			}

		} else {
            return new Response('Not Found',404);
        }
	}

	/**
	 * Esta función retorna la vista de lista de establecimientos para
	 * agencias de viaje.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @param Request $request
	 * @return html
	 */
	public function getListPropertiesAction($countryCode,$type, $slug, Request $request)
	{
		$data = array(
			"slug" => $slug,
			"countryCode" => $countryCode,
			"startDate" => $request->query->get("startDate"),
			"endDate" => $request->query->get("endDate"),
			"page" => 1,
			"type" => $type
		);

        $data["adult"] = empty($request->query->get('adult')) ? 2 : (integer)$request->query->get("adult");
        $data["room"] = empty($request->query->get('room')) ? 1 : (integer)$request->query->get("room");
        $data["kid"] = empty($request->query->get('kid')) ? 0 : (integer)$request->query->get("kid");

		return $this->render('NavicuInfrastructureBundle:AAVV/searchAAVV:listProperties.html.twig', ["data"=>json_encode($data)]);
	}

	/**
     * Esta función para retorna el home.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
	 * @return html
     */
	public function homeAction(Request $request)
    {
		$commandCurrency = new SetAlphaCurrencyCommand([ 'alphaCurrency' => 'VEF' ]);
		$responseCurrency = $this->get('CommandBus')->execute($commandCurrency);

		$command = new GetTopDestinationCommand();
		$response = $this->get('CommandBus')->execute($command);

		return $this->render('NavicuInfrastructureBundle:AAVV/searchAAVV:home/home.html.twig',
			['data'=>json_encode($response->getData()) ]);
	}

	/**
	 * Funcion encargada de devolver el listado de los destinos mas buscados por la aavv
	 *
	 * @version 18/10/2016
	 * @author Isabel Nieto <isabelcnd@gmail.com>
	 * @return JsonResponse
	 */
	public function getTopDestinationAction()
	{
		$command = new GetTopDestinationCommand();
		$response = $this->get('CommandBus')->execute($command);

		if ($response->getStatusCode() == 200)
			return new JsonResponse(['data' => $response->getData()], $response->getStatusCode());
		return new JsonResponse($response->getData(), $response->getStatusCode());
	}
}
