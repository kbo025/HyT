<?php
/**
 * Created by Isabel Nieto.
 * Date: 19/07/16
 * Time: 04:59 PM
 */

namespace Navicu\Core\Domain\Repository;


interface ExchangeRateHistory
{
    /**
     *	Metodo que devuelve un objeto ExchangeRateHistory por su id
     *	@param Integer
     *	@return Category
     */
    public function getById($id);

    public function save($data);

    public function persistDb($newExchangeRate);

    public function flushDb();

    public function getByArrayOfDates($arrayOfDays);

    public function getByIniDate($requestDate, $twoMothLater);

    public function getFirstDate();

    public function getLastDate();

    public function getByDateRange($firstDate, $lastDate);

    public function getByDateRangeInBs($firstDate, $lastDate, $orderAscOrDesc);

    public function getByDateRangeDescNotBs($firstDate, $lastDate);

    public function findByDate($today);

    public function findByLastDate($today);

    public function getLastPriceInBs($lastDate, $orderAscOrDesc);
}

