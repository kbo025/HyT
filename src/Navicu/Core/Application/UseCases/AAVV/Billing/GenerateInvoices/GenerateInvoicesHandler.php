<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\GenerateInvoices;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVInvoice;

class GenerateInvoicesHandler implements Handler
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
        $rev_group_rep = $rf->get('AAVVReservationGroup');
        $sequence_rep = $rf->get('NVCSequence');
        $currency = $currencyrep->find(148);
        $adminId = $userrep->findByUserNameOrEmail('supradmin')->getId();

        $currentdate = new \DateTime();
        $createDate = new \DateTime();
        $moddate = $currentdate->modify('first day of next month');
        $due_date = $moddate->add(new \DateInterval('P4D'));
        $creationdate = $currentdate->format('Y-m-d');
        //$due_date = $due_date->format('Y-m-d');

        $invoices = $aavvrep->generateInvoices();

        //die(var_dump($invoices));

        $newInvoices = array();

        foreach ($invoices as $key => $item){
            $newInvoices[$item['aavv_id']][$key] = $item;
        }

        $finalInvoices = array();
        foreach($newInvoices as $key => $value){
            $finalInvoices[$key] = array();
            $finalInvoices[$key]['total'] = 0;
            foreach($value as $item) {
                $finalInvoices[$key]['groups'][] = $item['id'];
                $finalInvoices[$key]['total'] = $finalInvoices[$key]['total'] + $item['total_amount'];
            }
        }

        if (count($finalInvoices) > 0) {
            try {

                $response = array();
                $generated = 0;

                foreach ($finalInvoices as $key => $currentinvoice) {
                    $invoice = new AAVVInvoice();
                    $aavv = $aavvrep->find($key);
                    $sequence = $sequence_rep->findOneByArray(['name' => 'aavv_invoice']);
                    $invoice->setAavv($aavv);
                    $invoice->setTotalAmount($currentinvoice['total']);
                    $invoice->setCurrencyType($currency);
                    $invoice->setType('ar_invoice');
                    $invoice->setDate($createDate);
                    $invoice->setDueDate($due_date);
                    $number = $sequence->getCurrentnext();
                    if($sequence->getPrefix())
                        $number = $sequence->getPrefix() . $number;
                    $invoice->setNumber($number);
                    $invoice->setDescription('Factura ' . $creationdate);
                    $invoice->setStatus(0);
                    $invoice->setTax(1);
                    $invoice->setTaxRate(0.12);

                    $invoice->setCreatedAt($createDate);
                    $invoice->setCreatedBy($adminId);
                    $invoice->setUpdatedAt($createDate);
                    $invoice->setUpdatedBy($adminId);

                    $invoicerep->save($invoice);
                    $sequence->setCurrentnext($number + 1);
                    $sequence_rep->save($sequence);
                    $generated++;

                    foreach($currentinvoice['groups'] as $currentgroup){
                        $group = $rev_group_rep->find($currentgroup);
                        $group->setAavvInvoice($invoice);
                        $rev_group_rep->save($group);
                    }
                }
                $response['toGenerate'] = count($invoices);
                $response['generated'] = $generated;

                return new ResponseCommandBus(200, 'ok', $response);
            } catch (\Exception $e) {
                return new ResponseCommandBus(400, $e->getMessage() . $e->getFile() . $e->getLine());
            }

        } else {
            $response['message'] = 'No existen facturas nuevas';
            return new ResponseCommandBus(404, 'ok', $response);
        }
    }
}