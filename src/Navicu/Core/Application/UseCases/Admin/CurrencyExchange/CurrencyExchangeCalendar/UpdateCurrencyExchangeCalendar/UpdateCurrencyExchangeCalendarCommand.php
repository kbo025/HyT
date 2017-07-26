<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 22/07/16
 * Time: 08:23 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\UpdateCurrencyExchangeCalendar;


use Navicu\Core\Application\Contract\Command;

class UpdateCurrencyExchangeCalendarCommand implements  Command
{

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    protected $data;

    public function __construct($data = null) {
        $this->data=$data;
    }

    /**
     * Retorna la informacion requerida
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @return array
     */
    public function getRequest() {
        return array(
            'data'=>$this->data
        );
    }
}