<?php
namespace Navicu\InfrastructureBundle\Controller\Admin;

use Navicu\Core\Application\UseCases\Admin\DeleteTempOwner\DeleteTempOwnerCommand;
use Navicu\Core\Application\UseCases\Admin\GetTempProperties\GetTempPropertiesCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\Admin\RegisterOwnerUser\RegisterOwnerUserCommand;



/**
 * Class TempPropertiesController
 *
 * La siguiente clase procesa todas las solicitudes de los establecimientos
 * que se encuentran en el proceso de registro (TempOwner)
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @package Navicu\InfrastructureBundle\Controller\Admin
 * @version 07/08/2015
 */
class TempPropertiesController extends Controller
{
    /**
     * La siguiente función carga los datos del establecimientos que se encuentran
     * en proceso de registro (TempOwner)
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return Response
     * @version 07/08/2015
     */
    public function tempPropertiesAction()
    {
        /*$command = new GetTempPropertiesCommand();
        $command->setUser($this->get('security.context')->getToken()->getUser());
        $response = $this->get('commandbus')->execute($command);*/

        return $this->render('NavicuInfrastructureBundle:Admin:accommodations/registrationProcess/registrationProcess.html.twig');
    }

    /**
     * función para realizar busquedas y ordenamiento en el los hoteles en proceso de registro
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return Response
     * @version 26/01/2017
     */
    public function listTempPropertiesAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            $command = new GetTempPropertiesCommand($data);
            $command->setUser($this->get('security.context')->getToken()->getUser());
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse(["data"=>$response->getData()], $response->getStatusCode());
        }

        return new Response('Not Found', 404);
    }

    /**
     * La siguiente función carga los datos del establecimientos que se encuentran
     * en proceso de registro (TempOwner)
     *
     * @author Carlos Aguilera <ceaf.21@gmail.com>
     * @return Response
     * @version 07/08/2015
     */
    public function registerOwnerAction(Request $request)
    {
        //si la solicitud es ajax
        if($request->isXmlHttpRequest()){
            //obtengo y decodifico el JSON
            $data = json_decode($request->getContent(),true);

            $command = new RegisterOwnerUserCommand(isset($data['slug']) ? $data['slug'] : null);
            $validator = $this->get('validator');
            $errors = $validator->validate($command);
                //si hay errores
            if (count($errors) > 0) {
                $arrayErrors = array();
                foreach($errors as $error){
                    $arrayErrors[$error->getPropertyPath()]=array(
                        'messeage'=>$error->getMessage(),
                        'value'=>$error->getInvalidValue(),
                        );
                }
                $response = array(
                    'meta'=>array(
                        'code'=>400,
                        'message'=>'Bad request'
                        ),
                    'data'=> $arrayErrors
                    );
                return new JsonResponse($response,400);
            }else{
                //se ejecuta el comando
                $response = $this->get('CommandBus')->execute($command);

                if ($response->getStatusCode() == 201) {

                    // se genera el pdf personalizado de terminos y condiciones
                    $this->get('pdfCreator')->generateTermsAndConditions($response->getData()['data_terms']);

                    $imagemanagerResponse = $this->container->get('liip_imagine.controller');

                    $imagemanagerResponse->filterAction(
                        $this->getRequest(),
                        $response->getData()['profile_image'], 'images_email');

                    $emailService = $this->get('EmailService');
                    $data = $command->getRequest();
                    $emailService->setRecipients(array($data['emailOwner']));
                    $emailService->setViewParameters(array('userName' => $data['slug'], 'publicId' => $data['codeProperty']));
                    $emailService->setViewRender('NavicuInfrastructureBundle:Email:extranetRegister.html.twig');
                    $emailService->setSubject('¡Bienvenido a la familia Navicu!');
                    $emailService->setEmailSender('info@navicu.com');
                    $emailService->sendEmail();
                    //se envia la respuesta

                    // Enviando correo a Yadickson Vasquez
                    $emailService->setRecipients(['yvasquez@navicu.com']);
                    $emailService->sendEmail();
                }
                return new JsonResponse($response->getArray(),$response->getStatusCode());
            }

        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La siguiente función elimina establecimientos que se encuentran
     * en proceso de registro (TempOwner)
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @return Response
     * @version 22/12/2015
     */
    public function deleteTempOwnerAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new DeleteTempOwnerCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201) {
                $logger = $this->get('logger');
                $log = [];
                $user = $this->get('security.context')
                    ->getToken()->getUser();
                $log['username'] = $user->getUsername();
                $log['roles'] = $user->getRoles();
                $log['ip'] = $_SERVER['REMOTE_ADDR'];
                $logger->info('temp_owner.delete:'.json_encode($log));
            }

            return new JsonResponse($response->getData(),$response->getStatusCode());
        }
        return new Response('Not Found',404);

    }

    /**
     * Funcion utilizada para exportar la lista de los establecimientos temporales a excel
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @param Request $request
     * @version 15/07/2016
     *
     * @param Request $request
     */
    public function listTempPropertiesToExcelAction(Request $request) {
        global $kernel;
        $docRouting = "/../web/files/list_temp_properties.xls";

        $data = json_decode($request->getContent(), true);
        $command = new GetTempPropertiesCommand($data);
        $command->setUser($this->get('security.context')->getToken()->getUser());
        $command->setNumberResult(9999);
        $response = $this->get('commandbus')->execute($command);

        $dataToExcel = $response->getData();

        $buildDataToEmail = $this->buildExcelFormatToDownload($dataToExcel['temp_properties']);
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
     * con los properties temporales
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
            $td .= '<tr><td>' . utf8_decode($row['expired_date']) . '</td>'.
                '<td>' . utf8_decode($row['name']) . '</td>'.
                '<td>' . utf8_decode($row['accommodation_title']) . '</td>'.
                '<td>' . utf8_decode($row['location']) . '</td>'.
                '<td>' . utf8_decode($row['contact_name']) . '</td>'.
                '<td>' . utf8_decode($row['phones']) . '</td>'.
                '<td>' . utf8_decode($row['progress']) . '</td>';
                if ($row['nvc_profile_name'])
                    $td .= '<td>' . utf8_decode($row['nvc_profile_name']) . '</td>';
                $td .= '</tr>';
        }

        $table = '<table>
                <tr>
                    <th> Fecha de afiliacion</th>
                    <th> Nombre </th>
                    <th> Tipo de establecimiento </th>
                    <th> Ciudad </th>
                    <th> Contacto </th>
                    <th> Telefono </th>
                    <th> Completado </th>
                    <th> Responsable </th>
                </tr>
                <tr>'
            . $td .
            '</tr>
            </table>';

        return $table;
    }
}
