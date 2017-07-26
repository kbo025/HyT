<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ListPayments;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

class ListPaymentsHandler implements Handler
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
        $invoicepaymentrep = $rf->get('AAVVInvoicePayments');

        $aavv_rep = $rf->get('AAVV');

        $aavvs = count($aavv_rep->findAllByAffiliates(['order' => 1]));

        $filters = $command->getRequest();

        $revpayments = $invoicepaymentrep->findFilteredPayments($filters);

        //die(var_dump($payments));

        $payments = array();

        foreach($revpayments as $payment){
            $currentpayment = [];

            $invoice = $payment->getAllocation()->first()->getInvoice();

            $currentpayment['aavvId'] = $invoice->getAavv()->getPublicId();
            $currentpayment['paymentId'] = $payment->getId();
            $currentpayment['name'] = $invoice->getAavv()->getCommercialName();
            $currentpayment['reference'] = $payment->getNumberReference();
            $currentpayment['amount'] = $payment->getAmount();
            $currentpayment['InvoiceNumber'] = $invoice->getNumber();
            if ($invoice->getLines()->count() === 0)
                $currentpayment['type'] = 1;
            else
                $currentpayment['type'] = 0;
            $currentpayment['status'] = (string)$payment->getStatus();

            $payments[] = $currentpayment;
        }

        if (isset($filters['order'])) {
            switch ($filters['orderBy']) {
                case 'agencyId':
                    $this->sortResponse($payments, 'aavvId', $filters['order']);
                    //die(var_dump($response));
                    //$qb->orderBy('aavv.public_id',$filters['order']);
                    break;
                case 'agencyName':
                    $this->sortResponse($payments, 'name', $filters['order']);
                    //$qb->orderBy('aavv.commercial_name', $filters['order']);
                    break;
                case 'reference':
                    $this->sortResponse($payments, 'reference', $filters['order']);
                    //$qb->orderBy('p.reference_number',$filters['order']);
                    break;
                case 'amount':
                    $this->sortResponse($payments, 'amount', $filters['order']);
                    //$qb->orderBy('p.amount',$filters['order']);
                    break;
                case 'invoiceNumber':
                    $this->sortResponse($payments, 'InvoiceNumber', $filters['order']);
                    //$qb->orderBy('invoice.number',$filters['order']);
                    break;
                case 'type':
                    $this->sortResponse($payments, 'type', $filters['order']);
                    //$qb->orderBy('invoice.number',$filters['order']);
                    break;
                case 'status':
                    $this->sortResponse($payments, 'status', $filters['order']);
                    //$qb->orderBy('invoice.number',$filters['order']);
                    break;
            }
        } else {
            $this->sortResponse($payments, 'InvoiceNumber', 'ASC');
        }
        $response['payments'] = $payments;
        $response['aavvCount'] = $aavvs;

        return new ResponseCommandBus(200, 'ok', $response);
    }

    private function sortResponse(&$response, $field,  $order){

        if ($order == 'ASC') {
            usort($response, function ($a, $b) use($field){
                if(is_string($a[$field]))
                    return strcmp($a[$field] , $b[$field]);
                else
                    return $a[$field] - $b[$field];
            });
        } else {
            usort($response, function ($a, $b) use($field){
                if(is_string($a[$field]))
                    return strcmp($b[$field] , $a[$field]);
                else
                    return $b[$field] - $a[$field];
            });
        }
    }
}