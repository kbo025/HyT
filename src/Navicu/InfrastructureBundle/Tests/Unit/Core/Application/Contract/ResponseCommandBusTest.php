<?php

namespace Navicu\InfrastructureBundle\Tests\Application\Contract;

use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* 	ResponseCommandBusTest realiza todas las pruebas unitarias sobra la clase ResponseCommandBus
*	
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*	@version 06/05/2015
*/
class ResponseCommandBusTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prueba Crear un ResponseCommandBus
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testCreateUser()
	{
		echo "------------------------------\n";
		echo "* Clase: ResponseCommandBus\n";
		echo "* Prueba: Crear ResponseCommandBus\n";
		$rcb = new ResponseCommandBus(200,'prueba RCB');
		$this->assertInstanceOf('Navicu\Core\Application\Contract\ResponseCommandBus', $rcb);
		$rcb = new ResponseCommandBus(200,'prueba RCB',array(0=>'prueba 1',1=>'Prueba 2',2=>'prueba 3'));
		$this->assertInstanceOf('Navicu\Core\Application\Contract\ResponseCommandBus', $rcb);
		echo "* Resultado: Prueba Exitosa\n";
	}

    /**
     * Prueba el metodo getStatusCode()
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testGetStatusCode()
	{
		echo "------------------------------\n";
		echo "* Clase: ResponseCommandBus\n";
		echo "* Prueba: metodo getStatusCode()\n";
		$rcb = new ResponseCommandBus(200,'prueba RCB');
		$code = $rcb->getStatusCode();
		$this->assertInternalType('integer', $code);
		$this->assertEquals(200,$code);
		echo "* Resultado: Prueba Exitosa\n";
	}

    /**
     * Prueba el metodo getMessage()
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testGetMessage()
	{
		echo "------------------------------\n";
		echo "* Clase: ResponseCommandBus\n";
		echo "* Prueba: metodo getMessage()\n";
		$rcb = new ResponseCommandBus(200,'prueba RCB');
		$message = $rcb->getMessage();
		$this->assertInternalType('string', $message);
		$this->assertEquals('prueba RCB',$message);
		echo "* Resultado: Prueba Exitosa\n";
	}

    /**
     * Prueba el metodo getData()
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testGetData()
	{
		echo "------------------------------\n";
		echo "* Clase: ResponseCommandBus\n";
		echo "* Prueba: metodo getData()\n";
		$rcb = new ResponseCommandBus(200,'prueba RCB');
		$data = $rcb->getData();
		$this->assertNull($data);
		$rcb = new ResponseCommandBus(200,'prueba RCB',array(25=>'prueba 1',88=>'Prueba 2',19=>'prueba 3'));
		$data = $rcb->getData();
		$this->assertArrayHasKey(19,$data);
		echo "* Resultado: Prueba Exitosa\n";
	}

    /**
     * Prueba el metodo getArray()
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testGetArray()
	{
		echo "------------------------------\n";
		echo "* Clase: ResponseCommandBus\n";
		echo "* Prueba: metodo getArray()\n";
		$rcb = new ResponseCommandBus(200,'prueba RCB',array(25=>'prueba 1',88=>'Prueba 2',19=>'prueba 3'));
		$data = $rcb->getArray();
		$this->assertArrayHasKey('meta',$data);
		echo "* Resultado: Prueba Exitosa\n";
	}

}