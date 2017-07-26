<?php
namespace Navicu\Core\Application\UseCases\Search\SearchCountry;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\SphinxService;

class SearchCountryHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $sphinxService = new SphinxService();
        $sphinxQL = $rf->get('SphinxQL');
        $data = $command->getRequest();

        $states = $sphinxQL->resultStatesByCountryCode(
            $data['countryCode']
        );

        var_dump($states);
        if (!empty($states)) {
            $response['states']['list'] = $states;
            return new ResponseCommandBus(200, 'Ok', $response);
        }
        else
            return new ResponseCommandBus(404, 'Not Found');

    }
}