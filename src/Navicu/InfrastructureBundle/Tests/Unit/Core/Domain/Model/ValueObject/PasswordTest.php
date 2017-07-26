<?php

namespace Navicu\InfrastructureBundle\Tests\Model\ValueObject;

use  Navicu\Core\Domain\Model\ValueObject\Password;

/**
 *
 * Se define una clase encargada de aplicar pruebas unitarias a la clase Password
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * 
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Metodo que prueba crear un password
     */
	public function testCreatePassword()
	{
		echo "------------------------------\n";
		echo "* Clase: Password\n";
		echo "* Prueba: Crear Password\n";
		try{
			$password = new Password('9524jSuf%');
			echo "* Resultado: Prueba Exitosa\n";
		}catch(\Exception $e){
			echo "* Resultado: Excepcion Argumento invalido\n";
		}
	}

    /**
     * Metodo que prueba el metodo toString() de la clase Password
     */
	public function testGetPasswordString()
	{
		echo "------------------------------\n";
		echo "* Clase: Password\n";
		echo "* Prueba: metodo toString()\n";
		$password = new Password('9524jSuf%');
		$string = $password->toString();
		$this->assertInternalType('string',$string);
		echo "* Resultado: Prueba Exitosa\n";
	}
}