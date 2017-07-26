<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 21/11/16
 * Time: 04:06 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\InvoicesAndServicesList;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

/**
 * Clase encargada de listar las facturas existentes en la base de datos de todas las agencias
 *
 * Class InvoicesAndServicesListHandler
 * @package Navicu\Core\Application\UseCases\AAVV\InvoicesAndServicesList
 */
class InvoicesAndServicesListHandler implements Handler
{

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $invoiceRf = $rf->get('AAVVInvoice');
        $request = $command->getRequest();
        $invoices = null;

        $numberAavv = $rf->get('AAVV')->findAllByAffiliates(['order' => 1]);

        // Si la peticion viene para una busqueda mediante un filtro
        if ( (isset($request['word'])) OR (is_null($request['word'])) )  {
            $request['status'] = $request['invoiceType']; // si es facturas vencidas 1 o por vencer 0
            $invoices = $invoiceRf->getInvoicesByFilters($request);
        }

        // Se arma la respuesta
        $data = $this->getData($request, $invoices, $invoiceRf, $rf);

        // Si el ordenamiento es por numero de servicio (construido fuera de la bd)
        if ( isset($request['orderBy']) && ($request['orderBy'] == 'numberServiceOrReservation')) {
            usort($data, self::build_sorter($request['orderBy'], $request['order']));
        }

        //Agregamos al inicio la cantidad de agencias
        array_unshift($data, ['aavvCount' => count($numberAavv)]);

        return new ResponseCommandBus(200, 'ok', $data);
    }

    /**
     * Funcion encargada de buscar las facturas dependiendo de lo solicitado y armar la estructura de retorno
     *
     * @param $requestWithOutFilter array, informacion de frontEnd con el tipo de factura a buscar
     * @param $invoices array, resultado de la busqueda con filtro de las facturas
     * @param $invoiceRf object, repositorio de aavvInvoice
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 30/11/2016
     *
     * @return array
     */
    private function getData($requestWithOutFilter, $invoices, $invoiceRf)
    {
        $data = [];
        // Si la peticion NO es mediante la peticion del filtro
        if (is_null($invoices)) {
            // Peticion a las facturas por vencer o vencidas
            if (isset($requestWithOutFilter['invoiceType']) /*AND isset($requestWithOutFilter['service'])*/) {
                // Si el tipo de invoice es 0 listar las no vencidas
                if ($requestWithOutFilter['invoiceType'] == 0)
                    $AavvsInvoices = $invoiceRf->findInvoicesNotExpired();
                else
                    $AavvsInvoices = $invoiceRf->findInvoicesExpired();

                $this->buildStructure($AavvsInvoices, $requestWithOutFilter['invoiceType'], $data);
            } // Peticion sin ninguna diferencia a las facturas
            else {
                $AavvsInvoices = $invoiceRf->findInvoicesNotExpired();
                $this->buildStructure($AavvsInvoices, 0, $data);
            }
        }
        else
            $this->buildStructure($invoices, $requestWithOutFilter['invoiceType'], $data);

        return $data;
    }

    /**
     * Funcion de comparacion para ordenar el array response para los campos calculados fuera de la BD (campos numericos)
     */
    public static function build_sorter($key,$order)
    {
        return function ($a, $b) use ($key,$order) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            if(strtolower($order)=='asc')
                return ($a[$key] < $b[$key]) ? -1 : 1;
            else
                return ($a[$key] > $b[$key]) ? -1 : 1;
        };
    }

    /**
     * Funcion encargada de construir la respuesta con los parametros necesarios
     *
     * @param $AavvsInvoices object, aavvInvoice object buscado por el tipo (facturas de reservas/servicios)
     * @param $invoiceType int, determina el tipo de invoice buscado indicado desde frontend (vencida o por vencer)
     * @param $data array, arreglo con toda la informacion de las facturas (reserva y servicio)
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 30/11/2016
     */
    public function buildStructure($AavvsInvoices, $invoiceType, &$data)
    {
        foreach ($AavvsInvoices as $invoice) {
            $objInvoice['invoiceType'] = 'service';
            $objInvoice['type'] = 0; // tipo servicio
            $objInvoice['numberInvoice'] = $invoice->getNumber(); // numero de factura
            $objInvoice['state'] = (integer)$invoiceType; //vencida o por vencer
            $aavv = $invoice->getAavv();

            // Si no posee lineas la factura es de tipo reserva
            if (count($invoice->getLines()) == 0) {
                $groups = $invoice->getAavvReservationGroup();
                $amountReservations = 0;

                foreach ($groups as $group)
                    $amountReservations = $amountReservations + count($group->getReservation());
                $objInvoice['invoiceType'] = 'reservation';
                $objInvoice['type'] = 1; // tipo reservacion
            }

            $objInvoice['aavvPublicId'] = $aavv->getPublicId() instanceof PublicId ?
                                        $aavv->getPublicId()->toString() :
                                        $aavv->getPublicId();
            $objInvoice['aavvSlug'] = $aavv->getSlug();
            $objInvoice['aavvId'] = $aavv->getId();
            $objInvoice['agencyName'] = $aavv->getCommercialName();
            $numberServiceOrReservation = count($invoice->getLines());
            $objInvoice['numberServiceOrReservation'] = ($numberServiceOrReservation == 0) ?
                $amountReservations :
                $numberServiceOrReservation;
            $objInvoice['total'] = number_format($invoice->getTotalAmount(), 2, ',', '.');
            $objInvoice['dueDate'] = $invoice->getDueDate()->format('d-m-Y');

            array_push($data, $objInvoice);
            $objInvoice = [];
        }
    }
}