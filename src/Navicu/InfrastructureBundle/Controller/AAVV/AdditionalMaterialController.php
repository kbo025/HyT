<?php
namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\Search\PropertySearchDetails\PropertySearchDetailsCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\Admin\GetAffiliatedProperties\GetAffiliatedPropertiesCommand;

/**
 * controlador para las acciones del usuario  AAVV relacionadas con la solicitud de materiales adicionales por parte de
 * navicu
 *
 * @author Gabriel Camacho <gcamacho@navicu.com>
 */
class AdditionalMaterialController extends Controller
{
    /**
     * esta función despliega una lista de establecimientos según filtros de busqueda
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @param $page
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function searchPropertiesAction($page, Request $request)
    {
        $response = [];
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(),true);
            if (isset($data['filter'])) {
                $data['propertyName'] = $data['filter'];
                $data['location'] = $data['filter'];
            }
        }

        $command = new GetAffiliatedPropertiesCommand(null,$data);
        $cbResponse = $this->get('CommandBus')->execute($command);
        $response['data'] = $cbResponse->getData();
        $response["page"] = $this
            ->get('Pagination')
            ->pagination(
                $response['data']['properties'],
                $page + 1,
                10
            );

        if ($cbResponse->isOk()) {
            if ($request->isXmlHttpRequest())
                return new JsonResponse($response);
            else
                return new Response(json_encode($response));
        }

        Return new Response('Bad Request',400);
    }

    /**
     * esta función genera un pdf con la información relevante del establecimiento
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @param $property
     * @return Response
     */
    public function pdfPropertyAction($property)
    {
        $command = new PropertySearchDetailsCommand(['countryCode' => 'VE', 'slug' => $property]);
        $cbResponse = $this->get('CommandBus')->execute($command);

        if ($cbResponse->isOk()) {
            $pdf = $this->get('pdfCreator')->generatePdfFromHtml (
                $this->render(
                    'NavicuInfrastructureBundle:AAVV:additionalMaterial\PropertyPdf.html.twig',
                    ['data' => $cbResponse->getData()]
                )->getContent()
            );
            return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
        }

        Return new Response('Not Found',404);
    }
}
