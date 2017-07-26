<?php
/**
 * Created by Isabel Nieto <isabelcnd@gmail.com>.
 * Date: 21/07/16
 * Time: 09:26 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\GetCurrencyExchangeCalendar;
use Navicu\Core\Application\Contract\Command;

class GetCurrencyExchangeCalendarCommand implements Command
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