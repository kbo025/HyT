<?php
namespace Navicu\Core\Application\UseCases\Search\SearchCountryLocation;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\SphinxService;

class SearchCountryLocationHandler implements Handler
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
        $sphinxService = new SphinxService($rf);
        $sphinxQL = $rf->get('SphinxQL');

        $data = $command->getRequest();
        $response = [];
        $countStates = 0;
        $countCities = 0;
        $countMunicipalities = 0;
        $countParishes = 0;
        $countProperties = 0;

        // Buscamos los estados donde tenga coincidencia la localidad
        $states = $sphinxQL->findOneStateByCountrySlugQL(
            $data['countryCode'],
            $data['location']
        );
        if ($states)
            $countStates =  count($states);

        // Buscamos la ciudades donde tenga coincidencia
        $cities = $sphinxQL->findOneCityByCountrySlugQL(
            $data['countryCode'],
            $data['location']
        );
        if ($cities)
            $countCities = count($cities);

        $municipalities = $sphinxQL->findOneMunicipalityByCountrySlugQL(
            $data['countryCode'],
            $data['location']
        );
        if ($municipalities)
            $countMunicipalities = count($municipalities);

        $parishes = $sphinxQL->findOneParishByCountrySlugQL(
            $data['countryCode'],
            $data['location']
        );
        if ($parishes)
            $countParishes = count($parishes);

        $properties = $sphinxQL->findOnePropertyByCountrySlugQL(
            $data['countryCode'],
            $data['location']
        );
        if ($properties)
            $countProperties = count($properties);

        //Cuantos resultados exactos con la localidad existen
        $countTotal = $countStates + $countCities + $countMunicipalities + $countParishes + $countProperties;

        print_r("estados:".count($states)."\n");
        print_r("ciudades:".count($cities)."\n");
        print_r("municipios".count($municipalities)."\n");
        print_r("parroquias".count($parishes)."\n");
        print_r("establecimientos:".count($properties)."\n\n");

        // Si no existe un resultado único exacto, se buscan las sugerencias
        if ($countTotal != 1) {

            $response['suggestion'] = false;

            if ($countStates > 0) {
                $response['states']['list'] = $sphinxQL->resultPropertiesByIdState($states[0]['id']);
                $response['suggestion'] = true;
            }
            if ($countCities > 0) {
                $response['cities']['list'] = $sphinxQL->resultPropertiesByIdState($states[0]['id']);
                $response['suggestion'] = true;
            }

            if ($countMunicipalities > 0) {
                $response['municipalities']['list'] = $sphinxQL->resultPropertiesByIdState($states[0]['id']);
                $response['suggestion'] = true;
            }

            if ($countParishes > 0) {
                $response['parishes']['list'] = $sphinxQL->resultPropertiesByIdState($states[0]['id']);
                $response['suggestion'] = true;
            }

            if  ($countProperties > 0) {
                $response['parishes']['list'] = $sphinxQL->resultPropertiesByIdState($states[0]['id']);
                $response['suggestion'] = true;
            }

            //No se consigue ningun resultado
            if ($response['suggestion'] == false)
                return new ResponseCommandBus(404,'Not Found');

        } else if ($countStates == 1)
            $response['redirect'] = 'estado';
        else if ($countCities == 1)
            $response['redirect'] = 'ciudad';
        else if ($countMunicipalities == 1)
            $response['redirect'] = 'municipio';
        else if ($countParishes == 1)
            $response['redirect'] = 'parroquia';
        else if ($countProperties == 1)
            $response['redirect'] = 'hotel';

        return new ResponseCommandBus(200, 'Ok', $response);

    }
}