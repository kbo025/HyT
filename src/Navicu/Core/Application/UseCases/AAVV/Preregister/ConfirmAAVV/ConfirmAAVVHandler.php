<?php
namespace Navicu\Core\Application\UseCases\AAVV\Preregister\ConfirmAAVV;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Model\Entity\AAVV;

/**
 * Clase para ejecutar el caso de uso RegisterAAVV
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 19/08/2016
 */
class ConfirmAAVVHandler implements Handler
{
	/**
     *
	 *
     *  @param ConfirmAAVVCommand $command
     *  @param RepositoryFactory $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{

		try{
			$user_rep = $rf->get('User');
			//$role_rep = $rf->get('Role');
			$aavv_rep = $rf->get('AAVV');
            $modulerep = $rf->get('ModuleAccess');
			$request = $command->getRequest();

			$user = $user_rep->findByUserNameOrEmail($request['username']);

			if($user->getConfirmationToken() == $request['token'] && !$user->isEnabled()){

				$user->setEnabled(true);

				$aavv = new AAVV();

                $temporal_slug = 'aavv-' . substr(md5(microtime()),rand(0,26),5);

                $aavv->setSlug($temporal_slug);

				$adminrole = new Role('ADMIN');
                $adminrole->setAdmin(true);

                $aavvmodules = $modulerep->getAavvModules();

                foreach($aavvmodules as $module) {

                    $adminrole->addModule($module);

                    foreach($module->getPermissions() as $permission) {
                        if($permission->getName() != 'HIDE')
                            $adminrole->addPermission($permission);
                    }
                }

				$userrole = new role('USER');

				$aavv->addRole($adminrole);
				$aavv->addRole($userrole);
                $aavv->setRegistrationDate(new \DateTime('now'));

				//$role_rep->save($adminrole);
				//$role_rep->save($userrole);

				$profile = $user->getAavvProfile();

                $user->addRole($adminrole);

				$profile->setAavv($aavv);

				//$user_rep->save($profile);

				$aavv_rep->save($aavv);

				return new ResponseCommandBus(200,'Usuario confirmado',$user);
			}else {
				return new ResponseCommandBus(400,'enlace Invalido',null);
			}



		} catch(\Exception $e) {
			return new ResponseCommandBus(400,$e->getTraceAsString());
		}
	}
}
