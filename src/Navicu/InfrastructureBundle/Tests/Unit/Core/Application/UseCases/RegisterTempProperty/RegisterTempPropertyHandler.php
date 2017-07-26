<?php

namespace Navicu\InfrastructureBundle\Tests\Core\Application\UseCases\RegisterTempProperty;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyHandler;
use Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\Entity\Location;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Application\Contract\ResponseCommandBus;


/**
* pruebas para el manejador de comando register temppropertycommandtest
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 28/05/2015
*/
class RegisterTempPropertyHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function testHandleRegisterTempProperty()
	{
	
	}
}