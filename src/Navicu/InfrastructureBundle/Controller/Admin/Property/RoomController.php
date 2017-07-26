<?php
namespace Navicu\InfrastructureBundle\Controller\Admin\Property;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Navicu\Core\Application\UseCases\Admin\GetRooms\GetRoomsCommand;
use Navicu\Core\Application\UseCases\Admin\SetRoom\SetRoomCommand;
use Navicu\Core\Application\UseCases\Admin\GetRoomData\GetRoomDataCommand;
use Navicu\Core\Application\UseCases\Admin\RemoveRoom\RemoveRoomCommand;

class RoomController extends Controller
{

    /**
     * Esta función es usada para el manejo de la vista de habitaciones
     * de un establecimiento.
     *
	 * @author Helen Mercatudo <hmercatudo@navicu.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $slug
     * return Twig
     */
    public function roomViewAction(Request $request, $slug)
    {

        $command = new GetRoomsCommand($slug);
        $response = $this->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200) {
            return $this->render(
                'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/listRooms.html.twig',
                array('data' => json_encode($response->getData()),"slugTemp" => $slug)
            );
        } else {
            return new Response("Resource Not Found", 404);
        }
    }

    /**
     * Esta función es usada para el manejo de la vista de
     * información de habitación de un establecimiento.
     *
	 * @author Helen Mercatudo <hmercatudo@navicu.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $slug
     * return Twig
     */
    public function roomSaveAction(Request $request, $slug, $id)
    {
		$data["slug"] = $slug;
		$data["id"] = $id;
		if($request->isXmlHttpRequest()) { // Guardar la Habitación
			$data["data"] = json_decode($request->getContent(),true);
			$command = new SetRoomCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else { // Buscar la información de una habitación
			$command = new GetRoomDataCommand($data);
			$response = $this->get('CommandBus')->execute($command);
			if ($response) {
				return $this->render(
					'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/editRoom.html.twig',
					array('data' => json_encode($response),"slugTemp" => $slug)
				);
			} else {
				return new Response("Resource Not Found", 404);
			}
		}
    }

	/**
     * Esta función es usada para eliminar una habitación de un
     * establecimiento.
     *
	 * @author Helen Mercatudo <hmercatudo@navicu.com>
	 * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param Request $slug
     * return Twig
     */
    public function roomDeleteAction(Request $request)
    {
		if($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);
			$command = new RemoveRoomCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(), $response->getStatusCode());
		}
    }
}
