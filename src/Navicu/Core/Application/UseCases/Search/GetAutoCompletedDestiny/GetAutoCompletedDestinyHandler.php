<?php
namespace Navicu\Core\Application\UseCases\Search\GetAutoCompletedDestiny;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\SearchEngineService;

/**
 * Metodo que hace uso del motor de busqueda para generar una lista de
 * destinos por medio de autocompletado.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetAutoCompletedDestinyHandler implements Handler
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
        $request = $command->getRequest();
        $sphinxQL = $rf->get('SERepository');
        $location = $rf->get('Location');
        $priorityCollection = new \SplPriorityQueue;
        $response["data"] = [];

        // Adaptando el conjunto de string de busqueda
        // al manejo de especificaciones de Motor de busqueda
        $request["words"] = $this->wordsAdapter($request["words"]);

        $locations = $sphinxQL->getLocationByWords($request);

        $this->infoDestination($locations);
        $destiny = [];
        for ($l = 0; $l < count($locations); $l++) {
            if ($locations[$l]["lvl"] == 1 or $l==0 ) {
                unset($locations[$l]["division"]);
                unset($locations[$l]["dependency"]);
                array_push($destiny, $locations[$l]);
            } else {
                $d = 0;
                $stop = true;
                while($d < count($destiny) and $stop == true){
                    if(is_integer(array_search($destiny[$d]["id"], $locations[$l]["division"]))) {
                        unset($locations[$l]["lvl"]);
                        unset($locations[$l]["division"]);
                        unset($locations[$l]["dependency"]);
                        $destiny[$d]["subLvl"][] = $locations[$l];
                        $stop = false;
                    }
                    $d++;
                }
                if ($stop) {
                    unset($locations[$l]["division"]);
                    unset($locations[$l]["dependency"]);
                    array_push($destiny, $locations[$l]);
                }
            }
        }

        for ($d = 0; $d < count($destiny); $d++) {
            $priority = $this->priorityPosition($destiny[$d]["lvl"], $d);
            unset($destiny[$d]["lvl"]);
            $priorityCollection->insert($destiny[$d], $priority);
        }

        if(!isset($request["properties"])) {
            $properties = $sphinxQL->getPropertyByWords($request);
            for ($p = 0; $p < count($properties); $p++) {
                $priority = $this->priorityPosition($properties[$p]["lvl"], $p);
                $auxProperty["name"] =  $properties[$p]["name"];
                $auxProperty["slug"] = $properties[$p]["slug"];
                $auxProperty["countryCode"] = $properties[$p]["alfa"];
                $auxProperty["typeId"] = 100;
                $priorityCollection->insert($auxProperty, $priority);
            }
        }

        $c = 0;
        while($priorityCollection->valid()){
            if ($c <= 4)
                array_push($response["data"], $priorityCollection->current());

            $c++;
            $priorityCollection->next();
        }

        return new ResponseCommandBus(200, 'Ok', $response);
    }

    /**
     * Esta Función es usada para devolver un conjunto de palabras
     * adaptadas a las especificaciones de motor de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param string $words
     * @return string
     */
    public function wordsAdapter($words)
    {
        $searchService = new SearchEngineService();

        // Limpiando el conjunto de string de busqueda de
        // caracteres especiales.
        $words = $searchService->wordClean($words);

        if (!$words)
            return $words;

        // Pasando string a array.
        $words = explode(" ", $words);

        // Eliminando palabras con longitud menor a 2
        $response =  array_map(function ($w) {return $w."*";}, $words);

        return implode(" ", $response);
    }

    /**
     * Esta Función es usada para devolver la información requerida
     * para todos y cada una de las localidades almacenadas en un arreglo
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param array $locations
     * @return void
     */
    public function infoDestination(&$locations)
    {

        array_walk(
            $locations,
            function(&$l)
            {
                $l["division"] = array_column(json_decode($l["division"], true), "id");
                array_shift($l["division"]);
                $l["countryCode"] = $l["alfa"];
                switch ($l["lvl"]){
                    case 0:
                        $l["type"] = "pais";
                        break;
                    case 1:
                        $l["type"] = "estado";
                        break;
                    case 2:
                        $l["type"] = "municipio";
                        break;
                    case 3:
                        $l["type"] = "parroquia";
                        break;
                    case 4:
                        $l["type"] = "ciudad";
                        break;
                    case 5:
                        $l["type"] = "isla";
                        break;
                }
                $l["typeId"] = $l["lvl"];
                unset($l["alfa"]);
            }
        );
    }

    /**
     * Función usada para devolver la prioridad basada en el tipo
     * y la posicion del elemento que se envia por parametro.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param Integer $c        Posición del elemento
     * @param Integer $lvl     Nivel del destino.
     * @return integer
     */
    public function priorityPosition($lvl, $c) {

        /*
         * $lvl             Nivel de la localidad
         * $lvl => 1..3     Nivel de división politica.
         * $lvl => 100      Establecimiento.
         */
        $response = null;
        switch ($c) {
            case 4:
                if ($lvl >= 3 && $lvl < 100)
                    $response = 1;
                if ($lvl == 2)
                    $response = 0;
                if ($lvl == 1)
                    $response = 0;
                if ($lvl == 100)
                    $response = 0;
                break;
            case 3:
                if ($lvl >= 3 && $lvl < 100)
                    $response = 2;
                if ($lvl == 2)
                    $response = 1;
                if ($lvl == 1)
                    $response = 0;
                if ($lvl == 100)
                    $response = 0;
                break;
            case 2:
                if ($lvl >= 3 && $lvl < 100)
                    $response = 2;
                if ($lvl == 2)
                    $response = 1;
                if ($lvl == 1)
                    $response = 1;
                if ($lvl == 100)
                    $response = 1;
                break;
            case 1:
                if ($lvl >= 3 && $lvl < 100)
                    $response = 3;
                if ($lvl == 2)
                    $response = 2;
                if ($lvl == 1)
                    $response = 2;
                if ($lvl == 100)
                    $response = 2;
                break;
            case 0:
                if ($lvl >= 3 && $lvl < 100)
                    $response = 3;
                if ($lvl == 2)
                    $response = 3;
                if ($lvl == 1)
                    $response = 3;
                if ($lvl == 100)
                    $response = 3;
                break;
        }

        return $response;
    }
}
