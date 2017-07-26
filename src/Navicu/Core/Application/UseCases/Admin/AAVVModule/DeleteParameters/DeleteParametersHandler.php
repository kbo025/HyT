<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class DeleteParametersHandler implements Handler
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

        $credit_rep = $rf->get('AAVVCreditNVC');

        try{
            $creditInstance= $credit_rep->find($data['id']);

            $credit_rep->delete($creditInstance);

            return new ResponseCommandBus(201,'OK');

        } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
}