<?php

namespace Navicu\InfrastructureBundle\Tests\Model\ValueObject;

use  Navicu\Core\Domain\Model\ValueObject\Url;

/**
 *
 * Se define una clase encargada de realizar pruebas unitarias a la clase EmailAddress
 * 
 * @author Freddy Contreras <freddycontreras@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras@gmail.com>
 * @version 12/05/2015
 */

class UrlTest extends \PHPUnit_Framework_TestCase
{

	/**
     * Metodo que prueba crear un EmailAddress
     */
	public function testCreateUrl()
	{
		echo "------------------------------\n";
		echo "* Clase: Url\n";
		echo "* Prueba: Crear Url\n";
		try{
			$url = new Url('http://navicu.com');
			echo "* Resultado: Prueba Exitosa\n";
		}catch(\InvalidArgumentException $e){
			echo "* Resultado: Excepcion Argumento invalido\n";
		}
	}

    /**
     * Metodo que prueba el metodo toString de la clase EmailAddress
     */
	public function testGetUrlString()
	{
		echo "------------------------------\n";
		echo "* Clase: Url\n";
		echo "* Prueba: metodo toString\n";
		$url = new Url('http://navicu.com');
		$urlString = $url->toString();
		$this->assertInternalType('string', $urlString);
		echo "* Resultado: Prueba Exitosa\n";
	}
}