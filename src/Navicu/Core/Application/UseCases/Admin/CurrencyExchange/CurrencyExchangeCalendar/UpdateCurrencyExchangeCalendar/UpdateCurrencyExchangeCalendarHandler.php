<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 22/07/16
 * Time: 08:25 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\UpdateCurrencyExchangeCalendar;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\ExchangeRateHistory;

class UpdateCurrencyExchangeCalendarHandler implements Handler
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
            $exchangeRateRf = $rf->get("ExchangeRateHistory");

            $dates = $this->getDates($request['data']['calendar']);
            $dataBd = $exchangeRateRf->getByArrayOfDates($dates);

            $data = $this->updateExchangeRate($dataBd, $request, $rf);

            $response = new ResponseCommandBus(200,'OK', $data);
        } catch( \Exception $e) {
            $response = new ResponseCommandBus(400, "\n".$e->getMessage()."\n".$e->getLine());
        }
        return $response;

    }

    /**
     * Funcion que se encarga de filtrar los datos del arreglo de objetos de fechas, a un arreglo por fechas y
     * transformarlo a un string
     *
     * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
     * @version 06/06/2016
     *
     * @param array $dailyDates arreglo obtenido de la base de datos de los DR o DP con disponibilidad.
     * @return mixed un arreglo de fechas formateado
     */
    public function getDates($calendar) {
        return array_map(function ($var) {
            return (new \DateTime($var))->format("Y-m-d");
        }, array_column(
                $calendar,
                "date")
        );
    }

    /**
     * Funcion encargada de comparar los objetos en base a las fechas ingresadas de forma ascendente.
     *
     * @param array $objDbDolar, arreglo de objetos ExchangeRateHistory del tipo USD en las fechas ingresadas
     * @param array $objDbEuro, arreglo de objetos ExchangeRateHistory del tipo EUR en las fechas ingresadas
     * @param array $dataBd, arreglo con todos los campos de la base de datos
     * @param $request
     * @return mixed
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 25/07/2016
     */
    public function getDateSame($objDbDolar, $objDbEuro, $dataBd, $request) {
        /**
         * Funcion que compara las fechas ingresadas(si uno de los dos arreglos es menor que el otro,
         * se rellena el mas pequeño con null), guardando el rate_navicu nuevo(y la fecha) o actualizar.
         */
        return array_map(function ($objDolar, $objEuro, $varDateBd, $varDateRequest, $percentage) {
            $varDateBD = new \DateTime($varDateBd);
            $varDateRq = new \DateTime("$varDateRequest 00:00:00");
            // Variables para actualizar los valores dentro de la base de datos
            if ($varDateBD == $varDateRq) {
                $result = [];
                $result['data'] = [];
                array_push($result['data'], "update");
                array_push($result['data'], $percentage);
                array_push($result['data'], $objDolar);
                array_push($result['data'], $objEuro);
                return $result;
            }
            else {
                // Variables para ingresar nuevos datos a la base de datos si no existe la fecha
                $result = [];
                $result['data'] = [];
                array_push($result['data'], "new");
                array_push($result['data'], $varDateRq);
                array_push($result['data'], $percentage);
                return $result;
            }
        },$objDbDolar, $objDbEuro, array_column(
                                    $dataBd,
                                    "date"), array_column(
                                            $request,
                                            "date"), array_column(
                                                    $request,
                                                    "percentage_navicu")
        );
    }

    /**
     * Funcion encargada de buscar y actualizar la informacion almancenada en la base de datos
     *
     * @param array $dataBd, arreglo con la informacion obtenida de la base de datos en funcion de las fechas
     * solicitadas
     * @param array $request, arreglo con los dias y las nuevos rate_navicu a actualizar
     * @param $rf
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 25/07/2016
     */
    public function updateExchangeRate($dataBd, $request, $rf) {
        $lengthDataDb = count($dataBd);
        $arrayDbDolar = [];
        $objDbDolar = [];
        $objDbEuro = [];
        $exchangeRateRf = $rf->get("ExchangeRateHistory");
        $currencyRf = $rf->get("CurrencyType");

        // Separamos en arreglos los tipos de monedas que se tienen que actualizar
        for ($ii = 0;  $ii < $lengthDataDb; $ii++) {
            if ($dataBd[$ii]->getCurrencyType()->getAlfa3() == "USD") {
                array_push($arrayDbDolar, $dataBd[$ii]->getArray());
                array_push($objDbDolar, $dataBd[$ii]);
            }
            else if ($dataBd[$ii]->getCurrencyType()->getAlfa3() == "EUR")
                array_push($objDbEuro, $dataBd[$ii]);
        }

        // Se buscan los objetos que estan en la base de datos y quieren ser modificados
        $data = $this->getDateSame($objDbDolar, $objDbEuro, $arrayDbDolar, $request['data']['calendar']);

        $lengthData = count($data);
        for ($ii = 0; $ii < $lengthData; $ii++) {
            $exchangeToUpdate = $data[$ii]['data'];
            // Se actualizan (de ser necesario) los objetos que se habian extraido de la base de datos
            if ($exchangeToUpdate[0] == "update") {
                $percentage = $exchangeToUpdate[1]; //posicion del porcentaje
                $exchangeToUpdate[2]->setPercentageNavicu($percentage); //posicion del objeto USD
                $rate_percentage = ($percentage/100) * $exchangeToUpdate[2]->getRateApi();
                $rateTotal = $exchangeToUpdate[2]->getRateApi() - $rate_percentage;
                $exchangeToUpdate[2]->setRateNavicu($rateTotal);

                $exchangeToUpdate[3]->setPercentageNavicu($percentage); //posicion del objeto EUR
                $rate_percentage = ($percentage/100) * $exchangeToUpdate[3]->getRateApi();
                $rateTotal = $exchangeToUpdate[3]->getRateApi() - $rate_percentage;
                $exchangeToUpdate[3]->setRateNavicu($rateTotal);
            }
            else //Se crean las fechas nuevas solo con el rate_navicu en la base de datos
                $this->createNewExchangeRateHistory($exchangeToUpdate, $currencyRf, $exchangeRateRf);
        }
        $exchangeRateRf->flushDb();
    }

    /**
     * Funcion encargada de actualizar o crear las nuevas entradas a la base de datos
     *
     * @param array $exchangeToUpdate, arreglo con la informacion de los objetos a ser actualizado
     * @param $currencyRf
     * @param $exchangeRateRf
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 25/07/2016
     */
    public function createNewExchangeRateHistory($exchangeToUpdate, $currencyRf, $exchangeRateRf)
    {
        $exchange = [];
        array_push($exchange, "USD");
        array_push($exchange, "EUR");

        $lengthExchange = count($exchange);
        for ($ii = 0; $ii < $lengthExchange; $ii++) {
            $newExchangeRate = new ExchangeRateHistory();

            $newExchangeRate->setDate($exchangeToUpdate[1]);
            $selectedCurrency = $currencyRf->findOneBy(array('alfa3' => $exchange[$ii] ));
            $newExchangeRate->setCurrencyType($selectedCurrency);
            $newExchangeRate->setPercentageNavicu($exchangeToUpdate[2]);

            $selectedCurrency->addExchangeRateHistory($newExchangeRate);
            $exchangeRateRf->persistDb($newExchangeRate);
        }
    }
}