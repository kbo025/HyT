<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Admin\EditUserAdmin;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Adapter\CoreUser;

/**
 * EditUserHandler
 *
 * Editar usuario administrador (nvc_profile)
 *
 * @author Freddy Contreras
 */
class EditUserAdminHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $rpNvcProfile = $rf->get('NvcProfile');
        $rolerep = $rf->get('Role');
        $request = $command->getRequest();
        $userNvc = $rpNvcProfile->findOneBy(['user' => $request['user_id']]);
        if (!$userNvc)
            return new ResponseCommandBus(404,'No Found');

        $userNvc->setAtributes($request);
        $user = $userNvc->getUser();

        if ($user) {
            if (isset($request['username']))
                $user->setUsername($request['username']);
            if (isset($request['email'])) {
                $email = new EmailAddress($request['email']);
                $user->setEmail($email->toString());
            }
            if (isset($request['password']))
                CoreUser::updatePassword($user, $request['password']);

            $rolesData = $request['roles'];

            $newroles = array(); //Roles a ser asignados

            $roles = $user->getRole(); //Roles Actuales del usuario

            $currentroles = array(); //Ids de los roles actuales del usuario

            foreach ($roles as $role) {
                $currentroles[] = $role->getId();
            }

            foreach ($rolesData as $data) {
                $newroles[] = $data['id'];
            }

            //Roles a ser Removidos
            foreach ($currentroles as $role) {

                if (!in_array($role, $newroles)) {
                    $roleinstance = $rolerep->findById($role);
                    if($roleinstance->getName() != 'ROLE_ADMIN_FIREWALL')
                        $user->removeRole($roleinstance);
                }
            }

            $roles = $user->getRole(); //Roles Actuales del usuario

            $currentroles = array(); //Ids de los roles actuales del usuario

            foreach ($roles as $role) {
                $currentroles[] = $role->getId();
            }


            foreach ($newroles as $newrole) {
                if (!in_array($newrole, $currentroles)) {
                    $roleinstance = $rolerep->findById($newrole);
                    $user->addRole($roleinstance);
                }

            }
        }
        $rpNvcProfile->save($userNvc);

        return new ResponseCommandBus(201,'Ok');
    }
}