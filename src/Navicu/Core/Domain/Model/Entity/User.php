<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;

/**
 * Se define una clase para el modelo y gestion del usuario fantasma (usuario hotelero en proceso de registro)
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho (05-05-15)
 */
class User
{
     /**
     * @var integer $id Identificador del objeto
     */
     protected $id;

	/**
     * @var String $username 	almacena el nombre de usuario e identifica al usuario. 
     */	
	protected $username;

	/**
     * @var Object $email 		almacena un Objeto Valor tipo email y representa la direccion de correo electronico del usuario.
     */
	protected $email;

	/**
     * @var Object $password 	     almacena un Objeto Valor tipo email y representa la direccion de correo electronico del usuario.
     */
	protected $password;

     /**
     * @var Object $rol            Indentifica al usuario bajo un rol que le permite tener acceso a activar casos de uso.
     */
     protected $rol;

    /**
     * Metodo Constructor de la clase
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param  String         $username      cadena de caracteres que representa un nombre de usuario
     * @param 	EmailAddress 	$email 		VO email representa una direccion de correo electroncio
     * @param 	Password  	$password 	VO password representa un password
     * @param  integer        $rol           Rol del usuario dentro del dominio
     *
     */
	public function __construct($username=null,$email=null,$password=null,$rol=0){
		$this->username = $username;
		$this->email = $email;
		$this->password = $password;
          $this->rol = $rol;
	}

    /**
     * devuelve string que representa el nombre del usuario
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function getUsername()
     {
          return $this->username;
     }

    /**
     * devuelve string que representa el email
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function getEmail()
     {
          return $this->email->toString();
     }

    /**
     * devuelve string que representa el password
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function getPassword()
     {
          return $this->password->toString();
     }

    /**
     * devuelve string que representa el nombre del usuario
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function setUsername($username)
     {
          $this->username = $username;
          return $this;
     }

    /**
     * devuelve string que representa el email
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function setEmail($email)
     {
          return $this->email = $email;
          return $this;
     }

    /**
     * devuelve string que representa el password
     *
     * @author   Gabriel Camacho <kbo025@gmail.com>
     * @version  08/05/2015
     * @return   string
     */
     public function setPassword($password)
     {
          $this->password = $password;
          return $this;
     }

     public function getRol()
     {
          return $this->rol;
     }

     public function setRol($rol)
     {
          $this->rol = $rol;
          return $rol;
     }
}