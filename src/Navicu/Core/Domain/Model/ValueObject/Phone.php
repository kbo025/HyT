<?php

namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *
 * Se define una clase objeto mvalor que modela una direccion de correo electronico en un estado valido
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 21/05/2015
 * 
 */

class Phone
{
    /**
     * @var String $number numero de telefono
     */
    private $number;

        /**
     * @var String $number numero de telefono
     */
    private $country;

    /**
     * Metodo Constructor de php
     *
     * @param   String  $tlf 	string con el numero de telefono
     *
     */    
	public function __construct($tlf)
	{

		if (empty(preg_replace('(^\+?\d{7,20})','',$tlf))) {
			$this->number = $tlf;
		} else {
			throw new \Exception('Formato de telefono invalido');
		}
	}

    /**
     * devuelve una representacion del numero de telefono en string
     *
     * @return   String
     */
    public function toString(){
    	return $this->number;
    }
}