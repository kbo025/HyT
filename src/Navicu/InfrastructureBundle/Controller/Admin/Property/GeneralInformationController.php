<?php
namespace Navicu\InfrastructureBundle\Controller\Admin\Property;

use Navicu\Core\Application\UseCases\Admin\UpdateGeneralInformationProperty\UpdateGeneralInformationPropertyCommand;
use Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateService\UpdateServiceCommand;
use Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateServiceInstance\UpdateServiceInstanceCommand;
use Navicu\Core\Application\UseCases\Admin\UpdateServices\DeleteServiceInstance\DeleteServiceInstanceCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class GeneralInformationController extends Controller
{

    public function generalInformationAction(Request $request,$slug)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new UpdateGeneralInformationPropertyCommand($slug,$data['GeneralInformation']);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(),$response->getStatusCode());
        } else {
            $service = $this->get('AdminProperties');
            $data = $service->getGeneralInformationData($slug);
            return $this->render(
                'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/editGeneralInformation.html.twig',
                array('data' => json_encode($data),"slugTemp" => $slug, "agePolicy" => json_encode($data['GeneralInformation']['additionalInformation']['agePolicy']))
            );
        }
    }

    public function servicesAction($slug)
    {
        $service = $this->get('AdminProperties');
        $data = $service->getServicesData($slug);
        /*return $this->render(
            'NavicuInfrastructureBundle:Admin:editProperty/editGeneralInformation.html.twig',
            array('data' => $data,"slugTemp" => $slug)
        );*/
        return $this->render(
                'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/editServices.html.twig',
                [
                    'data' => json_encode($data),
                    "slugTemp" => $slug,
                    "typeFood"=>$data['typeFood'],
                    'buffetCarta'=>$data['buffetCarta']
                ]
            );
        // return new Response(json_encode($data));
    }

    public function saveServiceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new UpdateServiceCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getArray(), $response->getStatusCode());
        } else {
            return new Response('Not Found',404);
        }
    }

    public function updateServiceInstanceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new UpdateServiceInstanceCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getArray(), $response->getStatusCode());
        } else {
            return new Response('Not Found',404);
        }
    }

    public function deleteServiceInstanceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new DeleteServiceInstanceCommand($data);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getArray(), $response->getStatusCode());
        } else {
            return new Response('Not Found',404);
        }
    }
}
