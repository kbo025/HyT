<?php
namespace Navicu\Core\Application\UseCases\Ascribere\CreateTempOwner;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Clase para ejecutar el caso de uso createTempOwner
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 06/05/2015
 */
class CreateTempOwnerHandler implements Handler
{
	/**
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso Crear Usuario Temporal.
	 *
     *  @param CreateTempOwnerCommand $command
     *  @param RepositoryFactory $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$temp_owner = $rf->get('TempOwner');
		$request = $command->getRequest();

		if (strpos($request['username'], " "))
			return new ResponseCommandBus(400, 'share.message.username_error_not_valid',['message'=>'share.message.username_error_not_valid']);

		if ($temp_owner->usernameExist($request['username']))
			return new ResponseCommandBus(400, 'share.message.username_error_used',['message'=>'share.message.username_error_used']);
						
		if ($temp_owner->emailExist($request['email']))
			return new ResponseCommandBus(400, 'share.message.email_error_used',['message'=>'share.message.email_error_used']);

		$email = new EmailAddress($request['email']);
		$password = new Password($request['password']);
		$user = new TempOwner($request['username'], $email, $password);
		$temp_owner->registerUser($user);
		
		return new ResponseCommandBus(200,'OK',array_merge($command->getRequest(), ['slug'=>$user->getSlug()]));	
	}
}