<?php

namespace Navicu\InfrastructureBundle\Tests\Application\Services;

use Navicu\Core\Application\Services\CommandBus;
use Navicu\Core\Application\UseCases\PruebaError\PruebaErrorCommand;
use Navicu\Core\Application\UseCases\Prueba\PruebaHandler;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Resources\Services\RepositoryFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Configuration;

/**
* 	CommandBusTest realiza todas las pruebas unitarias sobra la clase CommandBus
*	
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*	@version 06/05/2015
*/
class CommandBusTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Prueba Buscar Comando que no existe
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @version 06/05/2015
     */
	public function testExecuteCommandNotExist()
	{
		echo "------------------------------\n";
		echo "* Clase: CommandBus\n";
		echo "* Prueba: Ejecutar comando que no existe\n";
		$command = new PruebaErrorCommand('Prueba de command Bus');
		$cb = new CommandBus(new RepositoryFactory());
		$response = $cb->execute($command);
		$this->assertInstanceOf('Navicu\Core\Application\Contract\ResponseCommandBus', $response);
		$this->assertEquals(404, $response->getStatusCode());
		echo "* Resultado: Prueba Exitosa\n";
	}
}