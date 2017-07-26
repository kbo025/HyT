<?php
namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *  esta clase es un Objeto Valor que genera un numero aleatorio que funciona como id publica
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 03/09/2015
 */
class PublicId {

    /**
     * @var string $id
     */
    private $id;

    /**
     * 	constructor de la clase
     *	@param string $pre
     */
    public function __construct( $strategy, $pre=null )
    {
        $respre = isset($pre) ? $pre : '';
        if ($strategy=='date')
            $generate = self::strategyDate();
        else if ($strategy=='dateRandom')
            $generate = self::strategyDateRandom();
        else if ($strategy=='dateHex')
            $generate = self::strategyDateHex();
        else
            throw new \Exception('invalid_argument');
        $this->id = $respre.$generate;
    }

    /**
     *  Método strategyDateRandom genera un numero aleatorio entre 0 y 99 y lo concatena
     * con los ultimos 5 digitos de una representacion de la fecha actual en segundos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 03/09/2015
     * @return  string
     */
    private static function strategyDateRandom()
    {
        $res = strtotime(date('d-m-Y H:i:s'));
        $res = substr($res,-5);
        $res = rand(0,99).$res;
        return $res;
    }

    /**
     *  Método strategyDate genera un numero aleatorio entre 0 y 99 y lo concatena
     * con los ultimos 5 digitos de una representacion de la fecha actual en segundos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 03/09/2015
     * @return  string
     */
    private static function strategyDate()
    {
        $res = strtotime(date('d-m-Y H:i:s'));
        //$res = substr($res,-5);
        return $res;
    }

    /**
     *  Método strategyDateHex genera como public-id una marca de tiempo en hexadecimal
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 03/09/2015
     * @return  string
     */
    private static function strategyDateHex()
    {
        $res = strtotime(date('d-m-Y H:i:s'));
        return \dechex($res);
    }

    /**
     *  Método toString devuelve la representacion del objeto como un string
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 03/09/2015
     * @return  string
     */
    public function toString()
    {
        return $this->id;
    }
} 