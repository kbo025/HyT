<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 13/02/17
 * Time: 01:29 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\EditRolesAndPermissions;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class EditRolesAndPermissionsHandler implements Handler
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
        $data = $command->getRequest();

        $rolesRepo = $rf->get('Role');
        $permissionsRepo = $rf->get('Permission');

        $roleInstance = $rolesRepo->find($data['rolId']);
        $permissionInstance = $permissionsRepo->find($data['permissionId']);
        if($data['value'] == 'true') {
            $roleInstance->addPermission($permissionInstance);
        } else {
            $roleInstance->removePermission($permissionInstance);
        }

        try{
            $rolesRepo->save($roleInstance);

        } catch (\Exception $e) {
            return new ResponseCommandBus(422, 'unprocessable entity', $e->getMessage());
        }


        /*foreach ($data['roles'] as $currentRole) {
            $roleInstance = $rolesRepo->find($currentRole['rolId']);
            foreach ($currentRole['modules'] as $currentModule) {
                foreach ($currentModule['permissions'] as $currentPermission) {
                    $permissionInstance = $permissionsRepo->find($currentPermission['id']);
                    if($currentPermission['value'] == 'true') {
                        $roleInstance->addPermission($permissionInstance);
                    } else {
                        $roleInstance->removePermission($permissionInstance);
                    }


                    try{
                        $rolesRepo->save($roleInstance);

                    } catch (\Exception $e) {
                        return new ResponseCommandBus(422, 'unprocessable entity', $e->getMessage());
                    }
                }
            }
        }*/


        return new ResponseCommandBus(201, 'OK');
    }
}