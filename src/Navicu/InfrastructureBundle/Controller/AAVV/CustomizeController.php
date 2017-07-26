<?php
namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\AAVV\Customize\GetCustomize\GetCustomizeCommand;
use Navicu\Core\Application\UseCases\AAVV\Customize\SetCustomize\SetCustomizeCommand;
use Navicu\Core\Application\UseCases\AAVV\Customize\UploadLogo\UploadLogoCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Controlador donde se procesan los datos de
 * personalización de la AAVV
 *
 * @author Freddy Contreras
 * Class DefaultController
 * @package Navicu\InfrastructureBundle\Controller\AAVV
 */
class CustomizeController extends Controller
{
    /**
     * La función obtiene los datos personalizados de la AAVV
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @version 19/09/2016
     */
    public function indexAction()
    {
        $commad = new GetCustomizeCommand();
        $response = $this->get('CommandBus')->execute($commad);
        if ($response->getStatusCode() == 200)
            return $this->render('NavicuInfrastructureBundle:AAVV/editUserPreferences:userPreferences.html.twig', ['data' => json_encode($response->getData())]);
        else
            return $this->redirect($this->generateUrl('navicu_error_500'));
    }

    /**
     * La función se encarga de actualizar los datos
     * de personalización de una AAVV
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new SetCustomizeCommand(['data' => $data['data']]);
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return new JsonResponse(null, 400);
    }

    /**
     * La siguiente función se encarga de subir una imagen del
     * establecimiento en el sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com >
     * @param string $slug
     * @param Request $request
     * @return Response
     * @version 20/11/2015
     *
     */
    public function uploadLogoAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $command = new UploadLogoCommand();

            if ($request->get('file'))
                $command->set('file', $request->get('file'));

            if ($request->get('nameImage'))
                $command->set('nameImage',$request->get('nameImage'));

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        }

        return new JsonResponse('Bad Request', 400);
    }
}
