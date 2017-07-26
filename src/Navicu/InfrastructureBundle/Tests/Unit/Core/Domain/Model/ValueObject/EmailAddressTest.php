<?php

namespace Navicu\InfrastructureBundle\Tests\Model\ValueObject;

use  Navicu\Core\Domain\Model\ValueObject\EmailAddress;

/**
 *
 * Se define una clase encargada de realizar pruebas unitarias a la clase EmailAddress
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho 
 * 
 */
class EmailAddressTest extends \PHPUnit_Framework_TestCase
{

	/**
     * Metodo que prueba crear un EmailAddress
     */
	public function testCreateEmailAdress()
	{
		echo "------------------------------\n";
		echo "* Clase: EmailAdress\n";
		echo "* Prueba: Crear EmailAdress\n";
		try{
			$email = new EmailAddress('Example@email.com');
			echo "* Resultado: Prueba Exitosa\n";
		}catch(\InvalidArgumentException $e){
			echo "* Resultado: Excepcion Argumento invalido\n";
		}
	}

    /**
     * Metodo que prueba el metodo toString de la clase EmailAddress
     */
	public function testGetEmailString()
	{
		echo "------------------------------\n";
		echo "* Clase: EmailAdress\n";
		echo "* Prueba: metodo toString\n";
		$email = new EmailAddress('Example@email.com');
		$emailstring = $email->toString();
		$this->assertInternalType('string', $emailstring);
		echo "* Resultado: Prueba Exitosa\n";
	}

}