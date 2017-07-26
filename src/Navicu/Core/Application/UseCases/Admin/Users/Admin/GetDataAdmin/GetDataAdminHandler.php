<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Admin\GetDataAdmin;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * GetDataAdminHandler
 * El siguiente caso obtiene datos de un usuario administrador
 * dado un id del usuario de fos_user
 *
 * @author Freddy Contreras
 */
class GetDataAdminHandler implements Handler
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
        $userId = $command->get('user_id');
        $nvcProfile = $rf->get('NvcProfile');
        $roleRepository = $rf->get('Role');

        $roles = $roleRepository->getSimpleList();
        $response = array();

        $rolesArray = array();
        foreach($roles as $role) {

            if ($role['name'] != 'ROLE_ADMIN_FIREWALL') {
                if (is_null($role['aavv_id'])) {
                    $current = array();

                    $current['id']   = $role['id'];
                    $current['role'] = $role['userReadableName'];

                    $rolesArray[] = $current;
                }
            }
        }
        $user = $nvcProfile->findNvcProfileByUserId($userId);
        if ($user) {
            $user['roles'] = $roleRepository->getUserPermissions($userId);
            $user['birth_date'] = new \Datetime($user['birth_date']);
            $user['birth_date'] = $user['birth_date']->format('d/m/Y');
            $response['user']  = $user;
            $response['roles'] = $rolesArray;
            return new ResponseCommandBus(200, 'Ok', $user);
        } else
            return new ResponseCommandBus(404,'No Found');
    }
}