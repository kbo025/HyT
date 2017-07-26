<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 11/11/16
 * Time: 08:56 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\UpdatePayment;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

class UpdatePaymentHandler implements Handler
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
        $data = $command->getRequest();

        switch ($data['modificationType']) {

            case 0:
                    $invoicepaymentrep = $rf->get('AAVVInvoicePayments');
                    $payment = $invoicepaymentrep->find($data['paymentId']);
                    $payment->setNumberReference($data['reference']);
                    $invoicepaymentrep->save($payment);
                    return new ResponseCommandBus(201,'OK');


                break;
            case 1:
                    $invoicepaymentrep = $rf->get('AAVVInvoicePayments');
                    $invoicerep = $rf->get('AAVVInvoice');
                    $invoicepaymentallrep = $rf->get('AAVVAllocationOfInvoicePayment');

                    $payment = $invoicepaymentrep->find($data['paymentId']);
                    $allocation = $payment->getAllocation()->first();
                    $invoice = $allocation->getInvoice();

                    $payment->setStatus($data['status']);

                    if ($data['status'] == 1){
                        //die(var_dump($payment));
                        $allocation->setAllocationAmount($payment->getAmount());
                        $invoice->setStatus(1);
                    }else{
                        $allocation->setAllocationAmount(0);
                        $invoice->setStatus(0);
                    }

                    $invoicepaymentallrep->save($allocation);
                    $invoicepaymentrep->save($payment);
                    $invoicerep->save($invoice);

                    return new ResponseCommandBus(201,'OK');


                break;
        }


    }



}