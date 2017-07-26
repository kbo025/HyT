<?php

namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *
 * Se define una clase objeto mvalor que modela una direccion de correo electronico en un estado valido
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho (05-05-15)
 * 
 */
class EmailAddress
{
    /**
     * 
     * @var String $address    almacena una direccion de correo electronico considerada Valida.
     *  
     */
    private $address;

    /**
     * Metodo Constructor de php
     *
     * @param   String  $address    cadena de caracteres que representa una direccion de correo electrónico
     *
     */    
    public function __construct($address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email invalido');
        }
        $this->address = $address;
    }

    /**
     * devuelve el objeto email en su representacion string
     *
     * @return   String     retorna cadena de caracteres que representa una direccion de correo electronico en un
     *                      estado Valido
     *
     */
    public function toString(){
    	return $this->address;
    }
}