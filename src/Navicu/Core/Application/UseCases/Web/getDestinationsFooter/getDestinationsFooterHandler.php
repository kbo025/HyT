<?php

namespace Navicu\Core\Application\UseCases\Web\getDestinationsFooter;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Clase para ejecutar el caso de uso getDestinationsFooterCommand, para el manejo del
 * listado de destinos habilitados por los establecimiento dentro de la BD.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getDestinationsFooterHandler implements Handler
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
        $destinationsType = $rf->get('DestinationsType');

        $destinies = $destinationsType->getAll();
        $response =[];
        foreach ($destinies as $destiny) {
            $auxDes["locations"] = [];
            $auxDes["type"] = CoreTranslator::getTranslator($destiny->getTitle(), "location");
            $auxDes["service"] = explode(".", $destiny->getTitle())[1];
            foreach ($destiny->getLocations() as $location) {
                $auxLoc["name"] = $location->getTitle();
                $auxLoc["slug"] = $location->getSlug();
                $auxLoc['type'] = $location->checkType();
                $auxLoc['countryCode'] = $location->getRoot()->getAlfa2();
                array_push($auxDes["locations"], $auxLoc);
            }
            array_push($response, $auxDes);
        }
        return new ResponseCommandBus(200, 'Ok', $response);
    }
}
