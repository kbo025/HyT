<?php

namespace Navicu\Core\Domain\Model\ValueObject;


/**
 *
 * Se define una clase Objeto Valor que modela y valida un horario
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @author 04/06/2015
 * 
 */
class Schedule
{
	/**
     * @var Hora de entrada/apertura
     */
	private $opening;

	/**
     * @var Hora de salida/clausura
     */
	private $closing;

    /**
    * @var integer;
    */
    private $days;

    /**
    * @var integer
    */
    private $full_time;


    /**
     * Metodo Constructor de php
     *
     * @param mixed $opening
     * @param mixed $closing
     * @param array $days
     * @param bool $full_time
     * @throws \Exception
     */
	public function __construct($opening = null, $closing = null, $days = null, $full_time = false) {

        $this->full_time = !empty($full_time);
        $suma = 0;

        if (!$this->full_time && (empty($opening) ||  empty($closing)) && empty($days))
            throw new \Exception("Invalid schedule");

        if($opening instanceof \DateTime)
            $this->opening = $this->full_time ? null : $opening;
        else
            if(is_string($opening))
                $this->opening = $this->full_time ? null : new \DateTime($opening);

        if ($closing instanceof \DateTime)
            $this->closing = $this->full_time ? null : $closing;
        else
            if(is_string($closing))
                $this->closing = $this->full_time ? null : new \DateTime($closing);

        if (!empty($days)) {
            if (is_array($days)) {
                $j = 6;
                for ($i = 0; $i<7; $i++) {
                    if ($days[$i]) {
                        $suma = $suma + pow(2, $j);
                    }
                    $j--;
                }
            } else {
                if (is_integer($days) && ($days > 0) && ($days < 128)) {
                    $suma = $days;
                } else {
                    throw new \Exception("Invalid format for field 'days'".$days);
                }
            }
            $this->days = $suma;
        }
	}

    /**
     * Devuelve la representacion del objeto en un string en formato JSON
     * @return String 
     */
	public function toString()
	{
		return json_encode($this->toArray());
	}

    /**
     * Devuleve la representacion del objeto en un array
     * @return String
     */
	public function toArray()
	{
        $res = array();
        $res['opening'] = !empty($this->opening) ? date_format($this->opening, 'H:i:s') : null;
        $res['closing'] = !empty($this->closing) ? date_format($this->closing, 'H:i:s') : null;
        $res['full_time'] = $this->full_time;
        $res['days'] = (!empty($this->days)) ? $this->days : null;

		return $res;
	}

    /**
     * Devuelve un array con numeros enteros que representan los dias de la semana
     * escogidos donde:
     *  1: Lunes,
     *  2: Martes,
     *  3: Miercoles,
     *  4: Jueves,
     *  5: Viernes,
     *  6: Sabado,
     *  7: Domingo
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 14-09-15
     * @return array
     */
    public function getDays()
    {
        $response = [];
        if (!empty($this->days)) {
            $days = decbin($this->days);
            if (strlen($days) < 7) {
                $days = substr('0000000', 0, 7 - strlen($days)).$days;
            }

            $days = str_split($days);

            for ($i = 0; $i < count($days); $i++)
                $response[$i] = ($days[$i] == '1');
        }
        return $response;
    }

    /**
     * devuelve el horario de apertura del objeto
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 08-12-15
     * @return array
     */
    public function getOpening()
    {
        return $this->opening;
    }

    /**
     * devuelve el horario de cierre del objeto
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 08-12-15
     * @return array
     */
    public function getClosing()
    {
        return $this->closing;
    }
}