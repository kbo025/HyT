<?php

namespace Navicu\Core\Domain\Model\ValueObject;


/**
 *
 * Se define una clase Objeto Valor que modela y valida un password de usuario
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho (05-05-15)
 * 
 */
class Password
{
	/**
     * 
     * @var String $password 	almacena un password considerado valido.
     *  
     */
	private $password;


    /**
     * Metodo Constructor de php
     *
     * @param   String  $pass    cadena de caracteres que representa un password
     *
     */
	public function __construct($pass)
	{
		if(strlen($pass)>5){
			$num=ereg_replace("[^0-9]", "", $pass);
			if(!empty($num)){
				$min=ereg_replace("[^a-z]", "", $pass);	
				if(!empty($min)){
					//$may=ereg_replace("[^A-Z]", "", $pass);
					//if(!empty($may)){
						$this->password=$pass;
					//}else{
						//throw new \Exception(sprintf('"%s" is not a valid password', $pass));
					//}
				}else{
					throw new \Exception(sprintf('share.message.password_error', $pass));
				}
			}else{
				throw new \Exception(sprintf('share.message.password_error', $pass));
			}
		}else{
			throw new \Exception(sprintf('share.message.password_error_length', $pass));
		}
	}

    /**
     * Devuleve el password como un String
     *
     * @return   String     cadena de caracteres que representa un password
     *
     */
	public function  toString()
	{
		return $this->password;
	}
}