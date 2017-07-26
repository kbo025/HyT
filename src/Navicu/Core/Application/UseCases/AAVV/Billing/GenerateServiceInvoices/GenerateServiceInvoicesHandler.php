<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\GenerateServiceInvoices;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVInvoice;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Model\Entity\AAVVServiceInvoice;
use Navicu\Core\Domain\Model\Entity\AAVVInvoiceDetail;
use Symfony\Component\Validator\Constraints\Date;

class GenerateServiceInvoicesHandler implements Handler
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
        $invoice_rep = $rf->get('AAVVInvoice');
        $sequence_rep = $rf->get('NVCSequence');
        $aavv_rep = $rf->get('AAVV');
        $currencyrep = $rf->get('CurrencyType');
        $currency = $currencyrep->find(148);
        $userrep = $rf->get('User');
        $adminId = $userrep->findByUserNameOrEmail('supradmin')->getId();

        $currentdate = new \DateTime();
        $createDate = new \DateTime();
        $moddate = $currentdate->modify('first day of next month');
        $due_date = $moddate->add(new \DateInterval('P4D'));
        $creationdate = $currentdate->format('Y-m-d');

        $response = array();

        $aavvlist = $aavv_rep->findAll();

        $invoiceCount = 0;

        foreach ($aavvlist as $aavv) {

            if ($aavv->getStatusAgency() === 2 and count($aavv->getMonthlyServiceInvoices(new \DateTime())) == 0) {

                $services = $aavv->getAdditionalQuota();

                if(!empty($services->toArray())) {

                    $invoice = new AAVVInvoice();
                    $sequence = $sequence_rep->findOneByArray(['name' => 'aavv_service_invoice']);

                    $invoice_total = 0;

                    $invoice_subtotal = 0;

                    foreach ($services as $service) {

                        $line = new AAVVInvoiceDetail();
                        $line->setDate(new \DateTime());
                        $line->setTaxRate(0.12);
                        $priceWithoutTax = round($service->getAmount() / 1.12, 2);
                        $line->setPrice($priceWithoutTax);

                        $line->setInvoice($invoice);

                        $line->setCreatedBy($adminId);
                        $line->setCreatedAt(new \DateTime());

                        $line->setUpdatedAt(new \DateTime());
                        $line->setUpdatedBy($adminId);

                        $type = $service->getDescription();
                        //die(var_dump($type));
                        switch ($type) {

                            case "maintenance":

                                $line->setQuantity(1);
                                $line->setDescription('maintencance');
                                $line->setTotal($priceWithoutTax);

                                break;
                            case "email":

                                $line->setQuantity(1);
                                $line->setDescription('email');
                                $line->setTotal($priceWithoutTax);
                                break;
                            case "licence":
                                // Calculamos el numero de dias que posee el mes
                                $date = new \DateTime('now');
                                $month =  $date->format('m');
                                $year =  $date->format('Y');
                                $numDays = cal_days_in_month (CAL_GREGORIAN, $month ,$year);

                                $array_user_profile = $aavv->getAavvProfile();
                                $lengthUser = $aavv->getAavvProfile()->count();
                                $servicePerDay = ($service->getAmount() / $numDays);

                                for ($ii = 0; $ii < $lengthUser; $ii++) {
                                    // Solo incluimos en la factura los usuarios que esten activos o hayan caido en mora
                                    if ( ($array_user_profile[$ii]->getStatus() == AAVVProfile::STATUS_ACTIVE) OR
                                        ($array_user_profile[$ii]->getStatus() == AAVVProfile::STATUS_INACTIVE_DEBT_REASON)
                                    ) {
                                        $amountDaysDifferences = $numDays;
                                        $last_activation = $array_user_profile[$ii]->getLastActivation();
                                        $interval = $date->diff($last_activation);

                                        // Si existe mas de un usuario con licencia agregamos otra linea
                                        if ($ii > 0)
                                            $line = new AAVVInvoiceDetail();
                                        // si se activo el usuario a mediados del mes
                                        if ( ($interval->y == 0) AND ($interval->m == 0) )
                                            $amountDaysDifferences = $interval->d;

                                        $priceWithoutTax = round(($servicePerDay * $amountDaysDifferences) / 1.12, 2);

                                        $line->setDate(new \DateTime());
                                        $line->setTaxRate(0.12);
                                        $line->setPrice($priceWithoutTax);
                                        $line->setInvoice($invoice);
                                        $line->setCreatedBy($adminId);
                                        $line->setCreatedAt(new \DateTime());
                                        $line->setUpdatedAt(new \DateTime());
                                        $line->setUpdatedBy($adminId);

                                        if ($ii == 0)
                                            $line->setQuantity($ii);
                                        else
                                            $line->setQuantity(1);
                                        $line->setDescription('licence ' . ($ii + 1));
                                        $line->setTotal($priceWithoutTax);

                                        if ($line->getQuantity() > 0) {
                                            $invoice->addLine($line);
                                            $invoice_subtotal += $line->getTotal();
                                            $invoice_total += ($servicePerDay * $amountDaysDifferences);
                                        }
                                    }
                                }
                                break;
                        }

                        if ( (strcmp($type, 'maintenance') == 0) OR (strcmp($type, 'email') == 0) ) {
                            $invoice->addLine($line);
                            $invoice_subtotal += $line->getTotal();
                            $invoice_total += $service->getAmount() * $line->getQuantity();
                        }
                        //$invoice_rep->save($invoice);
                    }

                    $invoice->setType('ar_invoice');
                    $invoice->setDate($createDate);
                    $invoice->setDueDate($due_date);
                    $invoice->setDescription('Factura Servicios ' . $creationdate);
                    $number = $sequence->getCurrentnext();
                    if($sequence->getPrefix())
                        $number = $sequence->getPrefix() . $number;
                    $invoice->setNumber($number);
                    $invoice->setTaxRate(0.12);
                    $invoice->setStatus(0);
                    $invoice->setAavv($aavv);
                    $invoice->setCurrencyType($currency);

                    $invoice->setSubtotal($invoice_subtotal);
                    $invoice->setTax($invoice_subtotal * 0.12);
                    $invoice->setTotalAmount(round($invoice->getSubtotal() + $invoice->getTax(), 2));

                    $invoice->setCreatedAt($createDate);
                    $invoice->setCreatedBy($adminId);
                    $invoice->setUpdatedAt($createDate);
                    $invoice->setUpdatedBy($adminId);

                    $invoice_rep->save($invoice);
                    $invoiceCount++;
                    $sequence->setCurrentnext($number + 1);
                    $sequence_rep->save($sequence);
                }
            }
        }

        $response['invoiceCount'] = $invoiceCount; 

        return new ResponseCommandBus(200, 'ok', $response);
    }
}