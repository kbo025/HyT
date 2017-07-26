<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 27/07/16
 * Time: 09:23 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeGraphic;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetCurrencyExchangeGraphicHandler implements Handler
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
        try {
            $request = $command->getRequest();

            $data = $this->generateStructure($request, $rf);
            $response = new ResponseCommandBus(200,'OK', $data);
        } catch( \Exception $e) {
            $response = new ResponseCommandBus(500, "\n".$e->getMessage()."\n".$e->getLine());
        }
        return $response; 
    }

    /**
     * Funcion encargada de armar una estructura por moneda del valor que se tenga en esa fecha
     *
     * @param array $request, arreglo con los dos dias que establecen el rango de fechas a buscar
     * @param $rf
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 27/07/2016
     * @return array con la estructura por moneda, organizada por menor fecha y cambio en bolivares de la api
     */
    public function generateStructure($request, $rf)
    {
        $exchangeRateRf = $rf->get("ExchangeRateHistory");
        $response = [];

        $firstDate = $request['data']['first_date'];
        $lastDate = $request['data']['last_date'];
        $today = new \DateTime("now 00:00:00");
        $response['today'] = $today->format("d-m-Y");

        /* Buscamos la coincidencia con la moneda USD*/
        $value = array_map(function ($var) {
            if (strcmp($var->getArray()['currency_type'],'USD') == 0) 
                return $var->getArray()['rate_api'];
        }, $exchangeRateRf->findByLastDate($today));

        foreach ($value as $value_to_save) {
            if ($value_to_save) {
                $response['last_rate_api'] = $value_to_save;
                break;
            }
        }

        $firstDate = new \DateTime("$firstDate 00:00:00");
        $lastDate = new \DateTime("$lastDate 00:00:00");
        if ($firstDate->diff($lastDate)->d > 0) {
            $data = $exchangeRateRf->getByDateRange($firstDate, $lastDate);

            foreach ($data as $row) {
                $index = "rate_" . $row->getCurrencyType()->getAlfa3();
                // Por ahora solo se tomaran los cambios para dolar y euro
                if (($row->getCurrencyType()->getAlfa3() == 'USD') || ($row->getCurrencyType()->getAlfa3() == 'EUR')) {
                    if (!isset($response[$index])) {
                        $response[$index] = [];
                        $response["rate_navicu"] = [];
                        $response["rate_sicad_2"] = [];
                    }
                    $date = $row->getDate()->format('d-m-Y');
                    // Objeto para el currency seleccionado
                    $newObj = [];
                    $newObj['date'] = $date;
                    $newObj['value'] = $row->getRateApi();
                    array_push($response[$index], $newObj);

                    // Objeto equivalente a la tasa navicu del currency seleccionado
                    $newObj = [];
                    $newObj['date'] = $date;
                    $newObj['value'] = $row->getRateNavicu();
                    array_push($response["rate_navicu"], $newObj);

                    // Objeto equivalente al cambio oficial del currency seleccionado
                    $newObj = [];
                    $newObj['date'] = $date;
                    $newObj['value'] = $row->getDicom();
                    array_push($response["rate_sicad_2"], $newObj);
                }
            }
        }
        return $response;
    }
}