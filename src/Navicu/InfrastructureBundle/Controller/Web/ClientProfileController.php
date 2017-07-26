<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Navicu\Core\Application\UseCases\Client\GetReservationsForClient\GetReservationsForClientCommand;
use Navicu\Core\Application\UseCases\Admin\GetReservationDetails\GetReservationDetailsCommand;
use Navicu\Core\Application\UseCases\GetLocationHome\GetLocationHomeCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Navicu\Core\Application\UseCases\EditClientProfile\EditClientProfileCommand;
use Navicu\Core\Application\UseCases\GetClientProfile\GetClientProfileCommand;
use Navicu\Core\Application\UseCases\Notifications\NotificationView\NotificationViewCommand;
use Navicu\Core\Application\UseCases\Web\getLocationRegister\getLocationRegisterCommand;
use Navicu\Core\Application\UseCases\ClientInfoSession\ClientInfoSessionCommand;

class ClientProfileController extends Controller
{


	/**
	 * El siguiente controlador es temporal solo para el
	 * desarollo frontend de listado de Pre-reserva
	 * @author Gregory Perez <gregory.facyt@gmail.com>
	 * @return Response
	 * @version 14/03/2016
	 *
	 */
    public function listPreReservationAction(Request $request)
    {
        $rpReservation = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');

        /*
        Valores que puede tomar el data['statuś']
         * 0: prereserva
         * 1: por confirmar
         * 2: pago confirmado
         * 3: Reserva Cancelada
         * 4: Todo tipo de reserva
        */

        //obtengo los datos a inyectar en el comando de la peticion ajax
        $data = $request->isXmlHttpRequest() ?
            json_decode($request->getContent(), true) :
            ['status' => 0];
        //obtengo el usuario en la sesión
        $user = $this->get('SessionService')->userClientSession();
        if (is_null($user))
            return new Response('Forbidden', 403);

        //indico en la data el id del cliente en la sesion

        $data['idClient'] = $user->getId();

        //obtengo la pagina en la solicitud o obtengo la primera
        $pagination = $request->isXmlHttpRequest() ?
            $data['page'] :
            0;

         //defino el tipo de lista que quiero consultar
        $data['type'] = 1;

        //creo el comando e inyecto la data
        $command = new GetLocationHomeCommand();
        $locationsArray = $this->get('CommandBus')->execute($command);

        $command = new GetReservationsForClientCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();

        $command = new NotificationViewCommand(["user"=>$user, "type"=>0]);
        $this->get('CommandBus')->execute($command);

        $includeSession = new ClientInfoSessionCommand(["user"=>$user->getUser()]);
        $this->get('CommandBus')->execute($includeSession);

        //obtengo las 10 reservas correspondientes a la data solicitada
        // $dataPag = $this->get('Pagination')->pagination(
        //     $dataResponse, $pagination
        // );

        //lista de bancos
        $bankList = $this->getDoctrine()
            ->getRepository('NavicuDomain:BankType')
            ->getListBanksArray();
        //lista de bancos receptore
        $receiverBankList = $this->getDoctrine()
            ->getRepository('NavicuDomain:BankType')
            ->getListBanksArray(1,true);
        //lista de bancos
        $internationalBankList = $this->getDoctrine()
            ->getRepository('NavicuDomain:BankType')
            ->getListBanksArray(2);
        //lista de bancos receptore
        $internationalReceiverBankList = $this->getDoctrine()
            ->getRepository('NavicuDomain:BankType')
            ->getListBanksArray(2,true);
       // si fue una solicitud ajax devuelvo un JSON sino devuelvo un
        return $request->isXmlHttpRequest() ?
            new JsonResponse(
                [
                    'reservations' => $dataResponse,
                    'page' => $dataPag,
                ],
                $response->getStatusCode()
            ) :
            $this->render(
                'NavicuInfrastructureBundle:Web:Client\listPreReservation.html.twig',
                [
                    'data' => json_encode([
                        'reservations' => $dataResponse,
                        'states' => $rpReservation->getStatesList(),
                        'bankList' => $bankList,
                        'receiverBanksList' => $receiverBankList,
                        'internationalBankList' => $internationalBankList,
                        'internationalReceiverBanksList' => $internationalReceiverBankList,
                        //'page' => $dataPag,
                        //'hasReservation' => count($dataResponse)==0 ? 0 : 1
                    ]),
                    'locations' => json_encode($locationsArray->getData())
                ]
            );
    }

    public function listRecordAction(Request $request)
    {
        $rpReservation = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');

        /*
        Valores que puede tomar el data['statuś']
         * 0: prereserva
         * 1: por confirmar
         * 2: pago confirmado
         * 3: Reserva Cancelada
         * 4: Todo tipo de reserva
        */

        //obtengo los datos a inyectar en el comando de la peticion ajax
        $data = $request->isXmlHttpRequest() ?
            json_decode($request->getContent(), true) :
            ['status' => 4];
        //obtengo el usuario en la sesión
        $user = $this->get('SessionService')->userClientSession();
        if (is_null($user))
            return new Response('Forbidden', 403);

        //indico en la data el id del cliente en la sesion

        $data['idClient'] = $user->getId();

        //obtengo la pagina en la solicitud o obtengo la primera
        $pagination = $request->isXmlHttpRequest() ?
            $data['page'] :
            0;

        //defino el tipo de lista que quiero consultar
        $data['type'] = 1;

        //creo el comando e inyecto la data
        $command = new GetLocationHomeCommand();
        $locationsArray = $this->get('CommandBus')->execute($command);

        $command = new GetReservationsForClientCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();

        $command = new NotificationViewCommand(["user"=>$user, "type"=>2]);
        $this->get('CommandBus')->execute($command);

        $includeSession = new ClientInfoSessionCommand(["user"=>$user->getUser()]);
        $this->get('CommandBus')->execute($includeSession);

        //obtengo las 10 reservas correspondientes a la data solicitada
        // $dataPag = $this->get('Pagination')->pagination(
        //     $dataResponse, $pagination
        // );

       // si fue una solicitud ajax devuelvo un JSON sino devuelvo un
        return $request->isXmlHttpRequest() ?
            new JsonResponse(
                [
                    'reservations' => $dataResponse,
                    'page' => $dataPag,
                ],
                $response->getStatusCode()
            ) :
            $this->render(
                'NavicuInfrastructureBundle:Web\Client:listRecord.html.twig',
                [
                    'data' => json_encode([
                        'reservations' => $dataResponse,
                        'states' => $rpReservation->getStatesListName()
                        //'page' => $dataPag,
                        //'hasReservation' => count($dataResponse)==0 ? 0 : 1
                    ]),
                    'locations' => json_encode($locationsArray->getData())
                ]
            );
    }

    public function listUpcomingReservationsAction()
    {

        $rpReservation = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');

        //obtengo el usuario en la sesión
        $user = $this->get('SessionService')->userClientSession();
        if (is_null($user))
            return new Response('Forbidden', 403);

        //indico en la data el id del cliente en la sesion

        $data['idClient'] = $user->getId();

        //defino el tipo de lista que quiero consultar
        $data['type'] = 2;

        //obtengo el nombre del usuario

        $userFullName = $user->getFullName();
        $userGender = $user->getGender();

        //creo el comando e inyecto la data
        $command = new GetLocationHomeCommand();
        $locationsArray = $this->get('CommandBus')->execute($command);

        $command = new GetReservationsForClientCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();

        $command = new NotificationViewCommand(["user"=>$user, "type"=>1]);
        $this->get('CommandBus')->execute($command);

        $includeSession = new ClientInfoSessionCommand(["user"=>$user->getUser()]);
        $this->get('CommandBus')->execute($includeSession);

        return $this->render('NavicuInfrastructureBundle:Web\Client:listUpcomingReservations.html.twig',
            [
                    'data' => json_encode([
                        'reservations' => $dataResponse,
                        'user' => array(
                            "fullName"=>$userFullName,
                            "gender"=>$userGender)
                    ]),
                    'locations' => json_encode($locationsArray->getData())
            ]
        );
    }

    public function listConfirmationProcessAction(Request $request)
    {
        $rpReservation = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');

        /*
        Valores que puede tomar el data['statuś']
         * 0: prereserva
         * 1: por confirmar
         * 2: pago confirmado
         * 3: Reserva Cancelada
         * 4: Todo tipo de reserva
        */

        //obtengo los datos a inyectar en el comando de la peticion ajax
        $data = $request->isXmlHttpRequest() ?
            json_decode($request->getContent(), true) :
            ['status' => 1];
        //obtengo el usuario en la sesión
        $user = $this->get('SessionService')->userClientSession();
        if (is_null($user))
            return new Response('Forbidden', 403);

        //indico en la data el id del cliente en la sesion

        $data['idClient'] = $user->getId();

        //obtengo la pagina en la solicitud o obtengo la primera
        $pagination = $request->isXmlHttpRequest() ?
            $data['page'] :
            0;

        //defino el tipo de lista que quiero consultar
        $data['type'] = 1;

        //creo el comando e inyecto la data
        $command = new GetLocationHomeCommand();
        $locationsArray = $this->get('CommandBus')->execute($command);

        $command = new GetReservationsForClientCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();

        $command = new NotificationViewCommand(["user"=>$user, "type"=>1]);
        $this->get('CommandBus')->execute($command);

        $includeSession = new ClientInfoSessionCommand(["user"=>$user->getUser()]);
        $this->get('CommandBus')->execute($includeSession);

        //obtengo las 10 reservas correspondientes a la data solicitada
        // $dataPag = $this->get('Pagination')->pagination(
        //     $dataResponse, $pagination
        // );

       // si fue una solicitud ajax devuelvo un JSON sino devuelvo un
        return $request->isXmlHttpRequest() ?
            new JsonResponse(
                [
                    'reservations' => $dataResponse,
                    'page' => $dataPag,
                ],
                $response->getStatusCode()
            ) :
            $this->render(
                'NavicuInfrastructureBundle:Web\Client:listConfirmationProcess.html.twig',
                [
                    'data' => json_encode([
                        'reservations' => $dataResponse,
                        //'states' => $rpReservation->getStatesList()
                        //'page' => $dataPag,
                        //'hasReservation' => count($dataResponse)==0 ? 0 : 1
                    ]),
                    'locations' => json_encode($locationsArray->getData())
                ]
            );
    }

    public function reservationDetailsAction($id)
    {
            $data["publicId"] = $id;
            $data["userSession"] = $this->get("SessionService")->getUserSession();
            $command = new GetReservationDetailsCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            if($response->getData() == null){
                throw $this->createNotFoundException('Not Found');
            }else
            return $this->render('NavicuInfrastructureBundle:Web\Client:reservationDetails.html.twig',
                ['data' => json_encode($response->getData())]);

    }

    /**
     * La siguiente funcionalidad es la vista home de acceso de cliente
     *
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @return Response
     * @version 6/05/2016
     *
     */
    public function homeAction(Request $request)
    {
        $command = new GetLocationHomeCommand();
        $response = $this->get('CommandBus')->execute($command);
        return $this->render('NavicuInfrastructureBundle:Web\Client:home.html.twig',
            array('locations' => json_encode($response->getData())));
    }


    /**
     * La siguiente funcionalidad es la vista de datos de usuario de acceso de cliente
     *
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @return Response
     * @version 11/05/2016
     *
     */
    public function userDataAction(Request $request)
    {
        $command = new getLocationRegisterCommand(["code"=>"VE"]);
        $locationsArray = $this->get('CommandBus')->execute($command);

        $FosUserId = $this->get('security.context')->getToken()->getUser();
        $command = new GetClientProfileCommand($FosUserId->getClientProfile());
        $response = $this->get('CommandBus')->execute($command);

        return $this->render('NavicuInfrastructureBundle:Web\Client:userData.html.twig',
            array(
                'data' => json_encode($response->getData()),
                'locations' => json_encode($locationsArray->getData())
            )
        );


    }
}

