<?php

namespace Navicu\Core\Application\UseCases\Web\AutocompleteListForFlight;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class AutocompleteListForFlightHandler implements Handler
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
        $rp = $rf->get("Airport");

        $response = $rp->findByWords($command->getRequest());

        return new ResponseCommandBus(200, 'Ok', $response['data']);
    }
}
