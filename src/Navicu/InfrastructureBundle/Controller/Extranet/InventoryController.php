<?php
namespace Navicu\InfrastructureBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\PropertyInventory\Grid\InventoryData\InventoryDataCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\Grid\DailyUpdate\DailyUpdateCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\GetDataMassLoad\GetDataMassLoadCommand;

use Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\SetDataMassLoad\SetDataMassLoadCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\SlugSecurity\SlugSecurityCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\InventoryLogs\SearchOfLogs\SearchOfLogsCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\InventoryLogs\SearchLogsFile\SearchLogsFileCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\IncludeIntoSession\IncludeIntoSessionCommand;

/**
 * Clase GridController
 *
 * Se define una clase y una serie de funciones necesarios para el manejo de
 * las peticiones del inventario de un establecimiento (carga masiva y grilla).
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 07/10/2015
 */
class InventoryController extends Controller
{

    public function gridIndexAction($slug, $date)
    {
        $response["slug"] = $slug;
        $response["startDate"] = date('Y-m-d');
        $response["userSession"] = $this->get("SessionService")->userOwnerSession();
        $response['data'] = (isset($date)) ? $date : "null";

        /*Valida que la fecha tenga formato mm-dd-YYYY*/
        $invalid = false;
        if($response['data'] != "null"){
            if(!preg_match('/[0-9]{2}-[0-9]{2}-[0-9]{4}/', $response['data']))
            {
                $invalid = true;
            }else{
                /*Se valida que sea una fecha valida*/
                $datePartial = explode("-", $response['data']);
                $invalid = !checkdate($datePartial[1], $datePartial[0], $datePartial[2]);
                
            }
        }
        /*Si no es una fecha valida o no tiene formato valido se retorna 400*/
        if($invalid)
            return 400;
        
        $command = new SlugSecurityCommand($response);
        $validateSlug = $this->get('CommandBus')->execute($command);

        if ($validateSlug) {

            $command = new IncludeIntoSessionCommand($response);
            $this->get('CommandBus')->execute($command);
            return $this->render(
                'NavicuInfrastructureBundle:Extranet\Inventory:inventory.html.twig',
                $response
            );
        } else {
            return 404;
        }
    }

    /**
     * Esta función es usada para devolver el contenido de la grilla a través de un json
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $slug
     */
    public function apiInventoryDailyAction(Request $request, $slug)
    {
        if ($request->isXmlHttpRequest()) {

			$data = json_decode($request->getContent(),true);
            $data["slug"] = $slug;
            $data["userSession"] = $this->get("SessionService")->getUserSession();

			$command = new InventoryDataCommand($data);
			$response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());

        } else {

            return new Response('Not Found',404);

        }
    }

    /**
     * Esta función es usada para el manejo de peticiones referentes
     * al ingreso y actualización de dailyPack y dailyRoom.
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function apiDailyUpdateAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {

			$data["data"] = json_decode($request->getContent(),true);
            $data["id"] = $id;
            $data["userSession"] = $this->get("SessionService")->userOwnerSession();

			$command = new DailyUpdateCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response->getStatusCode() == 201)
				return new JsonResponse($response->getData());
			else
				return new JsonResponse($response->getData(), $response->getStatusCode());

        } else {

            return new Response('Not Found',404);

        }
	}

    /**
     * La siguiente función se encarga de obtener los datos necesarios
     * para el procesar la carga másiva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param string $slug
     * @version 07/10/2015
     */
    public function getDataMassLoadAction($slug)
    {
        $command = new GetDataMassLoadCommand(
            $slug,
            $this->get("SessionService")->userOwnerSession());

        //$response["userSession"] = $this->get("SessionService")->userOwnerSession();
        $response = $this->get('CommandBus')->execute($command)->getData();

        $array = [];
        $array['slug'] = $slug;
        $this->get('CommandBus')->execute(new IncludeIntoSessionCommand($array));

        return $this->render(
                'NavicuInfrastructureBundle:Extranet\MassLoad:load.html.twig',
                 array(
                     "calendars" => $this->get('CalendarService')->getCalendars(),
                     "today" => getdate(),
                     "data" => json_encode($response))
            );
    }

    /**
     * La siguiente función se encarga de cargar los datos
     * para realizar una carga másiva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @param string $slug
     * @version 13/10/2015
     */
    public function setDataMassLoadAction(Request $request, $slug)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            $command = new SetDataMassLoadCommand($slug, $this->get("SessionService")->userOwnerSession(), $data);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse(
                    json_encode($response->getData()),
                    $response->getStatusCode());
            else
                return new JsonResponse(
                    json_encode($response->getData()),
                    $response->getStatusCode());
        } else {
            return new JsonResponse("Bad Request", 404);
        }
    }

    /**
     * Esta función es usada para el manejo de peticiones para la busqueda de
     * los logs.
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function apiSearchLogsAction(Request $request, $slug, $page)
    {
        if ($request->isXmlHttpRequest()) {

			$data["data"] = json_decode($request->getContent(),true);
			$data["slug"] = $slug;
            $data["userSession"] = $this->get("SessionService")->getUserSession();

			$command = new SearchOfLogsCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response->getStatusCode() == 201) {
				$auxResponse["data"] = $response->getData();
				$auxResponse["page"] = $this->get('Pagination')->pagination($auxResponse["data"], $page);
				return new JsonResponse($auxResponse);
			} else {
				return new JsonResponse($response->getData(), $response->getStatusCode());
			}

        } else {

            return new Response('Not Found',404);

        }
	}

    /**
     * Esta función es usada para el manejo de peticiones para la busqueda de
     * la información guardada en un archivo logs, para la vista.
     *
     * @author Helen Mercatudo <helen.mercatudo@gmail.com>
     * @author Currently Working: Helen Mercatudo
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function logsViewsAction($slug)
    {
        $response["slug"] = $slug;
        $response["userSession"] = $this->get("SessionService")->userOwnerSession();

        $command = new SlugSecurityCommand($response);
        $validateSlug = $this->get('CommandBus')->execute($command);

        if ($validateSlug) {

            $command = new IncludeIntoSessionCommand($response);
            $this->get('CommandBus')->execute($command);
            return $this->render(
                'NavicuInfrastructureBundle:Extranet\History:historyOwner.html.twig',
                $response
            );
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * Esta función es usada para el manejo de peticiones para la busqueda de
     * la información guardada en un archivo logs, para la vista de los cambios en la grilla.
     *
     * @author Helen Mercatudo <helen.mercatudo@gmail.com>
     * @author Currently Working: Helen Mercatudo
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function logsInventoryAction($slug, $logFile)
    {
        $response["slug"] = $slug;
        $response['logfile'] = $logFile;
        $response["userSession"] = $this->get("SessionService")->userOwnerSession();

        $command = new SlugSecurityCommand($response);
        $validateSlug = $this->get('CommandBus')->execute($command);

        if ($validateSlug) {

            $command = new IncludeIntoSessionCommand($response);
            $this->get('CommandBus')->execute($command);
            return $this->render(
                'NavicuInfrastructureBundle:Extranet\History:historyInventory.html.twig',
                $response
            );
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * Esta función es usada para el manejo de peticiones para la busqueda de
     * la información guardada en un archivo logs, para la vista de los cambios en la carga masiva.
     *
     * @author Helen Mercatudo <helen.mercatudo@gmail.com>
     * @author Currently Working: Helen Mercatudo
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function logsLoadAction($slug, $logFile)
    {
        $data["slug"] = $slug;
        $data["logFile"] = $logFile;
        $data["userSession"] = $this->get("SessionService")->userOwnerSession();

        $command = new SearchLogsFileCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 201) {

            $service_calendar = $this->get('CalendarService');
            $response =$response->getData();
            $response["slug"] = $slug;
            $response["logFile"] = $logFile;

            $response['calendar_marked_days'] =
                 $service_calendar->getCalendarWithMarkedDays($response["dates"]);
            $response['cells_per_month'] = $service_calendar::CELLS_PER_MONTH;

            return $this->render(
                'NavicuInfrastructureBundle:Extranet\History:historyMassload.html.twig',
                $response
            );
        } else {
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }

    }

    /**
     * Esta función es usada para el manejo de peticiones para la busqueda de
     * la información guardada en un archivo logs, por medio de su nombre.
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $id
     * return Array
     */
    public function apiLogsFileAction(Request $request, $logFile, $slug)
    {
        if ($request->isXmlHttpRequest()) {

			$data["slug"] = $slug;
			$data["logFile"] = $logFile;
            $data["userSession"] = $this->get("SessionService")->userOwnerSession();

			$command = new SearchLogsFileCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response->getStatusCode() == 201) {
				return new JsonResponse($response->getData());
			} else {
				return new JsonResponse($response->getData(), $response->getStatusCode());
			}

        } else {

            return new Response('Not Found',404);

        }
	}

    /**
     * Esta función es usada solo por el admin para el manejo de peticiones para la busqueda de
     * las habitaciones para borrar sus paquetes. 
     *
     * @param array $slug
     * @return object Response
     *
     * @author Isabel Nieto <isabelcndgmail.com>
     * @version 09/05/2016
     */
    public function dropInventoryMassLoadAction($slug) {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $array = [];

            $command = new GetDataMassLoadCommand(
                $slug,
                $this->get("SessionService")->userOwnerSession());
            $response = $this->get('CommandBus')->execute($command)->getData();

            $array['slug'] = $slug;
            $this->get('CommandBus')->execute(new IncludeIntoSessionCommand($array));

            return $this->render(
                'NavicuInfrastructureBundle:Extranet\MassLoad:dropInventory.html.twig',
                array(
                    "calendars" => $this->get('CalendarService')->getCalendars(),
                    "today" => getdate(),
                    "data" => json_encode($response))
            );
        }
        return new Response('Not Found',404);
    }
}