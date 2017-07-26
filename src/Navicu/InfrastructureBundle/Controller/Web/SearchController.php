<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\Search\getPorpertyByLocation\getPorpertyByLocationCommand;
use Navicu\Core\Application\UseCases\Search\getRoomByProperty\getRoomByPropertyCommand;
use Navicu\Core\Application\UseCases\GetLocationHome\GetLocationHomeCommand;
use Navicu\Core\Application\UseCases\Search\PropertySearchDetails\PropertySearchDetailsCommand;
use Navicu\Core\Application\UseCases\Search\getPropertyByCoordenate\getPropertyByCoordenateCommand;
use Navicu\Core\Application\UseCases\Web\GetLocationMap\GetLocationMapCommand;
use Navicu\Core\Application\UseCases\Search\GetDestinyOfLocation\GetDestinyOfLocationCommand;
use Navicu\Core\Application\UseCases\Search\GetAutoCompletedDestiny\GetAutoCompletedDestinyCommand;

/**
 * Clase SearchController
 *
 * Se define una clase y una serie de funciones necesarios para el manejo de
 * las peticiones relacionadas con Motor de Busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class SearchController extends Controller
{
    /**
     * Esta función retorna un listado de resultado del motor de busqueda
     * con los hoteles relacionados a un destino que el usuario a escogido.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function ListSearchAction($countryCode,$type, $slug, Request $request)
    {
        if ($this->get('translator')->trans($type, [], 'location') == $type)
            return $this->render('TwigBundle:Exception:error404.html.twig');

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

        $command = new getPorpertyByLocationCommand($data);
        $propertyList = $this->get('CommandBus')->execute($command);

        $command = new GetLocationHomeCommand();
        $location = $this->get('CommandBus')->execute($command);

        $response = $propertyList->getData();
        $response["location"] = $location->getData();
        $response["search"] = $data;

        return $this->render(
			'NavicuInfrastructureBundle:Web:listSearch.html.twig',
            ['data' => json_encode($response)]
        );
	} 

    /**
     * Esta función retorna un listado de resultado del motor de busqueda
     * con los hoteles relacionados a un destino que el usuario a escogido.
     * Petición Asincrona.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
	public function asincListSearchAction(Request $request)
    {
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new getPorpertyByLocationCommand($data);
			$response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 200) {
				$response = $response->getData();
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
     * Esta función retorna una serie de habitaciones con sus servicios
     * para un establecimiento por medio del motor de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @version Updated: 29-06-15
     */
    public function asincRoomsSearchOfPropertyAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);

            $command = new getRoomByPropertyCommand($data);
            $response = $this->get('CommandBus')->execute($command);

			if ($response->getStatusCode() == 200)
				return new JsonResponse($response->getData(), $response->getStatusCode());

			return new JsonResponse("null");
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * Esta función retorna la información de un establecimiento
     * para mostrarla en la ficha del establecimiento en motor
     * de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function propertySearchDetailsAction($slug, Request $request)
    {
		$countryCode = "VE";
		$data = array(
			"slug" => $slug,
			"startDate" => $request->query->get("startDate"),
			"endDate" => $request->query->get("endDate"),
			"page" => 1
		);

		$data["adult"] = is_null($request->query->get('adult')) ? 2 : $request->query->get("adult");
		$data["room"] = is_null($request->query->get('room')) ? 1 : $request->query->get("room");
		$data["kid"] = is_null($request->query->get('kid')) ? 0 : $request->query->get("kid");
		$data["countryCode"] = $countryCode;

		// Si existe un error en el inventario se debe mostrar un error
		$errorInventory = $request->attributes->get('error') ? true : false;

		$command = new PropertySearchDetailsCommand($data);
		$response = $this->get('CommandBus')->execute($command);


		if ($response->getStatusCode() == 200) {
			$dataResponse = $response->getData();

			$dataResponse["search"] = $data;

			return $this->render('NavicuInfrastructureBundle:Web:property.html.twig',
				array(
					'data' => json_encode($dataResponse),
					'errorInventory' => $errorInventory
				)
			);
		}

		return $this->render('TwigBundle:Exception:error404.html.twig');
    }

    /**
     * Esta función retorna un conjunto de establecimiento dada unas
     * coordenadas.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
	public function asincPropertiesMapAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			if (is_null($data["perimeter"])) {
				$command = new GetLocationMapCommand($data);
			} else {
				$command = new getPropertyByCoordenateCommand($data);
			}
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else {
			return new Response('Not Found',404);
		}
	}

	/**
	 * Esta función retorna la lista de los destinos en al cliente.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @param Request $request
	 * @return Array
	 */
	public function asyncDestinyOfLocationAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {

			$command = new GetDestinyOfLocationCommand();
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else {
			return new Response('Not Found',404);
		}
	}

	/**
	 * Esta función retorna para el manejo de una lista de destinos
	 * por medio de autocompletado.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @param Request $request
	 * @return Array
	 */
	public function asyncAutoCompletedDestinyAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new GetAutoCompletedDestinyCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else {
			return new Response('Not Found',400);
		}
	}

	/**
	 * Esta función retorna para el manejo de una lista de destinos
	 * por medio de autocompletado.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @param Request $request
	 * @return Array
	 */
	public function testPaginationAction(Request $request)
	{
		$rep = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');
		$data = $rep->testReservation();
		return new Response(json_encode($data));
	}
}
