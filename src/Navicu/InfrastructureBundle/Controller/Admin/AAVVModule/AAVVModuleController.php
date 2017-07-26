<?php
namespace Navicu\InfrastructureBundle\Controller\Admin\AAVVModule;


use Navicu\Core\Application\UseCases\Admin\AAVVModule\ListPayments\ListPaymentsCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteParameters\DeleteParametersCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\EditParameters\EditParametersCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\ListParameters\ListParametersCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\ParametersLog\ParametersLogCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\RegisterAAVV\RegisterAAVVCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\ChangeStatusAAVV\ChangeStatusAAVVCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteAAVV\DeleteAAVVCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAgenciesInRegistrationProcess\GetAgenciesInRegistrationProcessCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\Search\GetListAffiliatedAAVV\GetListAffiliatedAAVVCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAffiliatesDetail\GetAffiliatesDetailCommand;
use Navicu\Core\Application\UseCases\AAVV\Reservation\GetListReservation\GetListReservationCommand;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\UpdatePayment\UpdatePaymentCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controlador para las funcionalidades de administracion sobre las agencias de viajes registradas
 */
class AAVVModuleController extends Controller
{
    /**
     * Esta función es usada para devolver un listado
     * de las agencias de viajes afiliadas.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @return Json
     */
    public function getAffiliatedListAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

			$command = new GetListAffiliatedAAVVCommand($data);
			$responseCommand = $this->get('CommandBus')->execute($command);

            return new JsonResponse($responseCommand->getData(), $responseCommand->getStatusCode());

        } else {
            $data["order"] = 1;
            $command = new GetListAffiliatedAAVVCommand($data);
            $responseCommand = $this->get('CommandBus')->execute($command);

            $response= $responseCommand->getData();

            return $this->render(
                'NavicuInfrastructureBundle:Admin:aavv/affiliate/listAffiliate.html.twig',
                ["data"=>json_encode($response)]

            );
        }
    }

    /**
     * Esta función es usada para devolver un listado
     * de las reservas de una agencia de viajes afiliadas.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @return Json
     */
    public function asyncGetAffiliatesReservationListAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            $data["user"] = $this->get("SessionService")->getUserSession();

            $command = new GetListReservationCommand($data);
            $responseCommand = $this->get('CommandBus')->execute($command);

            $response = null;
            if ($responseCommand->getStatusCode() == 200) {
                $data = $responseCommand->getData();
                if (!empty($data)) {
                    $response["reservations"] = $data["properties"];
                    $response["page"] = $data["page"];
                }
                return new JsonResponse($response);
            }

            return new JsonResponse($response, $responseCommand->getStatusCode());

        } else {
            return new Response('Not Found', 404);
        }
    }

    /**
     * Esta función es usada para devolver un listado
     * de las agencias de viajes afiliadas.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @return Json
     */
    public function getAffiliatesDetailAction(Request $request, $slug)
    {
        $data["slug"] = $slug;

        $command = new GetAffiliatesDetailCommand($data);
        $responseCommand = $this->get('CommandBus')->execute($command);

        $response = $responseCommand->getData();

        return $this->render(
            'NavicuInfrastructureBundle:Admin:aavv/affiliate/details/detailsAAVV.html.twig',
            ["data"=>json_encode(["data"=>$response])]
        );

    }

    /**
     * despliega la vista de la lista de agencias de viajes en proceso de registro y responde a las solicitudes de busqueda
     *
     * @author Gabriel Camacho
     *
     * @param $page
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function listAAVVAction(Request $request, $page)
    {
        $data = [];
        if ($request->isXmlHttpRequest())
            $data = json_decode($request->getContent(), true);

        $command = new GetAgenciesInRegistrationProcessCommand($data);
        $rcb = $this->get('CommandBus')->execute($command);

        $response['agencies'] = $rcb->getData();
        $response["pagination"] = $this
            ->get('Pagination')
            ->pagination(
                $response['agencies'],
                $page,
                50
            );

        if ($response["pagination"] == 0) {
            $response["pagination"] = [
                'current' => 1,
                'pageCount' => 1,
                'totalCount' => count($response['agencies']),
            ];
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                [
                    'meta' => [
                        'code' => $rcb->getStatusCode(),
                        'message' => null
                    ],
                    'data' => $response,
                ]
            );
        } else {
            return $this->render(
                'NavicuInfrastructureBundle:Admin:aavv/registration/listRegistration.html.twig',
                ["data"=>json_encode($response)]
            );
        }
    }

    /**
     * usada para dar de alta una agencia de viaje
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse|Response
     */
    public function registerAAVVAction(Request $request, $slug)
    {
        if (!$request->isXmlHttpRequest())
            return new Response('Not Found', 404);

        $command = new RegisterAAVVCommand(['slug' => $slug]);
        $rcb = $this->get('CommandBus')->execute($command);

        return new JsonResponse($rcb->getMessage(), $rcb->getStatusCode());
    }

    /**
     * usada para cambiar de estado una agencia de viaje registrada
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function changeStatusAAVVAction(Request $request)
    {
        if (!$request->isXmlHttpRequest())
            return new Response('Not Found', 404);

        $data = json_decode($request->getContent(), true);

        $command = new ChangeStatusAAVVCommand($data);
        $rcb = $this->get('CommandBus')->execute($command);

        return new JsonResponse($rcb->getMessage(), $rcb->getStatusCode());
    }

    /**
     * usada para eliminar una gencia de viaje que aun no ha sido dada de alta
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse|Response
     */
    public function deleteAAVVAction(Request $request, $slug)
    {
        if (!$request->isXmlHttpRequest())
            return new Response('Not Found', 404);

        $command = new DeleteAAVVCommand(['slug' => $slug]);
        $rcb = $this->get('CommandBus')->execute($command);

        return new JsonResponse($rcb->getMessage(), $rcb->getStatusCode());
    }

    public function listPaymentsAction(Request $request, $page)
    {
        $data = json_decode($request->getContent(), true);

        $command = new ListPaymentsCommand($data);
        $payments = $this->get('CommandBus')->execute($command);

        $response['payments'] = $payments->getData()['payments'];
        $response['aavvCount'] = $payments->getData()['aavvCount'];
        //die(var_dump($payments->getData()));
        $response["pagination"] = $this
            ->get('Pagination')
            ->simplePagination(
                $response['payments'],
                $page,
                10
            );

        return new JsonResponse(
            [
                'meta' => [
                    'code' => $payments->getStatusCode(),
                    'message' => null
                ],
                'data' => $response,
            ]
        );
    }

    public function editPaymentAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $command = new UpdatePaymentCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function directPaymentsAction()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Admin:invoicing/directPayments/directPayments.html.twig'
        );
    }


    /**
     * Acción usada para generar excel de aavv en proceso de registro ($status = 0) o afiliadas ($status = 1)
     *
     * @param $status
     * @return Response
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @version 04-11-2016
     */
    public function downloadExcelAavvAction($status)
    {
        if ($status == 0) {

            $command = new GetAgenciesInRegistrationProcessCommand([]);
            $rcb = $this->get('CommandBus')->execute($command);
            $response = $this->get('ExcelGenerator')->getExcelInProcessAAVV($rcb->getData());

        } elseif ($status == 1) {

            $command = new GetListAffiliatedAAVVCommand(['order' => 1]);
            $rcb = $this->get('CommandBus')->execute($command);
            $data = $rcb->getData();
            $response = $this->get('ExcelGenerator')->getExcelAffiliatesAAVV($data['data']);

        } else {
            return new response('Not Found', 404);
        }

        return $response;
    }

    public function listParametersAction()
    {

        $command = new ListParametersCommand();
        $response = $this->get('CommandBus')->execute($command);

        return $this->render(
            'NavicuInfrastructureBundle:Admin:aavv/parameters.html.twig',
            array("data" => json_encode($response->getData()))
        );
    }

    public function editParametersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $command = new EditParametersCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function deleteParametersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $command = new DeleteParametersCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function getParameterLogsAction(Request $request)
    {

        $command = new ParametersLogCommand();
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function redirectAAVVAction($subdomainRequest)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->redirect(
            $this->generateUrl('navicu_subdomain_access_admin',[
                'subdomain' => $subdomainRequest,
                'username' => $user->getUsername()
            ])
        );
    }
}
