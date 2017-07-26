<?php
namespace Navicu\Core\Application\UseCases\Web\getDataSiteMap;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Comando para devolver el contenido del archivo siteMap.xml
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getDataSiteMapHandler implements Handler
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
        $urls = [];
        $data = $command->getRequest();
        $repoProperty = $rf->get("Property");


        // incluye urls desde base de datos
        $properties = $repoProperty->findAllProperty();
        foreach ($properties as $property) {
            $auxUrl = [
                'loc' => $data["routing"]->generate('navicu_property_details', array(
                    'slug' => $property->getSlug()
                )),
                'changefreq' => 'daily',
                'lastmod' => date("Y-m-d"),
                'priority' => '0.8'
            ];
            array_push($urls, $auxUrl);
        }

        foreach ($data["locations"] as $location) {
            $auxUrl = [
                'loc' => $data["routing"]->generate('navicu_search_property_by_location', array(
                    'countryCode' => $location["countryCode"],
                    'type' => $location["type"],
                    'slug' => $location["slug"]
                )),
                'changefreq' => 'daily',
                'lastmod' => date("Y-m-d"),
                'priority' => '0.5'
            ];
            array_push($urls, $auxUrl);
        }

        return new ResponseCommandBus(200, 'Ok', $urls);
    }
}
