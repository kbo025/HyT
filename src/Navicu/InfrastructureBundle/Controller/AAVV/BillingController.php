<?php
namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\AAVV\Billing\ChangeStatusInvoice\ChangeStatusInvoiceCommand;
use Navicu\Core\Application\UseCases\AAVV\Billing\GetDetailInvoice\GetDetailInvoiceCommand;
use Navicu\Core\Application\UseCases\AAVV\Billing\ListBilling\ListBillingCommand;
use Navicu\Core\Application\UseCases\Admin\EditRolesAndPermissions\EditRolesAndPermissionsCommand;
use Navicu\Core\Application\UseCases\Admin\GetRolesAndPermissions\GetRolesAndPermissionsCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Admin\PermissionCreate\PermissionCreateCommand;
use Navicu\Core\Application\UseCases\AscribereDesktop\CreateTempOwner\CreateTempOwnerCommand;
use Navicu\Core\Application\UseCases\FixImagesDirectories\FixImagesDirectoriesCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Navicu\Core\Application\UseCases\AAVV\Billing\GenerateInvoices\GenerateInvoicesCommand;

/**
 * Controlador para las funcionalidades de facturación que podrá ejecutar el usuario AAVV
 *
 * @author Gabriel Camacho
 *
 */
class BillingController extends Controller
{
    /**
     * listado de facturación de una agencia de viajes con filtros
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @param $page
     * @return JsonResponse|Response
     */
    public function listBillingByAAVVAction(Request $request,  $page)
    {
        $data = ['slug' => $this->get('request')->get('subdomain')];
        if ($request->isXmlHttpRequest()) {
            $data_recived = json_decode($request->getContent(),true);
            if (is_array($data_recived))
                $data = array_merge($data,$data_recived);
        }

        $command = new ListBillingCommand($data);
        $rcb = $this->get('CommandBus')->execute($command);

        $data = $rcb->getData();
        $response['billing'] = $data['invoices'];
        $response['haveCreditZero'] = $data['haveCreditZero'];
        $response["pagination"] = $this
            ->get('Pagination')
            ->pagination(
                $response['billing'],
                $page,
                10
            );

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                [
                    'meta' => [
                        'code' => $rcb->getStatusCode(),
                        'message' => null
                    ],
                    'data' => $response,
                ]
            );
        } else {
            return $this->render(
                'NavicuInfrastructureBundle:AAVV:billing\billing.html.twig',
                ["data"=>json_encode($response)]
            );
        }
    }

    /**
     * cambio de estado de una agencia de viaje de 'no pagada' a 'por aprobación'
     *
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @param $idInvoice
     * @return JsonResponse|Response
     */
    public function changeStatusInvoiceAction(Request $request, $idInvoice)
    {
        if (!$request->isXmlHttpRequest())
            return new Response('Not Found', 404);

        $command = new ChangeStatusInvoiceCommand([
            'slug' => $this->get('request')->get('subdomain'),
            'idInvoice' => $idInvoice,
            'status' => 2
        ]);
        $rcb = $this->get('CommandBus')->execute($command);

        return new JsonResponse([
            'meta' => [
                'code' => $rcb->getStatusCode(),
                'message' => $rcb->getMessage(),
            ],
            'data' => null,
        ],$rcb->getStatusCode());
    }

    /**
     * generación de una factura en formato pdf
     *
     * @author Gabriel Camacho
     *
     * @param Request $request
     * @param $idInvoice
     * @return Response
     */
    public function generateInvoicePdfAction(Request $request, $idInvoice)
    {
        $command = new GetDetailInvoiceCommand([
            'slug' => $this->get('request')->get('subdomain'),
            'idInvoice' => $idInvoice
        ]);
        $rcb = $this->get('CommandBus')->execute($command);
        $data = $rcb->getData();
        $data['logo'] = $_SERVER['DOCUMENT_ROOT'].'/images/logo-pdf-resume-reservation.png';
        $data['fonts'] =  $_SERVER['DOCUMENT_ROOT'].'/fonts/font-navicu/font-lato/';

        $pdf = $this->get('pdfCreator')->generatePdfFromHtml (
            $this->render (
                'NavicuInfrastructureBundle:AAVV:billing\billPdf.html.twig',
                $data
            )->getContent()
        );
        return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
        return new JsonResponse($data);
    }

    public function generateInvoicesAction(Request $request)
    {

        $command = new GenerateInvoicesCommand();

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());

    }

    public function testingAction(Request $request)
    {
        //$data = json_decode($request->getContent(), true);

        $data['basePath'] = 'ruta';

        $command = new FixImagesDirectoriesCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());

    }
}
