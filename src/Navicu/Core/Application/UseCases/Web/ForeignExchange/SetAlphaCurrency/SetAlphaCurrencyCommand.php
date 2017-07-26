<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 17/08/16
 * Time: 01:42 PM
 */

namespace Navicu\Core\Application\UseCases\Web\ForeignExchange\SetAlphaCurrency;

use Navicu\Core\Application\Contract\Command;

class SetAlphaCurrencyCommand implements Command
{

    /**
     * @var string varible que indica a que moneda debe cambiarse la session
     */
    private $alphaCurrency;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->alphaCurrency = isset($data["alphaCurrency"]) ? strtoupper($data["alphaCurrency"]) : "USD";
    }

    public function setCurrentRate($rate)
    {
        $this->alphaCurrency = $rate;
    }

    public function getCurrentRate()
    {
        return $this->alphaCurrency;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return ['alphaCurrency' => $this->alphaCurrency];
    }
}