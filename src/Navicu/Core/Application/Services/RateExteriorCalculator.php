<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 18/08/16
 * Time: 02:50 PM
 */

namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\NotAvailableException;
use Navicu\Core\Domain\Model\Entity\CurrencyType;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\EntityValidationException;


class RateExteriorCalculator
{
    /**
     * @var string, Moneda seleccionada para calcular el cambio
     */
    static private $coinSelected;

    /**
     * @var object, Arreglo con objetos de la bd con los ultimos valores cargados por navicu ordenados por fecha desc
     */
    static private $lastRateChargedByNavicu;

    /**
     * @var object, arreglo con objetos de la bd del dia de hoy cargados por la api
     */
    static private $todayExchangeRate;

    /**
     * @var object, Arreglo de un objeto de la bd que indicara si la moneda esta activa o no
     */
    static private $validCoin;

    /**
     * RateExteriorCalculator constructor.
     * @param $newCoinOut
     * @param RepositoryFactoryInterface $rf
     * @param object $dateReservation, fecha en la cual se solicita hacer una reserva
     */
    public function __construct(RepositoryFactoryInterface $rf, $newCoinOut = null, $dateReservation = null)
    {
        self::setCurrency($rf,$newCoinOut, $dateReservation);
    }

    /**
     * Funcion encargada de transformar un monto de bolivares en otra moneda
     *
     * @param array $sellRate, monto a convetir de bolivares a la moneda cargada en el constructor
     * @param bool $is_round
     * @return float|int
     * @version 23/08/2016
     * @author Isabel Nieto <isabelnieto@gmail.com>
     */
    public static function calculateRateChange($sellRate, $is_round = true)
    {
        // Verificamos que sea una moneda valida
        $currency = self::getValidCoin();

        if (self::$coinSelected == 'VEF')
            return round($sellRate);

        $alphaCurrency = self::getCoinSelected();
        $totalAmount = -1;

        // Si se encontro que era una moneda valida
        if (count($currency) > 0) {
            // Buscamos el ultimo precio donde el rate_navicu tuvo un valor en dolares o en euros
            $priceInBsOfOneDollarOrEur = self::getLastRateChargedByNavicu();

            //Obtenemos los ultimos valores de la api para el dia de hoy
            $todayExchangeRate = self::getTodayExchangeRate();

            // Por todas las monedas posibles con estado activo, obtenemos su equivalente en bolivares
            // o lo que equivale un dolar en la moneda seleccionada
            foreach ($todayExchangeRate as $exchangeRate) {
                if ($exchangeRate->getCurrencyType()->getAlfa3() == $alphaCurrency)
                    $priceRateToConvert = $exchangeRate->getRateApi();
            }
            // Convertimos a Euros, en caso de ser otra moneda convertimos de bolivares a Dolares
            // y sacamos la cuenta en base a los dolares
            if ($alphaCurrency == 'EUR')
                $totalEur = ($sellRate / $priceInBsOfOneDollarOrEur);
            else {
                $totalDollar = ($sellRate / $priceInBsOfOneDollarOrEur);
                if ($alphaCurrency != 'USD') // Si el cambio se tiene que hacer a otra moneda diferente del Dolar
                    $totalOtherRate = ($totalDollar * $priceRateToConvert);
            }
            // Si se pide con redondeo
            if ($is_round) {
                if ($alphaCurrency == 'USD')
                    $totalAmount = round($totalDollar, $currency->getRound());
                else if ($alphaCurrency == 'EUR')
                    $totalAmount = round($totalEur, $currency->getRound());
                else
                    $totalAmount = round($totalOtherRate, $currency->getRound());
            }
            else {
                if ($alphaCurrency == 'USD')
                    $totalAmount = $totalDollar;
                else if ($alphaCurrency == 'EUR')
                    $totalAmount = $totalEur;
                else
                    $totalAmount = $totalOtherRate;
            }
        }
        return $totalAmount;
    }

    /**
     * Funcion encargada de convertir un precio de X moneda a Bolivares
     *
     * @param float $sellRate, Monto total en X moneda a convertir en Bolivares
     * @param bool $round
     * @version 22/08/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return float|int
     */
    public static function calculateRateChangeToBs($sellRate, $is_round = true)
    {
        // Verificamos que sea una moneda valida
        $currency = self::getValidCoin();
        $round = $is_round ? 1 : null;

        if (self::$coinSelected == 'VEF')
            return round($sellRate);

        $alphaCurrency = self::getCoinSelected();
        $totalAmount = -1;

        // Si se encontro que era una moneda valida
        if (count($currency) > 0) {
            // Buscamos el ultimo precio donde el rate_navicu tuvo un valor en dolares o en euros
            $priceInBsOfOneDollarOrEur = self::getLastRateChargedByNavicu();

            //Obtenemos los ultimos valores de la api para el dia de hoy
            $todayExchangeRate = self::getTodayExchangeRate();

            // Por todas las monedas posibles con estado activo, obtenemos su equivalente en bolivares
            // o lo que equivale la moneda seleccionada en dolares
            foreach ($todayExchangeRate as $exchangeRate) {
                if ($exchangeRate->getCurrencyType()->getAlfa3() == $alphaCurrency)
                    $priceRateToConvert = $exchangeRate->getRateApi();
            }
            // Convertimos de Euros a Bs, en caso de ser otra moneda convertimos de Dolares a Bolivares
            // y sacamos la cuenta en base a los dolares
            if ($alphaCurrency == 'EUR')
                $totalEurInBs = ($sellRate * $priceInBsOfOneDollarOrEur);
            else if ($alphaCurrency == 'USD')
                $totalDollarInBs = ($sellRate * $priceInBsOfOneDollarOrEur);
            else { // Si el cambio se tiene que hacer desde otra moneda diferente del Dolar
                $totalOtherRateInDollar = ($sellRate / $priceRateToConvert);
                $totalDollarInBs = ($totalOtherRateInDollar * $priceInBsOfOneDollarOrEur);
            }

            // Si se pide con redondeo
            if ($is_round) {
                if ($alphaCurrency == 'EUR')
                    $totalAmount = round($totalEurInBs, $currency->getRound());
                else
                    $totalAmount = round($totalDollarInBs, $currency->getRound());
            } else {
                if ($alphaCurrency == 'EUR')
                    $totalAmount = $totalEurInBs;
                else
                    $totalAmount = $totalDollarInBs;
            }
        }
        return $totalAmount;
    }

    /**
     * Moneda a la cual se le hara el cambio
     * @return mixed
     */
    public static function getCoinSelected()
    {
        return self::$coinSelected;
    }

    /**
     * @param mixed $coinSelected
     */
    public static function setCoinSelected($coinSelected)
    {
        self::$coinSelected = $coinSelected;
    }

    /**
     * Ultimo precio donde el rate_navicu tuvo un valor en dolares o en euros
     * @return mixed
     */
    public static function getLastRateChargedByNavicu()
    {
        return self::$lastRateChargedByNavicu;
    }

    /**
     * @param mixed $lastRateChargedByNavicu
     */
    public static function setLastRateChargedByNavicu($lastRateChargedByNavicu)
    {
        self::$lastRateChargedByNavicu = $lastRateChargedByNavicu;
    }

    /**
     * Ultimo precio cargado de las api's en dolares o cambio paralelo en bolivares
     * @return mixed
     */
    public static function getTodayExchangeRate()
    {
        return self::$todayExchangeRate;
    }

    /**
     * @param mixed $todayExchangeRate
     */
    public static function setTodayExchangeRate($todayExchangeRate)
    {
        self::$todayExchangeRate = $todayExchangeRate;
    }

    /**
     * Moneda ingresada a verificar si esta activa o no
     * @return mixed
     */
    public static function getValidCoin()
    {
        return self::$validCoin;
    }

    /**
     * @param mixed $validCoin
     */
    public static function setValidCoin($validCoin)
    {
        self::$validCoin = $validCoin;
    }

    public static function setCurrency(RepositoryFactoryInterface $rf, $newCoinOut = null, $dateReservation = null)
    {
        $exchangeRateRf = $rf->get('ExchangeRateHistory');
        $currencyTypeRf = $rf->get('CurrencyType');
        // Si la moneda viene definida
        if ($newCoinOut)
            self::$coinSelected = strtoupper($newCoinOut);
        else
            self::$coinSelected = strtoupper(CoreSession::get("alphaCurrency"));

        //Obtenemos si la moneda esta activa o no dentro de la base de datos
        self::$validCoin = $currencyTypeRf->findOneBy(array("alfa3" => self::$coinSelected, "active" => "true"));

        if (count(self::$validCoin) > 0) {
            $today = new \DateTime("now 00:00:00");

            // Si no es por una reserva sino para mostrar el mapa (por ejemplo) la fecha debe ser de hoy
            if (is_null($dateReservation))
                $dateReservation = new \DateTime("now 00:00:00");

            // Buscamos el ultimo precio donde el rate_navicu tuvo un valor en euros
            if (strcmp(self::$coinSelected,'EUR') == 0)
                $newRateNavicu = $exchangeRateRf->getLastRateNavicuInBs($dateReservation, $today, self::$validCoin->getId());
            else //buscamos el cambio para el dolar asi sea otra divisa la que este solicitando
                $newRateNavicu = $exchangeRateRf->getLastRateNavicuInBs($dateReservation, $today, 145);

            //Guardamos el ultimo valor donde el rate_api fue cargado y dio como resultado el rate_navicu
            self::$lastRateChargedByNavicu = $newRateNavicu[0]['new_rate_navicu'];
            
            //Obtenemos los ultimos valores de la api para el dia de hoy
            self::$todayExchangeRate = $exchangeRateRf->findByLastDate($today);
        }
    }
}