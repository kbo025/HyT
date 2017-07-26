<?php

namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *
 * Se define una clase objeto mvalor que modela Url en estado valido
 * 
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * 
 */
class Url
{
    /**
     * @var String $url almacena una url considerada Valida.
     *  
     */
    private $url;

    /**
     * Metodo Constructor de php
     *
     * @param   String  $url    cadena de caracteres que representa una url
     *
     */    
    public function __construct($url)
    {   
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Url invalida');
        }

        $this->url = $url;
    }

    /**
     * devuelve la url en su representacion de string
     *
     * @return   String     
     */
    public function toString(){
    	return $this->url;
    }
}