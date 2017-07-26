<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\GeneratePayments;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment;
use Navicu\Core\Domain\Model\Entity\AAVVInvoice;
use Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments;
use Navicu\Core\Domain\Model\Entity\AAVVServiceInvoicePayment;
use Navicu\Core\Domain\Model\Entity\AAVVServicePaymentAllocation;

class GeneratePaymentsHandler implements Handler
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
        $aavvrep = $rf->get('AAVV');
        $userrep = $rf->get('User');
        $invoicerep = $rf->get('AAVVInvoice');
        $currencyrep = $rf->get('CurrencyType');

        $invoicepaymentrep = $rf->get('AAVVInvoicePayments');
        $invoicepaymentallrep = $rf->get('AAVVAllocationOfInvoicePayment');


        $invoices = $invoicerep->findAll();

        $invoicePaymentAmt = $this->GenerateInvoicePayments($invoices, $rf);

        //$serviceInvoicePaymentsAmt = $this->GenerateServiceInvoicePayments($serviceinvoices, $rf);

        return new ResponseCommandBus(200, 'ok', ['Reservation' => $invoicePaymentAmt, 'Service' => 0]);

    }

    private function GenerateInvoicePayments($invoices, RepositoryFactoryInterface $rf)
    {
        $userrep = $rf->get('User');
        $invoicerep = $rf->get('AAVVInvoice');
        $invoicepaymentrep = $rf->get('AAVVInvoicePayments');
        $invoicepaymentallrep = $rf->get('AAVVAllocationOfInvoicePayment');
        $bankrep = $rf->get('BankType');
        $adminId = $userrep->findByUserNameOrEmail('supradmin')->getId();
        $bank = $bankrep->find(1);

        $generated = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->getStatus() === 0 and $invoice->getAllocation()->count() === 0){

                //PAGO
                $payment = new AAVVInvoicePayments();
                $payment->setDate(new \DateTime());
                $payment->setType(0);
                $payment->setAmount($invoice->getTotalAmount());
                $payment->setAmountCurrent($invoice->getTotalAmount());
                $payment->setStatus(0);
                $payment->setBankType($bank);
                $payment->setCreatedAt(new \DateTime());
                $payment->setCreatedBy($adminId);
                $payment->setUpdatedAt(new \DateTime());
                $payment->setUpdatedBy($adminId);

                //ASIGNACION
                $allocation = new AAVVAllocationOfInvoicePayment();
                $allocation->setDate(new \DateTime());
                $allocation->setAllocationAmount(0);
                $allocation->setInvoice($invoice);
                $allocation->setPayments($payment);
                $allocation->setCreatedAt(new \DateTime());
                $allocation->setCreatedBy($adminId);
                $allocation->setUpdatedAt(new \DateTime());
                $allocation->setUpdatedBy($adminId);

                $invoicepaymentrep->save($payment);
                $invoicepaymentallrep->save($allocation);

                $generated++;
            }
        }

        return $generated;
    }

   /* private function GenerateServiceInvoicePayments($serviceinvoices, RepositoryFactoryInterface $rf)
    {
        $userrep = $rf->get('User');
        $serviceinvoicerep = $rf->get('AAVVServiceInvoice');
        $servicrpaymentrep = $rf->get('AAVVServiceInvoicePayment');
        $servicrpaymentallrep = $rf->get('AAVVServicePaymentAllocation');
        $bankrep = $rf->get('BankType');
        $adminId = $userrep->findByUserNameOrEmail('supradmin')->getId();
        $bank = $bankrep->find(1);

        $generated = 0;

        foreach ($serviceinvoices as $invoice) {
            if ($invoice->getStatus() === 0 and $invoice->getAllocation()->count() === 0){

                //PAGO
                $payment = new AAVVServiceInvoicePayment();
                $payment->setDate(new \DateTime());
                $payment->setType(0);
                $payment->setAmount($invoice->getTotalAmount());
                $payment->setAmountCurrent($invoice->getTotalAmount());
                $payment->setStatus(0);
                $payment->setBankType($bank);
                $payment->setCreatedAt(new \DateTime());
                $payment->setCreatedBy($adminId);
                $payment->setUpdatedAt(new \DateTime());
                $payment->setUpdatedBy($adminId);

                //ASIGNACION
                $allocation = new AAVVServicePaymentAllocation();
                $allocation->setDate(new \DateTime());
                $allocation->setAllocationAmount(0);
                $allocation->setInvoices($invoice);
                $allocation->setPayments($payment);
                $allocation->setCreatedAt(new \DateTime());
                $allocation->setCreatedBy($adminId);
                $allocation->setUpdatedAt(new \DateTime());
                $allocation->setUpdatedBy($adminId);

                $servicrpaymentrep->save($payment);
                $servicrpaymentallrep->save($allocation);

                $generated++;
            }
        }

        return $generated;
    }*/
}