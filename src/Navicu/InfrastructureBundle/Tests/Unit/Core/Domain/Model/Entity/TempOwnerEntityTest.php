<?php

namespace Navicu\InfrastructureBundle\Tests\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;

/**
 *
 * Se define una clase encargada de aplicar pruebas unitarias a la clase TempOwner
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * 
 */
class TempOwnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Metodo que prueba crear un TempOwner
     */
	public function testCreateUser() {
		echo "------------------------------\n";
		echo "* Clase: TempOwner\n";
		echo "* Prueba: Crear TempOwner\n";
		$email = new EmailAddress('kbo025@gmail.com');
		$password = new Password('hsgT73698#');
		$user = new TempOwner('kbo025',$email,$password);
		$this->assertInstanceOf('Navicu\Core\Domain\Model\Entity\TempOwner', $user);
		echo "* Resultado: Prueba Exitosa\n";
	}
}
