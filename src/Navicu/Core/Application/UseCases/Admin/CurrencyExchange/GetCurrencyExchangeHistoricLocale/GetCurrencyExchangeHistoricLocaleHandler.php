<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 27/07/16
 * Time: 02:57 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\GetCurrencyExchangeHistoricLocale;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetCurrencyExchangeHistoricLocaleHandler implements Handler
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
            $response = new ResponseCommandBus(500, "Something went wrong","\n".$e->getMessage()."\n".$e->getLine());
        }
        return $response;
    }

    /**
     * Funcion encargada de armar una estructura por moneda del valor que se tenga en esa fecha
     *
     * @param array $request, arreglo con los dos dias que establecen el rango de fechas a buscar
     * @param $rf
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 28/07/2016
     * @return array con la estructura por moneda, organizada por menor fecha y cambio en bolivares de la api
     */
    public function generateStructure($request, $rf)
    {
        $exchangeRateRf = $rf->get("ExchangeRateHistory");
        $response = [];
        $objCompleted = [];
        $obj = [];

        if (isset($request['data']['first_date'])) {
            $firstDate = $request['data']['first_date'];
            $firstDate = new \DateTime("$firstDate 00:00:00");
        }
        else
            $firstDate = null;

        if (isset($request['data']['last_date'])) {
            $lastDate = $request['data']['last_date'];
            $lastDate = new \DateTime("$lastDate 00:00:00");
        }
        else
            $lastDate = new \DateTime("now 00:00:00");

        //fecha maxima a buscar en la bd
        $currentDate = new \DateTime("now 00:00:00");

        /* Si la fecha introducida es mayor a la de hoy, se asigna la ultima fecha como hoy*/
        if ( $lastDate->diff($currentDate)->invert > 0)
            $lastDate = $currentDate;
        $data = $exchangeRateRf->getByDateRangeInBs($firstDate, $lastDate, "desc");

        // Comenzamos la busqueda por la primera fecha encontrada en la base de datos
        if (count($data) > 0) {
            $currentDate = $data[0]->getDate();
            foreach ($data as $row) {
                if ($currentDate->diff($row->getDate())->d > 0) {
                    $obj['date'] = $currentDate->format('d-m-Y');
                    $obj['currency'] = $objCompleted;
                    if (empty($response['historic']))
                        $response['historic'] = [];
                    // Insertamos al inicio el objeto construido
                    array_unshift($response['historic'], $obj);

                    /* Reiniciamos los valores para la siguiente iteracion */
                    $objCompleted = [];
                    $currentDate = $row->getDate();
                }
                $objApi["rate_api"] = (!is_null($row->getRateApi())) ? $row->getRateApi() : null;
                $objApi["name"] = $row->getCurrencyType()->getTitle();
                $objApi['sicad_2'] = (!is_null($row->getDicom())) ? $row->getDicom() : null;
                $objApi['rate_navicu'] = (!is_null($row->getRateNavicu())) ? $row->getRateNavicu(): null;
                $objApi['percentage_navicu'] = (!is_null($row->getPercentageNavicu())) ? $row->getPercentageNavicu() : null;

                array_push($objCompleted, $objApi);
                $objApi = [];
            }
            // Asignamos los objetos generados del recorrido por fechas
            $obj['date'] = $currentDate->format('d-m-Y');
            $obj['currency'] = $objCompleted;
            // Insertamos al inicio el objeto construido
            if (empty($response['historic']))
                $response['historic'] = [];
            array_unshift($response['historic'], $obj);

        }
        return $response;
    }
}