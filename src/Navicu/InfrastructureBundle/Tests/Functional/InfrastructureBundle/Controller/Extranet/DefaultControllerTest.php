<?php

namespace Navicu\InfrastructureBundle\Tests\InfrastructureBundle\Controller\Extranet;
 
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
*	DefaultControllerExtranetTest ejecuta las pruebas funcionales sobre los action del controlador de extranet
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*	@version 13/05/2015
*/
class DefaultControllerExtranetTest extends WebTestCase
{

	/**
	* Prueba de la funcion "Crear Usuario Temporal"
	*/
	public function testRegisterOwner()
	{
		echo "------------------------------\n";
		echo "* Prueba Funcional: Registrar Usuario Temporal\n";

        $client = static::createClient();
 		$crawler = $client->request('GET', '/extranet/register/registerOwner');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        echo "* Resultado: ruta correcta\n";

        $form = $crawler->selectButton('Save')->form();
		$form['form[username]'] = '1234';
		$form['form[email]'] = 'jjjjjjj@email.com';
		$form['form[password]'] = '1111111';
		$crawler = $client->submit($form);
		$this->assertRegExp(
            '/is not a valid password/',
            $client->getResponse()->getContent()
        );
        echo "* Resultado: Respuesta a datos invalidos correcta\n";
		$form['form[username]'] = 'testuser';
		$form['form[email]'] = 'testuser@email.com';
		$form['form[password]'] = 'TestUser-1234';
		$crawler = $client->submit($form);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		echo "* Resultado: Respuesta a datos validos correcta\n";
	}
}