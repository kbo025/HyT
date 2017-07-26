<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 21/11/16
 * Time: 03:07 PM
 */

namespace Navicu\InfrastructureBundle\Controller\Admin\AAVVModule;

use Navicu\Core\Application\UseCases\Admin\AAVVModule\InvoicesAndServicesList\InvoicesAndServicesListCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Navicu\Core\Application\UseCases\AAVV\Billing\GetDetailInvoice\GetDetailInvoiceCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Funciones encargadas del listado de facturas desde admin de las aavv
 *
 * Class InvoiceController
 * @package Navicu\InfrastructureBundle\Controller\AAVV
 */
class InvoiceController extends Controller
{
    /**
     * Función encargada de desplegar la vista de las facturas de todas las aavv
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 23/11/2016
     */
    public function getViewListInvoicesOfAAVVAction()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Admin:invoicing/invoiceList/invoiceList.html.twig'
        );
    }

    /**
     * Función encargda de listar las facturas solicitas de todas las aavv
     *
     * @param Request $request
     * @param int $invoiceType, facturas por vencer
     * @param int $page
     * @return JsonResponse
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function invoicesListOfAAVVAction(Request $request, $page = 1, $invoiceType = 0)
    {
        $data_recived = json_decode($request->getContent(),true);
        $data_recived['invoiceType'] = $invoiceType; // vencidas o por vencer

        $command = new InvoicesAndServicesListCommand($data_recived);
        $responseData = $this->get('CommandBus')->execute($command);

        $data = $responseData->getData();
        //Extraemos el numero de la agencia y dejamos el arreglo solo con la info de las facturas
        $agencyNumber = array_shift($data);

        $response['aavvCount'] = $agencyNumber['aavvCount'];
        $response['billing'] = $data;
        $response["pagination"] = $this
                ->get('Pagination')
                ->simplePagination($response['billing'], $page, 50);
        return new JsonResponse(
            [
                'meta' => [
                    'code' => $responseData->getStatusCode(),
                    'message' => 'ok'
                ],
                'data' => $response,
            ]
        );
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
    public function generateInvoicePdfAction(Request $request, $slug, $idInvoice)
    {
        $command = new GetDetailInvoiceCommand([
            'isAdmin' => true,
            'slug' => $slug,
            'idInvoice' => $idInvoice
        ]);
        $rcb = $this->get('CommandBus')->execute($command);
        $data = $rcb->getData();
        $data['logo'] = $_SERVER['DOCUMENT_ROOT'].'/images/logo-pdf-resume-reservation.png';
        $data['fonts'] =  $_SERVER['DOCUMENT_ROOT'].'/fonts/font-navicu/font-lato/';

        if ($rcb->isOk()) {
            $pdf = $this->get('pdfCreator')->generatePdfFromHtml (
                $this->render (
                    'NavicuInfrastructureBundle:AAVV:billing\billPdf.html.twig',
                    $data
                )->getContent()
            );
            return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
        }

        return new Response('Not Found', 404);
    }
}
