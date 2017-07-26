<?php
namespace Navicu\Core\Application\UseCases\AAVV\Billing\ChangeStatusInvoice;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Caso de uso para el cambio de estatus de una factura
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 */
class ChangeStatusInvoiceHandler implements Handler
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
        $rpInvoice = $rf->get('AAVVInvoice');

        $invoice = $rpInvoice->findOneByArray(['number' => $command->get('idInvoice')]);

        if(empty($invoice) || $invoice->getAavv()->getSlug() != $command->get('slug'))
            return new ResponseCommandBus(404,'Not Found');

        $invoice->setStatus($command->get('status'));

        if ($rpInvoice->save($invoice))
            return new ResponseCommandBus(201,'ok');
        else
            return new ResponseCommandBus(400,'bad request');
    }
}