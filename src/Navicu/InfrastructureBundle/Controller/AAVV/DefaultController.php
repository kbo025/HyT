<?php
namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\AAVV\Logs\GetAavvLogs\GetAavvLogsCommand;
use Navicu\Core\Application\UseCases\AAVV\Logs\GetAavvLogDetails\GetAavvLogDetailsCommand;
use Navicu\InfrastructureBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Navicu\Core\Application\UseCases\AAVV\Preregister\RegisterAAVV\RegisterAAVVCommand;
use Navicu\Core\Application\UseCases\AAVV\Preregister\ConfirmAAVV\ConfirmAAVVCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\AAVV\Preregister\GetValidAAVVEmail\GetValidAAVVEmailCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\GetAAVVAgreement\GetAAVVAgreementCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step3\GetBillingAddress\GetBillingAddressCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\GetCompanyData\GetCompanyDataCommand;
use Navicu\Core\Domain\Adapter\CoreSession;


class DefaultController extends Controller
{
    /**
     * La siguiente función carga el homepage del administrador
     * @return Response
     */
    public function landingAction()
    {

        return 'hola';
        //return $this->render('NavicuInfrastructureBundle:Admin:tempProperties/listProperties.html.twig');
    }

    public function adminEditAction($slug)
    {
        CoreSession::setSessionAavvSlug($slug);

        return $this->redirect(
            $this->generateUrl(
                'aavv_admin_register',
                array('step' => 'company')
            )
        );
    }

    /**
     * La siguiente función retorna el pdf con los requerimientos
     * para registrarse como agencia de viajes
     * @return Response
     */
    public function registrationRequirementsPdfAction()
    {
    	$filename = 'RequerimientosVicander.pdf';
        $path = $this->get('kernel')->getRootDir(). "/../web/files/";
        $content = file_get_contents($path.$filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

        $response->setContent($content);
        return $response;
    }

    /**
     * La siguiente función Registra un nuevo usuario inactivo
     * con el perfil de agencia de viajes
     * @return JsonResponse
     */
    public function userRegisterAction(Request $request)
    {
        $data = $request->request->all();

        //dump($data);

        $command = new RegisterAAVVCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(),$response->getStatusCode());
    }


    /**
     * La siguiente función activa un usuario y lo asocia a una nueva agencia de viajes
     *
     * @return RedirectResponse
     */
    public function userConfirmRegistrationAction($userName, $token)
    {
        $command = new ConfirmAAVVCommand($userName, $token);

        $response = $this->get('CommandBus')->execute($command);

        if($response->getStatusCode() == 200) {

            $user = $response->getArray()['data'];

            //die(var_dump($user));

            if($this->get('SecurityService')->loginDirect(['userName'=>$user->getEmail()], 'navicu_aavv')) {

                return $this->redirect(
                    $this->generateUrl(
                        'aavv_register',
                        array('step' => 'company')
                    )
                );
            }
        } else {
            return $this->redirect($this->generateUrl('navicu_aavv_login'));
        }


    }

    public function validEmailAction(Request $request)
    {

            $data = json_decode($request->getContent(),true);
            $command = new GetValidAAVVEmailCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());

    }

    public function showRegisterAction() {
        return $this->render('NavicuInfrastructureBundle:AAVV/landing:register.html.twig');
    }

    public function aavvRegistrationAction(Request $request, $step)
    {

        switch($step)
        {
            case 'company':
            case 1:

                $command = new GetCompanyDataCommand();
                $response = $this->get('CommandBus')->execute($command);
                return $this->render(
                    'NavicuInfrastructureBundle:AAVV/register/step1:step1.html.twig',
                    array("data" => json_encode($response->getArray()))
                );


            case 'payment':
            case 2:

            /*$user = CoreSession::getUser();

            $profile = $user->getAavvProfile();
            if(is_null($profile)) {

                return $this->redirect(
                    $this->generateUrl(
                        'aavv_admin_register',
                        array('step' => 'payment')
                    )
                );
            }*/

                /*$data = array('username'=>$slug);

                $command = new GetCompanyDataCommand($data);
                $response = $this->get('CommandBus')->execute($command);*/



                return $this->render(
                    'NavicuInfrastructureBundle:AAVV/register/step2:step2.html.twig',
                    array("data"=>null)
                );

            case 'billing':
            case 3: //Paso 3 para el registro de facturacion de la aavv

            $user = CoreSession::getUser();
            $profile = null;
            if ($user instanceof User)
                $profile = $user->getAavvProfile();
            if (!is_null($profile)) {
                $slug = $profile->getAavv()->getSlug();
            } else {
                $slug = CoreSession::get('sessionAavv');
                //die(var_dump($slug));
            }

                $command = new GetBillingAddressCommand(['slug' => $slug]);
                $response = $this->get('CommandBus')->execute($command);

                return $this->render(
                    'NavicuInfrastructureBundle:AAVV/register/step3:step3.html.twig',
                    ['data' => json_encode($response->getData())]
                );
            break;

            case 'agreement':
            case 4:
            $logo = $_SERVER['DOCUMENT_ROOT'].'/images/logo-pdf-contrato-aavv.png';
            $user = CoreSession::getUser();
            $profile = null;
            if ($user instanceof User)
                $profile = $user->getAavvProfile();
            if (!is_null($profile)) {
                $slug = $profile->getAavv()->getSlug();
            } else {
                $slug = CoreSession::get('sessionAavv');
                //die(var_dump($slug));
            }

                $command = new GetAAVVAgreementCommand(['slug' => $slug]);
                $response = $this->get('CommandBus')->execute($command);
                return $this->render (
                    'NavicuInfrastructureBundle:AAVV:register\step4\step4.html.twig',
                    ['data' => json_encode($response->getData()), 'slug' => $slug, 'logo' => $logo]
                );
                //return new Response('OK');
            break;

            case 'end':
                return $this->render('NavicuInfrastructureBundle:AAVV/register:end.html.twig');
            default: return new Response('Unauthorized',401);
        }
    }

    public function logsIndexAction()
    {
        return $this->render('NavicuInfrastructureBundle:AAVV/historical:historical.html.twig');
    }

    public function getLogsAction(Request $request, $page)
    {
        $command = new GetAavvLogsCommand();

        //$response = $this->get('CommandBus')->execute($command);

        $currentpage = $page;

        $response['data'] = $this->get('CommandBus')->execute($command)->getData();
        $response["page"] = $this->get('Pagination')->simplePagination($response['data']['logs'], $currentpage,10);

        return new JsonResponse($response,200);
    }

    public function getLogDetailsAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);

        $command = new GetAavvLogDetailsCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());

    }


}
