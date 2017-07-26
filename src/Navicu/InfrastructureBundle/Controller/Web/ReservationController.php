<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\BanescoTDCPaymentGateway;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\Reservation\ReservationForm\PropertyReservationFormCommand;
use Navicu\Core\Application\UseCases\Reservation\CalculateRateReservation\CalculateRateReservationCommand;
use Navicu\Core\Application\UseCases\Reservation\ProcessReservation\ProcessReservationCommand;
use Navicu\Core\Application\UseCases\Reservation\setClientProfile\setClientProfileCommand;
use Navicu\Core\Application\UseCases\ClientInfoSession\ClientInfoSessionCommand;
use Navicu\Core\Application\UseCases\GetLocationHome\GetLocationHomeCommand;
use Navicu\Core\Application\UseCases\Reservation\GetResumenReservationStructure\GetResumenReservationStructureCommand;


/**
 * Class ReservationController
 * La siguiente entidad se encarga de procesar todas las peticiones y respuestas
 * referente al proceso de reserva
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @package Navicu\InfrastructureBundle\Controller\Web
 * @version 01/09/2015
 */
class ReservationController extends Controller
{
    //Esta función es totalmente de prueba debe ser borrada posteriormente
    public function testReservationAction()
    {
        $product = $this->getDoctrine()
            ->getRepository('NavicuDomain:Property')
            ->findBySlug('hotel-pinturillo');
        $response = array();
        $response['slug'] = $product->getSlug();
        $rooms = $product->getRooms();

        $response['rooms'] = array();
        $response['startDate'] = (new \DateTime())->format('d-m-Y');
        $response['endDate'] = (new \DateTime())->modify('+ 4 days')->format('d-m-Y');
        foreach ($rooms as $currentRoom) {
            $auxRoom = array();
            $auxRoom['idRoom'] = $currentRoom->getId();
            $auxRoom['name'] = $currentRoom->getType()->getTitle();
            $auxRoom['packages'] = array();

            foreach ($currentRoom->getPackages() as $currentPack) {
                $auxPack = array();
                $auxPack['id'] = $currentPack->getId();
                $auxPack['name'] = $currentPack->getType()->getTitle();

                array_push($auxRoom['packages'], $auxPack);
            }
            array_push($response['rooms'],$auxRoom);
        }

        return $this->render('NavicuInfrastructureBundle:Email:reservationInfo.html.twig', $response);
    }

    /**
     * La siguiente funcion se encarga de recibir los datos del formulario
     * de la ficha del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse|Response
     * @version 01/09/2015
     */
    public function confirmationReservationAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $command = new PropertyReservationFormCommand();

            //verifico que el usuario logueado es el dueño de la reserva (Descomentar cuando haya login)
            $user = $this->get('SessionService')->getUserSession();
            $clientProfile = null;  
            if (!is_null($user)) {
                $clientProfile = $user->getClientProfile();
                if (!is_null($clientProfile)) {
                    $client = [
                        'email' => $clientProfile->getEmail(),
                        'fullName' => $clientProfile->getFullName(),
                        'identityCard' => $clientProfile->getIdentityCard(),
                        'phone' => $clientProfile->getPhone(),
                    ];
                } else
                    $client = null;
            } else
                 $client = null;

            $command = new PropertyReservationFormCommand([
                'slug' => isset($_POST['slug']) ? $_POST['slug'] : null,
                'checkIn' => isset($_POST['checkIn']) ? new \DateTime($_POST['checkIn']) : null,
                'checkOut' => isset($_POST['checkOut']) ? new \DateTime($_POST['checkOut']) : null,
                'numberAdults' => isset($_POST['numPerson']) ? $_POST['numPerson'] : null,
                'numberChild' => isset($_POST['numberChild']) ? $_POST['numberChild'] : null,  
                'rooms' => isset($_POST['rooms']) ? $_POST['rooms'] : null, 
                'client' => $clientProfile,
                'clientIp' => $request->getClientIp(),
                'alphaCurrency' => $request->getSession()->get('alphaCurrency'), 
            ]);
            
            $response = $this->get('CommandBus')->execute($command);
            $dataResponse = $response->getData();
            $dataResponse['client'] = $client;

            // Parametros que incluyen la informacion sobre si es persona natural o juridica
            $user = $this->get('SessionService')->userClientSession();
            $show_type = false;
            $clientIdentity = null;
            if (!is_null($user)) {
                // No ha cargado aun si es de tipo juridico o natural
                if (is_null($user->getTypeIdentity()))
                    $show_type = true;
                else
                    $show_type = false;
                $clientIdentity = $user->getTypeIdentity();
            }

            $dataResponse["show_type"] = $show_type;
            $dataResponse["typeIdentity"] = $clientIdentity;

            if ($response->getStatusCode() == 201) {
                return $this->render('NavicuInfrastructureBundle:Web:reservation.html.twig',
                    [
                        'bookingInformation' => json_encode($dataResponse),
                    ]);
            } else {
                return $this->forward(
                    'NavicuInfrastructureBundle:Web/Search:propertySearchDetails',array (
                        'slug' => $_POST['slug'],
                        'error' => true)
                    );
            }
        }
        return $this->redirect($this->get('router')->generate("navicu_homepage_temporal"));
    }

    /**
     * accion que despliega la vista de un a reserva realizada
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param String $PublicId
     * @return Response
     * @version 19/09/2015
     */
    public function resumeReservationAction($publicId)
    {
        $command = new GetResumenReservationStructureCommand(['id' => $publicId,]);
        $commandLocation = new GetLocationHomeCommand();
        $response = $this->get('CommandBus')->execute($command);
        $location = $this->get('CommandBus')->execute($commandLocation);

        if ($response->getStatusCode() == 201 && $location->getStatusCode() == 200) {
            return $this->render(
                'NavicuInfrastructureBundle:Web:resume.html.twig',
                [
                    'bookingInformation' => json_encode($response->getData()),
                    'locations' => json_encode($location->getData()),
                    'publicId' => $response->getData()['confirmationId'],
                    'currentUserCurrency' => $response->getData()['alphaCurrency'],
                    'isoCurrency' => $response->getData()['alphaCurrencyIso'],
                ]
            );
        } else
            return new Response('ERROR',500);
    }

    /**
     * La siguiente funcion recibe los datos requeridos para procesar la reserva y llama al CU que lo hace
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param Request $request
     * @return JsonResponse|Response
     * @version 03/09/2015
     */
    public function processReservationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            //obtengo y decodifico el JSON
            $data = json_decode($request->getContent(), true);

            $user = $this->getClientProfileForReservation($data);

            if(!$user)
                return new JsonResponse(null,403);

            //uso el servicio PaymentGatewayService para obtener el PaymentGateway del tipo de pago solicitado
            $paymentGateway = $this->get('PaymentGatewayService')->getPaymentGateway($data['paymentType']);
            //creo el comando, le paso la data que le corresponde y el paymentgateway obtenido
            $currency = $request->getSession()->get('alphaCurrency');
            $paymentGateway->setCurrency($currency,$this->get('RepositoryFactory'));
            $command = new ProcessReservationCommand($data);
            $command
                ->set('paymentGateway',$paymentGateway)
                ->set('user',$user)
                ->set('currency',$currency)
                ->set('ip',$request->getClientIp())
                ->set('decreaseAvailability',true);

            $response = $this->get('CommandBus')->execute($command);

            $user = $this->get('Security.context')->getToken()->getUser();
            $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
            $this->get('CommandBus')->execute($includeSession);

            return new JsonResponse($response->getData(),$response->getStatusCode());
        }
        return new Response("Método incorrecto",404);
    }

    private function getClientProfileForReservation($data)
    {
        $redsocial = isset($data['redSocial']) ?
            [
                'idSocial' => $data['redSocial']['idSocial'],
                'type' => $data['redSocial']['type'],
                'link' => isset($data['redSocial']['link']) ?
                    $data['redSocial']['link'] :
                    'default',
                'photo' => isset($data['redSocial']['photo']) ?
                    $data['redSocial']['photo'] :
                    'default',
                'ageRange' => isset($data['redSocial']['ageRange']) ?
                    $data['redSocial']['ageRange'] :
                    0,
            ] :
            null;

        $clientData = array_merge(
            $data['client'],
            [
                'password' => !empty($data['client']['pass1']) ? $data['client']['pass1'] : null,
                'redSocial' => $redsocial,
                'birthDate' => null,
                'idSocial' => null,
                'type' => null,
                'gender' => !empty($data['client']['gender']) ? $data['client']['gender'] : null,
                'typeIdentity' => !empty($data['client']['typeIdentity']) ? $data['client']['typeIdentity'] : null,
            ]
        );
        $command = new setClientProfileCommand($clientData);
        $response = $this->get('CommandBus')->execute($command);

        $client = $data['client'];
        if ($response->getStatusCode() == 201) {
            if ($this->get('SecurityService')->loginDirect(['userName'=>$client['email']], 'navicu_web')) {
                    $user = $this->get('Security.context')->getToken()->getUser();
                    $user = $user->getClientProfile();
                } else
                //return new JsonResponse(null,403);
                return false;
        } else {
            $user = $this->get('SessionService')->userClientSession($client['email']);
            if (is_null($user))
                return false;
                //return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return $user;
    }

    /**
     * recalcula el costo de una reserva cobrada en moneda nacional bajo ciertas condiciones para cobrar el 10% del import_request_variables
     * segun la providencia publicada en diciembre del 2016  
     */
    public function recalculateRateReservationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            //obtengo y decodifico el JSON
            $data = json_decode($request->getContent(), true);

            $command = new CalculateRateReservationCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(),$response->getStatusCode());
        }
        return new Response("Método incorrecto",404);
    }

    /**
     *  la siguiente funcion es usada para confirmar una reserva a traves de una transferencia
     *
     */
    public function processPaymentReservationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            //obtengo y decodifico el JSON
            $dataRequest = json_decode($request->getContent(), true);

            //comando
            $command = new GetResumenReservationStructureCommand(['id' => $dataRequest['idReservation']]);
            $response = $this->get('CommandBus')->execute($command);
            $data = $response->getData();

            //verifico que el usuario logueado es el dueño de la reserva (Descomentar cuando haya login)
            $user = $this->get('SessionService')->userClientSession($data['clientEmail']);
            if (is_null($user))
                return new Response('Forbidden', 403);

            //verifico que el tipo y status de la reserva sea el correcto
            if (($data['reservationPaymentType']!=2 || $data['reservationPaymentType']!=4) && $data['statusReservation']!=0)
                return new Response('Not Found', 404);

            //uso el servicio PaymentGatewayService para obtener el PaymentGateway del tipo de pago solicitado
            $paymentGateway = $this
                ->get('PaymentGatewayService')
                ->getPaymentGateway($data['reservationPaymentType']);
            $currency = $request->getSession()->get('alphaCurrency');
            $paymentGateway->setCurrency($currency,$this->get('RepositoryFactory'));

            //creo el comando, le paso la data que le corresponde y el paymentgateway obtenido
            $command = new ProcessReservationCommand($dataRequest);
            $command
                ->set('user',$user)
                ->set('paymentGateway',$paymentGateway)
                ->set('ip',$request->getClientIp())
                ->set('currency',$currency);

            $response = $this->get('CommandBus')->execute($command);

            $user = $this->get('Security.context')->getToken()->getUser();
            $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
            $this->get('CommandBus')->execute($includeSession);

            return new JsonResponse($response->getData(),$response->getStatusCode());
        }
        return new Response("Método incorrecto",404);
    }

    /**
     *  genera el documento pdf con el resumen de la reservacion hecha por un cliente
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 24-08-2015
     * @param $publicId
     * @param int $owner 1 = owner, cualquier otro valor = aavv o cliente
     * @return Pdf
     */
    public function reservationResumePdfAction($publicId,$owner = 0)
    {
        set_time_limit(0);
        $command = new GetResumenReservationStructureCommand($publicId,false,$owner);
        $validator = $this->get('validator');
        $errors = $validator->validate($command);

        //si hay errores
        if (count($errors) > 0) {

            $arrayErrors = array();

            foreach ($errors as $error)
                $arrayErrors[] = $error->getMessage();

            $response = array(
                'meta' => array(
                    'code' => 400,
                    'message' => 'Bad request'
                ),
                'data' => $arrayErrors
            );
            return new JsonResponse($response, 400);
        } else {
            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201) {
                $data = $response->getData();
                if (isset($data['sendAavv']) and $data['sendAavv']) {
                    $data['total'] = \number_format($data['subTotal']+$data['tax'],2,",",".");
                    $data['subTotal'] = \number_format($data['subTotal'],2,",",".");
                    $data['tax'] = \number_format($data['tax'],2,",",".");
                }
                else {
                    $data['total'] = \number_format($data['subTotal'] + $data['tax'], 0, ",", ".");
                    $data['subTotal'] = \number_format($data['subTotal'], 0, ",", ".");
                    $data['tax'] = \number_format($data['tax'], 0, ",", ".");
                }
                $urls['logoImage'] = $_SERVER['DOCUMENT_ROOT'].'/images/logo-pdf-resume-reservation.png';
                $urls['starImage'] = $_SERVER['DOCUMENT_ROOT'].'/images/navicu/star.png';
                $urls['barImage'] = $_SERVER['DOCUMENT_ROOT'].'/images/navicu/corporate_line.png';
                $urls['fonts'] =  $_SERVER['DOCUMENT_ROOT'].'/fonts/font-navicu/font-lato/';
                //$pdf = $this->get('pdfCreator')->generateReservationResume($response->getData());
                $urls = array_merge($urls,$data);
                $pdf = $this->get('pdfCreator')->generatePdfFromHtml(
                    $this->render('NavicuInfrastructureBundle:Web:resumeReservationPdf.html.twig',$urls)->getContent()
                );
                return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));

                //return $this->render('NavicuInfrastructureBundle:Web:resumeReservationPdf.html.twig',$urls);
            } else {
                return new Response($response->getMessage(), $response->getStatusCode());
            }
        }
    }

    /**
     * accion que despliega la vista de un a pre-reserva realizada
     *
     * @author Carlos Aguilera <ceaf.21@gmail.com>
     * @param String $PublicId
     * @return Response
     * @version 19/09/2015
     */
    public function resumePreReservationAction($publicId, $username, $token)
    {
        //comando
        $command = new GetResumenReservationStructureCommand(['id' => $publicId]);
        $response = $this->get('CommandBus')->execute($command);
        $command = new GetLocationHomeCommand();
        $location = $this->get('CommandBus')->execute($command);
        $data = $response->getData();
        //verifico que el tipo y status de la reserva sea el correcto
        if ( ($data['reservationPaymentType'] != 2 || $data['reservationPaymentType'] != 4 ) && $data['statusReservation'] != 0 )
            return new Response('Not Found', 404);

        //lista de bancos
        $bankList = $this->getDoctrine()
            ->getRepository('NavicuDomain:BankType')
            ->getListBanksArray($data['isForeignCurrency']);

        $user = $this->get('SessionService')->userClientSession($data['clientEmail']);
        if (is_null($user)) {
            if (isset($username, $token)) {
                if (!($data['token'] === $token && $this->get('SecurityService')->loginDirect(['userName' => $username], 'navicu_web'))) {
                    throw $this->createNotFoundException('Page not exist');
                } else {
                    $this->get('SecurityService')->loginDirect(['userName'=>$username], 'navicu_web');
                }
            }
            return $this->redirect($this->get('router')->generate("navicu_homepage_temporal"));
        }

        //si la respuesta es correcta
        if ($response->getStatusCode() == 201) {
            return $this->render('NavicuInfrastructureBundle:Web:resumePreReservation.html.twig',
                [
                    'locations' => json_encode($location->getData()),
                    'foreignCurrency' => $data['foreignCurrency'],
                    'bookingInformation' => json_encode(
                        array_merge(
                            $response->getData(),
                            [
                                'bankList' => $bankList,
                                'status' => $data['statusReservation']

                            ]
                        )
                    ),
                    'publicId' => $response->getData()['confirmationId'],
                    'currentUserCurrency' => $data['alphaCurrency'],
                    'isoCurrency' => $data['alphaCurrencyIso'],
                ]
            );
        }
        
        return new Response($response->getMessage(), $response->getStatusCode());
    }
}
