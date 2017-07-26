<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\CreateRole;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Clase para ejecutar el caso de uso CreateRole
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 16/09/2016
 */
class CreateRoleHandler implements Handler
{
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$rolerep = $rf->get('Role');

		$request = $command->getRequest();

		$user = CoreSession::getUser();

		$profile = $user->getAavvProfile();

        $aavv = $profile->getAavv();

        $roles = $aavv->getRoleNames();



        if(isset($request['name']) and !in_array($request['name'], $roles)) {

        	try {
	        	$role = new Role($request['name']);

	        	$aavv->addRole($role);

	        	$rolerep->save($aavv);

	        	return new ResponseCommandBus(201,'Created');
	        	
        	} catch(\Exception $e) {
			    return new ResponseCommandBus(400,$e->getMessage());
		    }



        } else {
        	return new ResponseCommandBus(400,'aavv.edit.roles.errors.name_already_in_use');
        }
	}
}