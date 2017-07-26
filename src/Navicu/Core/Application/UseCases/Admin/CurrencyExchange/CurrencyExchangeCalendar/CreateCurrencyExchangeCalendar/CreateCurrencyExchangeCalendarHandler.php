<?php
/**
 * Created by Isabel Nieto.
 * Date: 20/07/16
 * Time: 01:30 PM
 */
namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\CreateCurrencyExchangeCalendar;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\ExchangeRateHistory;

/**
 * Clase para cargar los valores de las apis a nuestras base de datos
 *
 * @author Isabel Nieto <isabelcnd@gmail.com>
 *
 * Class SetCurrencyExchangeCalendarHandler
 * @package Navicu\Core\Application\UseCases\Admin\CurrencyExchangeCalendar\CreateCurrencyExchangeCalendar
 */
class CreateCurrencyExchangeCalendarHandler implements Handler
{
    // Porcentaje por defecto a aplicar a las nuevas entradas
    const DEFAULT_PERCENTAGE = 25;
    // Porcentaje nuevo si la tasa dicom es mayor a la de navicu
    const NEW_PERCENTAGE = 10;
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
            $data = $this->getInfoFromApis();

            $data = $this->generateExchangeRate($data, $rf);
            $response = new ResponseCommandBus(200, 'Ok', $data);
        }catch( \Exception $e) {
            $response = new ResponseCommandBus(500, "\n".$e->getMessage()."\n".$e->getLine());
            echo "\n".$e->getMessage()."\n".$e->getLine();
        }
        return $response;
    }

    /**
     * Funcion que invoca una peticion a la API de dolar today
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 19/07/2016
     *
     * @return array con la informacion de Dolar today
     */
    public function getInfoFromApis()
    {
        $response = [];
        $petition = curl_init();
        // definimos la URL a la que hacemos la petición
        curl_setopt($petition, CURLOPT_URL,"https://s3.amazonaws.com/dolartoday/data.json");
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($petition, CURLOPT_RETURNTRANSFER, true);
        // Indicamos que el resultado lo devuelva curl_exec() por medio de la variable $petition
        $data = curl_exec ($petition);

        $data = json_decode(utf8_decode($data), true);

        $response['USD']['rate_api'] = $data['USD']['dolartoday'];
        // Valor por defecto del porcentaje de descuento
        $response['USD']['percentage_navicu'] = $this::DEFAULT_PERCENTAGE;
        $discount = (($this::DEFAULT_PERCENTAGE/100) * $response['USD']['rate_api']);
        $total = $response['USD']['rate_api'] - $discount;
        $response['USD']['rate_navicu'] = round($total, 3, PHP_ROUND_HALF_UP);
        $response['USD']['dicom'] = $data['USD']['sicad2'];

        $response['EUR']['rate_api'] = $data['EUR']['dolartoday'];
        $response['EUR']['percentage_navicu'] = $this::DEFAULT_PERCENTAGE;
        $discount = (($this::DEFAULT_PERCENTAGE/100) * $response['EUR']['rate_api']);
        $total = $response['EUR']['rate_api'] - $discount;
        $response['EUR']['rate_navicu'] = round($total, 3, PHP_ROUND_HALF_UP);
        $response['EUR']['dicom'] = $data['EUR']['sicad2'];

        // Api con la informacion adicional de las monedas faltantes
        curl_setopt($petition, CURLOPT_URL,"http://www.floatrates.com/daily/usd.json");
        curl_setopt($petition, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec ($petition);
        $data = json_decode($data, true);

        foreach ($data as $value) {
            if ($value['code'] != 'EUR') {
                $code = $value['code'];
                $response[$code]['rate_api'] = round($value['rate'], 3, PHP_ROUND_HALF_UP);
            }
        }

        // cerramos la sesión cURL
        curl_close ($petition);

        return $response;
    }

    /**
     * Funcion encargada de generar entradas en la entidad de exchange_rate_history
     *
     * @param string $data, array con los valores de la api de dolar today
     * @param $rf
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 20/07/2016
     * @return int
     */
    public function generateExchangeRate($data, $rf)
    {
        $exchangeRateRf = $rf->get("ExchangeRateHistory");
        $currency = $rf->get("CurrencyType");

        $today = new \DateTime("now 00:00:00");
        $dataDb = $exchangeRateRf->findByDate($today->format('Y-m-d'));

        foreach ($data as $key => $value) {
            $newExchangeRate = new ExchangeRateHistory();
            $selectedCurrency = $currency->findOneBy(array('alfa3'=>$key, 'active'=>true));
            // Si consiguio una moneda que esta en la base de datos
            if ($selectedCurrency) {
                //Si se encontro la fecha de hoy ya agregada, la moneda seleccionada es USD or EUR,
                //para este caso se actualiza ese registro
                if ( (count($dataDb) ) AND
                    (
                        ($dataDb[0]->getCurrencyType()->getId() == $selectedCurrency->getId()) OR
                        ($dataDb[1]->getCurrencyType()->getId() == $selectedCurrency->getId())
                    )
                ) {// Casos para la moneda o Usd o Eur
                    if ($selectedCurrency->getId() == $dataDb[0]->getCurrencyType()->getId()) {
                        $dataDb[0]->setRateApi($value['rate_api']);
                        $dataDb[0]->setDicom($value['dicom']);
                        if ($dataDb[0]->getPercentageNavicu()) {
                            $discount = (($dataDb[0]->getPercentageNavicu()/100) * $dataDb[0]->getRateApi());
                            $total = $dataDb[0]->getRateApi() - $discount;
                            // Si el rate_navicu es mayor al dicom se guarda en la base de datos
                            if ($total > $value['dicom'])
                                $dataDb[0]->setRateNavicu(round($total, 3, PHP_ROUND_HALF_UP));
                            // sino se recalcula tanto el porcentaje como el monto a guardar por rate_navicu
                            else {
                                $dataDb[0]->setPercentageNavicu($this::NEW_PERCENTAGE);
                                $newDiscount = (($this::NEW_PERCENTAGE/100) * $dataDb[0]->getRateApi());
                                $newTotal = $dataDb[0]->getRateApi() - $newDiscount;
                                $dataDb[0]->setRateNavicu(round($newTotal, 3, PHP_ROUND_HALF_UP));
                            }
                        }
                    }
                    else if ($selectedCurrency->getId() == $dataDb[1]->getCurrencyType()->getId()){
                        $dataDb[1]->setRateApi($value['rate_api']);
                        $dataDb[1]->setDicom($value['dicom']);
                        if ($dataDb[1]->getPercentageNavicu()) {
                            $discount = (($dataDb[1]->getPercentageNavicu()/100) * $dataDb[1]->getRateApi());
                            $total = $dataDb[1]->getRateApi() - $discount;
                            if ($total > $value['dicom'])
                                $dataDb[1]->setRateNavicu(round($total, 3, PHP_ROUND_HALF_UP));
                            else {
                                $dataDb[1]->setPercentageNavicu($this::NEW_PERCENTAGE);
                                $newDiscount = (($this::NEW_PERCENTAGE/100) * $dataDb[1]->getRateApi());
                                $newTotal = $dataDb[1]->getRateApi() - $newDiscount;
                                $dataDb[1]->setRateNavicu(round($newTotal, 3, PHP_ROUND_HALF_UP));
                            }
                        }
                    }
                }
                else { //sino, se agrega una nueva fila a la base de datos
                    $data[$key]['currency_type'] = $selectedCurrency;
                    $newExchangeRate->updateObject($data[$key]);
                    $selectedCurrency->addExchangeRateHistory($newExchangeRate);
                    $exchangeRateRf->persistDb($newExchangeRate);
                }
            }
        }
        $exchangeRateRf->flushDb();
        return 0;
    }
}