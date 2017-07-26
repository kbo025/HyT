<?php

namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\AAVV\Reservation\GetListReservation\GetListReservationCommand;
use Navicu\Core\Application\UseCases\AAVV\Reservation\GetReservationDetail\GetReservationDetailCommand;
use Navicu\Core\Application\UseCases\AAVV\Reservation\RenewReservation\RenewReservationCommand;
use Navicu\Core\Application\UseCases\AAVV\Reservation\CreateReservation\CreateReservationCommand;
use Navicu\Core\Application\UseCases\Admin\GetBankList\GetBankListCommand;
use Navicu\Core\Application\UseCases\Reservation\setClientProfile\setClientProfileCommand;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Application\UseCases\AAVV\Reservation\SetDataReservation\SetDataReservationCommand;
use Navicu\Core\Domain\Model\Entity\AAVVReservationGroup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Navicu\Core\Application\UseCases\AAVV\Reservation\GetConfirmReservation\GetConfirmReservationCommand;
use Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure\GetResumenReservationStructureCommand;
use Navicu\Core\Application\UseCases\Reservation\ProcessReservation\ProcessReservationCommand;

/**
 * La clase representa el conjunto de envido y recepción de datos
 * referentes al proceso de reserva mediante una AAVV
 *
 *
 * Class ReservationController
 * @package Navicu\InfrastructureBundle\Controller\AAVV
 */
class ReservationController extends Controller
{

    /**
     * Funcion encargada de generar una reserva a paritr de los datos ingresados
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function CreateReservationAction(Request $request)
    {
        $aavv = (strcmp(gettype(CoreSession::getUser()), "string") == 0) ?
            null :
            (method_exists(CoreSession::getUser()->getAavvProfile(),"getAavv")) ?
                CoreSession::getUser()->getAavvProfile()->getAavv() :
                null;

        if (!is_null($aavv)) {
            //uso el servicio PaymentGatewayService para obtener el PaymentGateway del tipo de pago solicitado
            $data = json_decode($request->getContent(), true);

            if ($data['data']['reservation_type']==0)
                $paymentGateway = $this->get('PaymentGatewayService')->getPaymentGateway(2);
            else
                $paymentGateway = $this->get('PaymentGatewayService')->getPaymentGateway(5);

            $paymentGateway->setCurrency("VEF", $this->get('RepositoryFactory'));

            if (isset($data)) {
                $data['data']['domain'] = $this->getRequest()->getHost();
                $data['data']['paymentGateway'] = $paymentGateway;
                // Llamamos a la generacion de la reserva por medio del caso de uso
                $command = new CreateReservationCommand($data['data']);
                $response = $this->get('CommandBus')->execute($command);
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }
            return new JsonResponse("Not Found GetReservationDetailCommandData", 400);
        }
        return new JsonResponse("Not Found Agency", 400);
    }

    /**
     * Funcion encargada de renderizar la vista mediante el id del grupo de la reserva realizada por la aavv
     *
     * @param string $location , nombre/numero que identifica a donde pertenece la reserva realizada
     * @param string $public_group_id , id del grupo de la reserva a mostrar
     * @param null $hashUrl
     * @return Response
     * @version 05/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function GetReservationConfirmDataAction($location, $public_group_id)
    {
        $aavv = (strcmp(gettype(CoreSession::getUser()), "string") == 0) ?
            null :
            (method_exists(CoreSession::getUser()->getAavvProfile(),"getAavv")) ?
                CoreSession::getUser()->getAavvProfile()->getAavv() :
                null;

        if (!is_null($aavv)) {
            $data['data']['public_group_id'] = $public_group_id;
            $data['data']['location'] = $location;
            $data['data']['aavv'] = $aavv;

            if (isset($data)) {
                // Llamamos a la generacion de la reserva por medio del caso de uso
                $command = new GetConfirmReservationCommand($data['data']);
                $response = $this->get('CommandBus')->execute($command);
                return $this->render('NavicuInfrastructureBundle:AAVV/reservation/confirm:ok_reservation.html.twig',
                        array('data' => json_encode($response->getData()))
                );

            }
        }
        return $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');
    }

    public function getListPropertiesAction(Request $request)
    {
        $data = array(
            //"slug" => $request->query->get("slug"),
            "slug" => "isla-de-margarita",
            "startDate" => $request->query->get("startDate"),
            "endDate" => $request->query->get("endDate"),
            "page" => 1,
        );

        $data["adult"] = is_null($request->query->get('adult')) ? 2 : $request->query->get("adult");
        $data["room"] = is_null($request->query->get('room')) ? 1 : $request->query->get("room");
        $data["kid"] = is_null($request->query->get('kid')) ? 0 : $request->query->get("kid");
        //$data["countryCode"] = $countryCode;
        $data["countryCode"] = "VE";
        $data["type"] = "isla";
        return $this->render('NavicuInfrastructureBundle:AAVV/searchAAVV:listProperties.html.twig', ["data"=>json_encode($data)]);
    }

    /**
     * esta acción renueva una reserva bloqueada
     */
    public function renewReservationAction(Request $request)
    {
        //$data = json_decode($request->getContent(),true);
        $command = new RenewReservationCommand(['time' => 720]);
        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse($response->getData(),$response->getStatusCode());
    }
    
    /**
     * La petición recibe los datos a validar en la reservar
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return Response
     */
    public function SetDataReservationAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $command = new SetDataReservationCommand();
            $command->set('ip', $request->getClientIp());

            if (isset($_POST['properties']))
                $command->set('properties', $_POST['properties']);

            if (isset($_POST['checkIn']))
                $command->set('checkIn', new \DateTime($_POST['checkIn']));

            if (isset($_POST['checkOut']))
                $command->set('checkOut', new \DateTime($_POST['checkOut']));

            if (isset($_POST['location']))
                $command->set('location', $_POST['location']);

            /* Tipo de reserva.
             * reservation_type = 0 <- pre-reserva
             * reservation_type = 2 <- reserva */
            if (isset($_POST['reservation_type']))
                $command->set('reservation_type', $_POST['reservation_type']);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 200) {
                return $this->render('NavicuInfrastructureBundle:AAVV/searchAAVV:dataReservations/dataReservations.html.twig',
                        [
                            'data' => json_encode($response->getData()),
                        ]
                );
            } else {
                // En caso de error se redireccion a frontEnd con el parametro de warning con valores 1 - 4
                return $this->redirect($this->generateUrl('navicu_aavv_home', [
                        'subdomain' => $this->get('request')->get('subdomain'),
                        'warning' => $response->getData()
                    ]));
            }
        }
    }

    /**
     * Esta función es usada para devolver de forma async el
     * listado de reservas asignada a una Agencia de Viaje.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @return Response
     */
    public function asyncListReservationAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {

            $data = json_decode($request->getContent(), true);
            $data["user"] = $this->get("SessionService")->getUserSession();
            $data["slug"] = $this->get('request')->get('subdomain');

            if (!$data["user"])
                return new JsonResponse(null, 404);

            $command = new GetListReservationCommand($data);
            $commandResponse = $this->get('CommandBus')->execute($command);

            if ($commandResponse->isOk()) {
                return new JsonResponse($commandResponse->getData());
            } else {
                return new JsonResponse($commandResponse->getData(), 404);
            }
        }
    }

    public function getListReservationAction(Request $request)
    {
        return $this->render('NavicuInfrastructureBundle:AAVV:listReservation/index.html.twig');
    }

    /**
     * Funcion encargada de devolver la informacion de la reserva ingresada
     *
     * @param $idReservation
     * @return JsonResponse
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 2016/10/13
     */
    public function getDetailReservationAction($idReservation)
    {
        if (is_null($idReservation))
            return $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');

        $command = new GetReservationDetailCommand($idReservation);
        $response = $this->get('CommandBus')->execute($command);
                
        return $this->render('NavicuInfrastructureBundle:AAVV/listReservation/detailsReservation:detailsReservation.html.twig',
            [ 'data' => json_encode($response->getData()) ]);
    }

    public function detailsReservationAction()
    {
        return $this->render('NavicuInfrastructureBundle:AAVV:listReservation/detailsReservation/detailsReservation.html.twig');
    }

    /**
     * Funcion encargada de visualizar el resumen de la reserva en pdf
     *
     * @param $location
     * @param $public_group_id
     * @param $hashUrl
     * @param null $owner
     * @return Response
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 13/12/2016
     */
    public function generateResumeReservationPdfAAVVAction($location, $public_group_id, $hashUrl, $owner = 0)
    {
        /*$aavv = (strcmp(gettype(CoreSession::getUser()), "string") == 0) ?
            null :
            (method_exists(CoreSession::getUser()->getAavvProfile(),"getAavv")) ?
                CoreSession::getUser()->getAavvProfile()->getAavv() :
                null;*/

        $data['data']['public_group_id'] = $public_group_id;
        $data['data']['location'] = $location;
        $data['data']['owner'] = ($owner == 0) ? false : true;
//        $data['data']['aavv'] = $aavv;
        // Llamamos a la visualizacion de la reserva por medio del caso de uso
        $command = new GetConfirmReservationCommand($data['data']);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();
        //die(var_dump($dataResponse));
        /*$aavv = $dataResponse['aavv'];
        $dataToPdf['isCustomize'] = $aavv->isCustomize();
        $dataToPdf['customize'] = $aavv->getCustomize();
        $dataToPdf = array_merge($dataToPdf, $dataResponse);*/

        // Si todo salio bien devolver la vista del pdf
        //if (!is_null($aavv)) {
            if ( ($response->isOk()) AND (strcmp($hashUrl, $dataResponse['hash_url']) == 0) ) {
                // Cargamos variables necesarias en la vista
                $customizedData['grayColor1'] = "#3d3d3b";
                $customizedData['grayColor2'] = '#808080';
                $customizedData['grayFooter'] = "#EDEDED";
                $customizedData['fonts'] =  $_SERVER['DOCUMENT_ROOT'].'/fonts/font-navicu/font-lato/';
                //$customizedData['domain'] = $this->getRequest()->getHost();

                if (!is_null($dataResponse['logo']))
                    $customizedData['logo'] = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_original/'.$dataResponse['logo'];
                else
                    $customizedData['logo'] = $_SERVER['DOCUMENT_ROOT'].'/images/white-navicu.png';
                $customizedData['logoVicander'] = $_SERVER['DOCUMENT_ROOT'].'/images/trip-vicander-logo-pdf.png';
                $customizedData['stars'] = $_SERVER['DOCUMENT_ROOT'].'/images/navicu/star.png';
                
                // Unimos las variables obtenidas del caso de uso con la customizacion del correo
                $dataToPdf = array_merge($dataResponse, $customizedData);
                $pdf = $this->get('pdfCreator')->generatePdfFromHtml(
                    $this->render('NavicuInfrastructureBundle:AAVV/reservation:resumeReservationPdf.html.twig', $dataToPdf)->getContent()
                );
                return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
            }
            return new Response($response->getMessage(), $response->getStatusCode());
        //}
    }

    public function confirmReservationAAVVAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) { 
            //obtengo y decodifico el JSON
            $dataRequest = json_decode($request->getContent(), true);

            //comando
            $command = new GetResumenReservationStructureCommand(['id' => $dataRequest['idReservation']]);
            $response = $this->get('CommandBus')->execute($command);
            if(!$response->isOk())
                return new JsonResponse($response->getData(),$response->getStatusCode());

            $data = $response->getData();

            //verifico que el tipo y status de la reserva sea el correcto
            if (($data['reservationPaymentType']!=2 && $data['reservationPaymentType']!=4) || $data['statusReservation']!=0)
                return new JsonResponse(['message' => 'not_found','reservationPaymentType' => $data['reservationPaymentType'],'statusReservation' => $data['statusReservation'],], 404);

            $paymentGateway = $this
                ->get('PaymentGatewayService')
                ->getPaymentGateway($data['reservationPaymentType']);
            $currency = $request->getSession()->get('alphaCurrency');

            //creo el comando, le paso la data que le corresponde y el paymentgateway obtenido
            $command = new ProcessReservationCommand($dataRequest);
            $command
                ->set('isAavv',true)
                ->set('paymentGateway',$paymentGateway)
                ->set('ip',$request->getClientIp())
                ->set('currency',$currency);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(),$response->getStatusCode());            
        }
        return new Response("Not Found", 404);
    }

    public function getBankListAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $command = new GetBankListCommand();
            $commandResponse = $this->get('CommandBus')->execute($command);

            if ($commandResponse->isOk()) {
                return new JsonResponse($commandResponse->getData());
            } else {
                return new JsonResponse(null, 404);
            }
        }
        return new Response("Not Found", 404);
    }
}
