<?php

namespace Navicu\Core\Application\UseCases\AAVV\Billing\MarkExpiredInvoices;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVInvoice;

class MarkExpiredInvoicesHandler implements Handler
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
        $invoicerep = $rf->get('AAVVInvoice');

        $expiredInvoices = $invoicerep->findExpiredInvoicesToDate();

        //die(var_dump($expiredInvoices));

        if($expiredInvoices) {
            $updated = 0;
            $response = null;
            try {

                foreach ($expiredInvoices as $expiredInvoice) {
                    $expiredInvoice->setStatus(3);
                    $expiredInvoice->setUpdatedAt(new \DateTime());
                    $invoicerep->save($expiredInvoice);
                    $updated++;
                }

                $response['updated'] = $updated;
                return new ResponseCommandBus(200, 'ok', $response);
            } catch (\Exception $e) {
                return new ResponseCommandBus(400, $e->getMessage() . $e->getFile() . $e->getLine());
            }
        } else {
            return new ResponseCommandBus(404, '');
        }
    }

}