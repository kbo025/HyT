<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 18/10/16
 * Time: 08:42 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Search\GetTopDestination;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

class GetTopDestinationHandler implements Handler
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
        $response = [];
        $obj = [];
        $aavv = (strcmp(gettype(CoreSession::getUser()), "string") == 0) ?
            null :
            (method_exists(CoreSession::getUser()->getAavvProfile(), "getAavv")) ?
                CoreSession::getUser()->getAavvProfile()->getAavv() :
                null;
        // Si esta logeada la aavv
        if (!is_null($aavv)) {
            $locationRf = $rf->get('Location');
            $topDestinationRf = $rf->get('AAVVTopDestination');

            $aavvTopDestination = $topDestinationRf->findTopDestinationOrderByDesc($aavv->getId());
            // Si se encontro un destino lo agregamos a la respuesta de destinos por aavv
            if (count($aavvTopDestination) > 0) {
                foreach ($aavvTopDestination as $topDestination) {
                    $id = $topDestination->getLocation()->getId();
                    $location = $locationRf->findOneByArray(['id' => $id]);

                    $obj['countryCode'] = 'VE';
                    $obj['type'] = $location->checkType();
                    $obj['slug'] = $location->getSlug();
                    $obj['title'] = $topDestination->getLocation()->getTitle();
                    array_push($response, $obj);
                }
            }
        }
        return new ResponseCommandBus(200, 'ok', $response);
    }
}