<?php

namespace Navicu\InfrastructureBundle\Tests\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\User;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Metodo que prueba crear un User
     */
	public function testCreateUser()
	{
		echo "------------------------------\n";
		echo "* Clase: User\n";
		echo "* Prueba: Crear User\n";
		$email = new EmailAddress('kbo025@gmail.com');
		$password = new Password('hsgT73698#');
		$user = new User('kbo025',$email,$password);
		$this->assertInstanceOf('Navicu\Core\Domain\Model\Entity\User', $user);
		echo "* Resultado: Prueba Exitosa\n";
	}
}
