<?php
namespace Navicu\InfrastructureBundle\Controller\Admin;

use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Application\UseCases\Admin\Users\SetRecruitToProperty\SetRecruitToPropertyCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Navicu\Core\Application\UseCases\Admin\GetAffiliatedProperties\GetAffiliatedPropertiesCommand;
use Navicu\Core\Application\UseCases\Admin\ChangeStatusProperty\ChangeStatusPropertyCommand;
use Navicu\Core\Application\UseCases\Admin\GetDetailsProperty\GetDetailsPropertyCommand;
use Navicu\Core\Application\UseCases\Admin\ListReservation\ListReservationCommand;
use Navicu\Core\Application\UseCases\Admin\ReservationModule\ReservationList\ReservationListCommand;
use Navicu\Core\Application\UseCases\Admin\ChangeReservationStatus\ChangeReservationStatusCommand;
use Navicu\Core\Application\UseCases\Admin\EditReservationDetails\EditReservationDetailsCommand;
use Navicu\Core\Application\UseCases\Admin\GetReservationDetails\GetReservationDetailsCommand;
use Navicu\Core\Application\UseCases\Admin\Users\SetCommercialToProperty\SetCommercialToPropertyCommand;
use Navicu\Core\Application\UseCases\Admin\DropDailyFromRoom\DropDailyFromRoomCommand;
use Navicu\Core\Application\UseCases\Extranet\NotifyTheUnavailabilityInProperties\NotifyTheUnavailabilityInPropertiesCommand;

class AffiliatePropertiesController extends Controller
{
    /**
     * La siguiente función carga los datos del establecimientos que son afiliados
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return Response
     * @version 16/10/2015
     */
    public function affiliatePropertiesAction()
    {
        /*$command = new GetAffiliatedPropertiesCommand();
        $command->setUser($this->container->get('security.context')->getToken()->getUser());
        $response = $this->get('CommandBus')->execute($command);*/

        return $this->render(
            'NavicuInfrastructureBundle:Admin:accommodations/affiliates/affiliates.html.twig'
        );
    }

    /**
     * Obtener el listado de establecimientos por filtros
     *
     * @param $page
     * @param Request $request
     * @return JsonResponse
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     */
    public function apiGetAffiliatesPropertiesAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new GetAffiliatedPropertiesCommand($data);
            $command->setUser($this->container->get('security.context')->getToken()->getUser());

            $data = $this->get('CommandBus')->execute($command)->getData();
            $response['data'] = $data;
            return new JsonResponse($response, 200);
        }

        return new JsonResponse(null, 404);
    }

    /**
     * Funcion encargda de renderizar la vista del detalle del establecimiento
     *
     * @return Response
     */
    public function viewPropertyDetailsAction($slug)
    {
        return $this->render('NavicuInfrastructureBundle:Admin:accommodations/affiliates/details/detailsAccommodations.html.twig', ['slug' => $slug]);
    }

    /**
     * La siguiente funcion se encarga de obtener el detalle de un establecimiento
     * dado un slug
     *
     * @param $slug
     * @param Request $request, data del filtro
     * @return Response
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 26/10/2015
     */
    public function dataPropertyDetailsAction($slug, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $command = new GetDetailsPropertyCommand($data);
        $command->setSlug($slug);

        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse($response->getData(), $response->getStatusCode());
    }

    /**
     * La función muetra el template del detalle de la reserva
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 23/02/2017
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function reservationDetailsTemplateAction(Request $request)
    {
        return $this->render('NavicuInfrastructureBundle:Admin:detailsReservation/detailsReservation.html.twig');
    }

    /**
     * La función consulta los detalles de una reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 26/10/2015
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function reservationDetailsAction(Request $request, $publicId)
    {
        if ($request->isXmlHttpRequest()) {
            $command = new GetReservationDetailsCommand([
                'publicId' => $publicId,
                'userSession' => $this->get("SessionService")->getUserSession(),
                'owner' => false
            ]);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());

        } else
            return new JsonResponse('Bad Request', 400);
    }

    /**
     * La siguiente función se encargá de ejectar el caso de uso
     * de "Editar los datos de una reserva"
     * @param Request $request
     * @return JsonResponse
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 29/10/2015
     */
    public function editReservationDetailsAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);
            $command = new EditReservationDetailsCommand($data['reservationId'], $data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }

        return new JsonResponse('Bad Request', 400);
    }

    /**
     * Accion que activa/desactiva un establesimiento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 29/10/2015
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changeStatusPropertyAction(Request $request)
    {
        //si la solicitud es ajax
        if ($request->isXmlHttpRequest()) {
            //obtengo y decodifico el JSON
            $data = json_decode($request->getContent(), true);

            $command = new ChangeStatusPropertyCommand($data['id'], $data['status']);
            $validator = $this->get('validator');
            $errors = $validator->validate($command);
            //si hay errores
            if (count($errors) > 0) {
                $arrayErrors = array();
                foreach ($errors as $error) {
                    $arrayErrors[$error->getPropertyPath()] = array(
                        'messeage' => $error->getMessage(),
                        'value' => $error->getInvalidValue(),
                    );
                }
                $response = array(
                    'meta' => array(
                        'code' => 400,
                        'message' => 'Bad request'
                    ),
                    'data' => $arrayErrors
                );
                return new JsonResponse($response, 400);
            } else {
                //se ejecuta el comando
                $response = $this->get('CommandBus')->execute($command);
                return new JsonResponse($response->getArray(), $response->getStatusCode());
            }
        } else {
            return new Response('Not Found', 404);
        }
    }

    /**
     * La siguiente función muestra un listado de todas las reserva
     * @author Jose Agraz <jaagraz@navicu.com>
     * @return Response
     * @version 22/12/2015
     */
    public function listReservationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new ListReservationCommand($data);
            $command->setUser($this->container->get('security.context')->getToken()->getUser());
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        } else {
            return new Response('Not Found', 404);
        }

    }

    /**
     * La siguiente funciónes usada para devolver la lista de
     * reservas dentro del sistema.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @return Json
     */
    public function reservationListPOFAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new ReservationListCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        } else {
            return new Response('Not Found', 404);
        }

    }

    /**
     * La siguiente función carga la vista de un listado segun el estatus de una reserva
     * @author Jose Agraz <jaagraz@navicu.com>
     * @return Response
     * @version 22/12/2015
     */
    public function listReservationTemplateAction($reservationStatus)
    {
        return $this->render('NavicuInfrastructureBundle:Admin:reservation/reservation.html.twig',
            ['reservationStatus' => $reservationStatus]
        );
    }

    /**
     * La función consulta los detalles de una reserva
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 01/02/2016
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function detailsReservationsAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $data["userSession"] = $this->get("SessionService")->getUserSession();
            $command = new GetReservationDetailsCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());

        } else
            return new JsonResponse('Bad Request', 400);
    }

    /**
     * La siguiente función cambia el estado de una reserva
     * en proceso de registro (TempOwner)
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @return Response
     * @version 22/12/2015
     */
    public function changeReservationStatusAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            if (isset($data['data']))
                $data = $data['data'];
            $command = new ChangeReservationStatusCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new Response('Not Found', 404);
    }

    /**
     * La funcion recibe los datos para la asignación
     * de un establecimiento temporal o afiliado a un usuario comercial
     *
     * @author Freddy Contreras <freddycontreras3gmail.com>
     * @param Request $request
     * @version 04/04/2016
     */
    public function setCommercialToPropertyAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new SetCommercialToPropertyCommand();

            if (isset($data['propertyId']))
                $command->setPropertyId($data['propertyId']);

            if (isset($data['commercialId']))
                $command->setCommercailId($data['commercialId']);

            if (isset($data['propertyType']))
                $command->setPropertyType($data['propertyType']);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        }

        return new JsonResponse('Bad Request', 404);
    }

    /**
     * La funcion recibe los datos para la asignación
     * de un establecimiento temporal o afiliado a un usuario captador
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @param Request $request
     * @version 14/02/2017
     * @return JsonResponse
     */
    public function setRecruitToPropertyAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new SetRecruitToPropertyCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new JsonResponse('Bad Request', 404);
    }

    /**
     * La función recibe como parámetro un conjunto de datos para eliminar
     * de la habitación el dailyRoom y el dailyPack dada una fecha
     *
     * @author Isabel Nieto <isabelcndgmail.com>
     * @param Request $request
     * @version 04/04/2016
     * @return JsonResponse
     */
    public function dropDailyFromRoomAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $data = json_decode($request->getContent(), true);
                $command = new DropDailyFromRoomCommand($data);
                $response = $this->get('CommandBus')->execute($command);

                return new JsonResponse($response->getData(), $response->getStatusCode());
            }
            return new JsonResponse("Forbidden", 403);
        }
        return new JsonResponse('Bad Request', 404);
    }

    /**
     * Funcion utilizada para exportar la lista de los establecimientos a excel
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @param Request $request
     * @version 15/07/2016
     *
     * @param Request $request
     */
    public function listPropertiesToExcelAction(Request $request) {
        global $kernel;
        $docRouting = "/../web/files/list_properties.xls";

        $data = json_decode($request->getContent(), true);
        $command = new GetAffiliatedPropertiesCommand($data);
        $command->setUser($this->container->get('security.context')->getToken()->getUser());
        $command->setNumberResult(9999);
        $response = $this->get('CommandBus')->execute($command);

        $dataToExcel = $response->getData();

        $buildDataToEmail = $this->buildExcelFormatToDownload($dataToExcel['properties']);
        // Construimos el documento y lo guardamos
        $fp = fopen($kernel->getRootDir() . $docRouting, "w+");
        fwrite($fp, print_r($buildDataToEmail, TRUE));
        fclose($fp);

        // Obtenemos el documento construido para permitir la descarga desde el correo
        $dataToEmail = $kernel->getRootDir() . $docRouting;
        if (file_exists($dataToEmail)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . basename($dataToEmail));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($dataToEmail));
            ob_clean();
            flush();
            readfile($dataToEmail);
        }
        else
            $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');
    }

    /**
     * Funcion que construye una tabla para guardarla en un documento del tipo excel,
     * con el listado de los properties
     *
     * @param array $data, datos con los dias, responsable y la ciudad a los que pertenecen los properties
     *
     * @return string
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 15/07/2016
     */
    public function buildExcelFormatToDownload($data)
    {
        $td = "";
        foreach ($data as $row) {
            $td .= '<tr><td>' . utf8_decode($row['join_date']) . '</td>'.
                '<td>' . utf8_decode($row['name']) . '</td>'.
                '<td>' . utf8_decode($row['location']) . '</td>'.
                '<td>' . utf8_decode($row['admin_email']) . '</td>'.
                '<td>' . utf8_decode($row['total_to_pay']) . '</td>'.
                '<td>' . utf8_decode($row['number_reservation']) . '</td>'.
                '<td>' . utf8_decode($row['credit_days']) . '</td>'.
                '<td>' . utf8_decode($row['discount_rate']) . '</td>';
                if ($row['nvc_profile_name'])
                    $td .= '<td>' . utf8_decode($row['nvc_profile_name']) . '</td>';
                $td .= '</tr>';
        }

        $table = '<table>
                <tr>
                    <th> Fecha de afiliacion</th>
                    <th> Nombre </th>
                    <th> Ciudad </th>
                    <th> Email responsable</th>
                    <th> Facturacion </th>
                    <th>'.utf8_decode('Nº').' de reservas</th>
                    <th> Dias de credito </th>
                    <th> Tasa de descuento </th>
                    <th> Responsable </th>
                </tr>
                <tr>'
            . $td .
            '</tr>
            </table>';

        return $table;
    }

    /**
     * Funcion encargada de construir el esqueleto y los datos de los properties que no poseen dias disponibles
     *
     * @param $properties
     * @return array
     */
    public function getProperties($properties)
    {
        $response = [];
        foreach ($properties as $property) {
            if ($property->getActive()) {
                $data['slug'] = $property->getSlug();
                $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
                $propertiesData = $this->get('CommandBus')->execute($command);

                $propertyResponse = $propertiesData->getData();

                if ($propertyResponse['listPorperty']) {
                    $lengthRoom = count($propertyResponse['dailyRoom']);
                    $lengthPack = count($propertyResponse['dailyPack']);

                    $arrayResponse['join_date'] = $property->getJoinDate()->format('d-m-Y');
                    $arrayResponse['propertyName'] = $propertyResponse['propertyName'];
                    if ($lengthRoom > 1)
                        $dateRoom = $propertyResponse['dailyRoom'][0];
                    else if ($lengthPack > 1)
                        $dateRoom = $propertyResponse['dailyPack'][0];
                    $arrayResponse['date'] = $dateRoom['date'];
                    $arrayResponse['slug'] = $property->getSlug();

                    $commercialProfile = $property->getCommercialProfile();
                    if ($commercialProfile) {
                        $arrayResponse['responsible'] = $commercialProfile->getFullName();
                    } else
                        $arrayResponse['responsible'] = null;

                    $location = $property->getLocation();
                    $arrayResponse['city'] =
                        !is_null($location->getCityId()) ?
                            $location->getCityId()->getTitle() : $location->getParent()->getTitle();

                    array_push($response, $arrayResponse);
                }
            }
        }
        return $response;
    }

    /**
     * La siguiente funcion devolvera la vista de los establecimientos que contengan habitaciones sin
     * tarifas
     *
     * @author Isabel Nieto <isabelcndgmail.com>
     * @version 01/02/2017
     * @return JsonResponse|Response
     */
    public function viewPropertiesWithoutPriceAction()
    {
        return $this->render('NavicuInfrastructureBundle:Admin:accommodations/noAvailability/noAvailability.html.twig');
    }

    /**
     * La siguiente funcion devolvera los datos de los establecimientos que contengan habitaciones sin
     * tarifas
     *
     * @author Isabel Nieto <isabelcndgmail.com>
     * @param Request $request
     * @version 01/02/2017
     * @return JsonResponse|Response
     */
    public function dataPropertiesWithoutPriceAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
        $command->setUser($this->get('security.context')->getToken()->getUser());

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getData(), 200);
    }

    /**
     * Funcion encargada de generar un Excel con los datos de los alojamientos sin disponibilidad
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 03-08-2016
     * @param Request $request
     * @return JsonResponse
     */
    public function downloadPropertiesWithoutAvailabilityAction(Request $request)
    {
        global $kernel;
        $docRouting = "/../web/files/propertiesWithoutAvailability.xls";

        $data = json_decode($request->getContent(), true);
        $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
        $command->setUser($this->get('security.context')->getToken()->getUser());
        $command->setNumberResult(999999);

        $response = $this->get('CommandBus')->execute($command)->getData();

        $buildDataToEmail = $this->buildExcelFormatForEmailAndAdmin(
            $response,
            $command->getStartDate()->format('d-m-Y'),
            $command->getEndDate()->format('d-m-Y')
        );

        // Construimos el documento y lo guardamos
        $fp = fopen($kernel->getRootDir() . $docRouting, "w+");
        fwrite($fp, print_r($buildDataToEmail, TRUE));
        fclose($fp);

        // Obtenemos el documento construido para permitir la descarga desde el correo
        $dataToEmail = $kernel->getRootDir() . $docRouting;
        if (file_exists($dataToEmail)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . basename($dataToEmail));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($dataToEmail));
            ob_clean();
            flush();
            readfile($dataToEmail);
        }
        else
            $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');
    }

    /**
     * Funcion encargada de responder al enlace dentro del correo enviado al admin con los
     * properties sin disponibilidad
     *
     * @param string $username, usuario "supradmin"
     * @return int
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 01/07/2016
     */
    public function propertiesWithoutPriceToEmailAction(Request $request, $username)
    {
        global $kernel;
        $docRouting = "/../web/files/properties.xls";

        $userSession = $this->get("SessionService")->getUserSession();

        if ($userSession->getUsername() == $username) {

            $data = json_decode($request->getContent(), true);
            $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
            $command->setUser($this->get('security.context')->getToken()->getUser());
            $command->setNumberResult(999999);

            $response = $this->get('CommandBus')->execute($command)->getData();

            $buildDataToEmail = $this->buildExcelFormatForEmailAndAdmin(
                $response,
                $command->getStartDate()->format('d-m-Y'),
                $command->getEndDate()->format('d-m-Y')
            );
            // Construimos el documento y lo guardamos
            $fp = fopen($kernel->getRootDir() . $docRouting, "w+");
            fwrite($fp, print_r($buildDataToEmail, TRUE));
            fclose($fp);

            // Obtenemos el documento construido para permitir la descarga desde el correo
            $dataToEmail = $kernel->getRootDir() . $docRouting;
            if (file_exists($dataToEmail)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . basename($dataToEmail));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($dataToEmail));
                ob_clean();
                flush();
                readfile($dataToEmail);
            }
            else
                $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');
        }
        else
            return $this->render('NavicuInfrastructureBundle:Errors:404.html.twig');
    }

    /**
     * Funcion que construye una tabla para guardarla en un documento del tipo excel,
     * con los properties que no tienen disponibilidad
     *
     * @param array $data , datos con los dias, responsable y la ciudad a los que pertenecen los properties
     *
     * @param $startDate object fecha en la que se realizo la consulta
     * @param $endDate object fecha en la que se realizo la consulta
     * @return string
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 1/07/2016
     */
    public function buildExcelFormatForEmailAndAdmin($data, $startDate, $endDate)
    {
        $td = "";
        foreach ($data['properties'] as $row) {
            $td .= '<tr>'.
                       '<td>' . utf8_decode($row['join_date']) . '</td>'.
                       '<td>' . utf8_decode($row['name']) . '</td>'.
                       '<td>' . utf8_decode($row['full_name']) . '</td>'.
                       '<td>' . utf8_decode($row['city']) . '</td>'.
                       '<td>' . utf8_decode($row['date']) . '</td>'
                   .'</tr>';
        }

        $table = '
            <table>
                <tr><td>Reporte generado del '.$startDate.' hasta el'.$endDate.'</td></tr>
            </table>
            <table>
                <tr>
                    <th>Fecha de afiliacion</th>
                    <th>Establecimiento</th>
                    <th>Responsable</th>
                    <th>Ciudad</th>
                    <th>Sin disponibilidad desde</th>
                </tr>
                <tr>'
                    . $td .
                '</tr>
            </table>';

        return $table;
    }

    /**
     * Acción usada para generar excel reservas
     *
     * @param $status
     * @return Response
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @version 04-11-2016
     */
    public function downloadExcelReservationsAction($status, $page, $search, $totalItems)
    {
        //busquedas vacias
        if($search=='empty')
            $search = null;

        //si no es un estado valido
        if (($status < 0) || ($status > 4))
            return new Response('Not Found', 404);

        if ($status == 4)
            $status = null;
        
        $command = new ReservationListCommand(
            [
                'reservationStatus' => $status,
                'itemsPerPage' => $totalItems,
                'page' => $page,
                'search' => $search,
            ]);
        $rcb = $this->get('CommandBus')->execute($command);
        $data = $rcb->getData();
        $response = $this->get('ExcelGenerator')->getExcelReservations($data['data']);

        return $response;
    }
}
