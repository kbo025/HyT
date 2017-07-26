<?php

namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\InfrastructureBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\FinishRegistration\FinishRegistrationCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\DeleteDocument\DeleteDocumentCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\UploadDocument\UploadDocumentCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\SetCompanyData\SetCompanyDataCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\GetCompanyData\GetCompanyDataCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step2\GetInfoStep2\GetInfoStep2Command;
use Navicu\Core\Application\UseCases\AAVV\Register\Step2\SetInfoStep2\SetInfoStep2Command;
use Navicu\Core\Application\UseCases\AAVV\Register\Step3\SetBillingAddress\SetBillingAddressCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\GetAAVVAgreement\GetAAVVAgreementCommand;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\SetAAVVAgreement\SetAAVVAgreementCommand;

class RegistrationController extends Controller
{
	public function uploaddocumentAction(Request $request)
    {
        $data = json_decode($request->request->get('data'), true);
        $command = new UploadDocumentCommand($data);

        if ($request->files->get('file'))
            $currentImage = new File($request->files->get('file'));

        //Se carga la imagen en el comando
        if (isset($currentImage) and $currentImage)
            $command->setFile($currentImage);

        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function deleteDocumentAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $command = new DeleteDocumentCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function RegisterCompanyAction(Request $request)
    {

    	
    		$temp = json_decode($request->getContent(), true);


            $users = $temp['users'];

            $data = array();

            $toUpload = [];

            $test = (array)json_decode($request->getContent());

            $form = (array) $test['form'];

            $files = (array) $test['files'];

            //die(var_dump($files));

            if((isset($form['logo'])) && ($form['logo'] instanceof \stdClass)) {
                $logo = [];
                $templogo = get_object_vars($form['logo']);
                $logo['file'] = $templogo['$ngfDataUrl'];
                $logo['documentType'] = 'logo';
                $logo['originalName'] = '';
                $toUpload[] = $logo;
            }

            if(isset($files['tourism_document']) && strlen($files['tourism_document']) > 300) {
                $rtn = [];
                $temprtn = $files['tourism_document'];
                $rtn['file'] = $temprtn;
                $rtn['documentType'] = 'rtn';
                $rtn['originalName'] = $form['tourism_document'];
                $toUpload[] = $rtn;
            }

            if(isset($files['rif_document']) && strlen($files['rif_document']) > 300) {
                $rif = [];
                $temprif = $files['rif_document'];
                $rif['file'] = $temprif;
                $rif['documentType'] = 'rif';
                $rif['originalName'] = $form['rif_document'];
                $toUpload[] = $rif;
            }

            if(isset($files['lease_document']) && strlen($files['lease_document']) > 300) {
                $lease = [];
                $templease = $files['lease_document'];
                $lease['file'] = $templease;
                $lease['documentType'] = 'lease';
                $lease['originalName'] = $form['lease_document'];
                $toUpload[] = $lease;
            }


            if(isset($form['images'])) {
                $images_array = $form['images'];

                $size = count($images_array);


                if ($size > 0) {
                    for ($i = 0; $i < $size; $i++) {

                        if (($images_array[$i] != null) && ($images_array[$i] instanceof \stdClass)) {
                            $image = [];
                            $tempimage = get_object_vars($images_array[$i]);
                            $image['file'] = $tempimage['$ngfDataUrl'];
                            $image['documentType'] = 'image';
                            $image['originalName'] = '';
                            $toUpload[] = $image;
                        }
                    }
                }
            }

            //die(var_dump($toUpload));


            //unset($form['tourism_document']);
            //unset($form['lease_document']);
            unset($form['logo']);
            unset($form['images']);

            $data['form'] = $form;
            $data['users'] = $users;

    	    $command = new SetCompanyDataCommand($data);

            $response = $this->get('CommandBus')->execute($command);

            $user = $this->container->get('security.context')->getToken()->getUser();

            $profile = null;
            if($user instanceof User)
                $profile = $user->getAavvProfile();

            if (!is_null($profile)) {
                $aavv = $profile->getAavv();
                $slug = $aavv->getSlug();
            }
             else {
                $slug = CoreSession::get('sessionAavv');
            }




                foreach ($toUpload as $currentDocument) {
                    if($currentDocument != null) {
                        $commanddata = array();

                        $commanddata['slug'] = $slug;
                        $commanddata['documentType'] = $currentDocument['documentType'];
                        $commanddata['originalName'] = $currentDocument['originalName'];

                        $command = new UploadDocumentCommand($commanddata);
                        $command->setFile($currentDocument['file']);

                        $currentresponse = $this->get('CommandBus')->execute($command);

                        if($currentresponse->getStatusCode() == 404) {
                            return new JsonResponse($currentresponse->getArray(), $currentresponse->getStatusCode());
                        }
                    }

                }


                return new JsonResponse($response->getArray(), $response->getStatusCode());

    	

    }

    public function CompanyDataAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $command = new GetCompanyDataCommand($data);

        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function testingAction(Request $request)
    {
        $aavvrep = $this->getDoctrine()->getRepository('NavicuDomain:AAVV');
        $userrep = $this->getDoctrine()->getRepository('NavicuInfrastructureBundle:User');

        $aavv = $aavvrep->find(1);

        $aavv->setCommercialName('La agencia');

        $aavv2 = new AAVV();

        $user = new User();

        $profile = new AAVVProfile();

        $profile->setAavv($aavv2);

        $user->setAavvProfile($profile);

        $userrep->save($user);

        $aavvrep->save($aavv2);




        return $this->render('NavicuInfrastructureBundle:Test:uploadtest.html.twig');
    }

    public function uploadTestingAction(Request $request)
    {
        $data = array();

        $data['slug'] = 18;
        $data['documentType'] = 'image';

        $command = new UploadDocumentCommand($data);
        //die(var_dump($request->files->get('fileselect')));

        if ($request->files->get('fileselect'))
            $currentImage = new File($request->files->get('fileselect'));

        //Se carga la imagen en el comando
        if (isset($currentImage) and $currentImage)
            $command->setFile($currentImage);

        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function deleteTestingAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $command = new DeleteDocumentCommand($data);
        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    /**
     * Esta función retorna la información necesaria para el manejo
     * del registro en el paso 2.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return Array
     */
    public function getInfoStep2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $command = new GetInfoStep2Command();

            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());

        } else {
            return new JsonResponse('Bad Request', 400);
        }
    }

    /**
     * Esta función Guarda la información necesaria para el manejo
     * del registro en el paso 2.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @return boolean
     */
    public function setInfoStep2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            $command = new SetInfoStep2Command($data);

            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());

        } else {
            return new JsonResponse('Bad Request', 400);
        }
    }

    /**
     * Accion para despliegue y registo del paso 4 de registro de AAVV
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16-09-2016
     *
     * @param Request $request
     * @param $slug
     * @internal param $Request
     * @internal param $slog
     *
     * @return mixed
     */
    public function AgreementAAVVAction(Request $request,$slug)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            $data['slug'] = $slug;
            $data['ip'] = $request->getClientIp();
            $command = new SetAAVVAgreementCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(),$response->getStatusCode());
        }
        return new JsonResponse('Not Found',404);
    }

    /**
     * Accion para despliegue y registo del paso 4 de registro de AAVV
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16-09-2016
     *
     * @param Request $request
     * @param $slug
     * @internal param $Request
     * @internal param $slog
     *
     * @return mixed
     */
    public function finishRegistrationAction(Request $request,$slug)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            $data['slug'] = $slug;
            $data['ip'] = $request->getClientIp();
            //die(var_dump($data));
            $command = new SetAAVVAgreementCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            if($response->getStatusCode() == '201') {

                $contract = $data['form']['contract'];
                $contractName = $data['form']['contractName'];
                if($contract) {
                    $commanddata = array();

                    $commanddata['slug'] = $slug;
                    $commanddata['documentType'] = 'ctr';
                    $commanddata['originalName'] = $contractName;

                    $command = new UploadDocumentCommand($commanddata);
                    $command->setFile($contract);

                    $response = $this->get('CommandBus')->execute($command);
                }

                if($response->getStatusCode() == '201') {
                    $command = new FinishRegistrationCommand(['slug' => $slug]);
                    $response = $this->get('CommandBus')->execute($command);
                    $data = $this->transformDataValidation($response->getData());
                    return new JsonResponse($data, $response->getStatusCode());
                } else {
                    return new JsonResponse($response->getData(),$response->getStatusCode());
                }
            } else {
                return new JsonResponse($response->getData(),$response->getStatusCode());
            }

        }
        return new JsonResponse('Not Found',404);
    }

    private function transformDataValidation($data)
    {
        $response = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'step1': $response[] = 1; break;
                    case 'step2': $response[] = 2; break;
                    case 'step3': $response[] = 3; break;
                    case 'step4': $response[] = 4; break;
                }
            }
        }
        return $response;
    }

    /**
     * Accion que genera y despliega el pdf de terminos y condiciones
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16-09-2016
     *
     * @param $slug
     * @internal param $Request
     * @internal param $slog
     *
     * @return mixed
     */
    public function GenerateAgreementPdfAAVVAction($slug)
    {
        $command = new GetAAVVAgreementCommand(['slug' => $slug]);

        $response = $this->get('CommandBus')->execute($command);
        $data = $response->getData();

        if ($response->isOk()) {
            $logo['logo'] = $_SERVER['DOCUMENT_ROOT'].'/images/logo-pdf-contrato-aavv.png';

            $pdf = $this->get('pdfCreator')->generatePdfFromHtml(
                $this->render('NavicuInfrastructureBundle:AAVV:register\step4\AgreementAavvPdf.html.twig', $logo)->getContent(),
                ['data' => $data]
            );
            return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
        }
        return new Response($response->getMessage(), $response->getStatusCode());
    }

    /**
     * Funcion encargada de actualizar los datos de la facturacion
     *
     * @param Request $request
     * @return JsonResponse
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 13/09/2016
     */
    public function aavvSetBillingDataAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user = CoreSession::getUser();
        $profile = null;
        if ($user instanceof User)
            $profile = $user->getAavvProfile();
        if(!is_null($profile)) {
            $slug = $profile->getAavv()->getSlug();
        }
        else {
            $slug = CoreSession::get('sessionAavv');
        }
        $data['data']['slug'] = $slug;
        $command = new SetBillingAddressCommand($data['data']);
        $response = $this->get('CommandBus')->execute($command);


        return new JsonResponse($response->getData(), $response->getStatusCode());
    }
}

