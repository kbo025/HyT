<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 13/02/17
 * Time: 09:16 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\GetRolesAndPermissions;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;

class GetRolesAndPermissionsHandler implements Handler
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
        $rolesRepo = $rf->get('Role');
        $modulesRepo = $rf->get('ModuleAccess');

        $response = array();
        $modulesStructure = array();

        $rolesList = $rolesRepo->getSimpleList();
        $mainModulesList = $modulesRepo->getMainModules();

        foreach ($mainModulesList as $currentModule) {
            $current = array();

            $current['name'] = $currentModule['name'];
            $current['subModules'] = $modulesRepo->getChildModules($currentModule['id'], 'es_VE');

            $modulesStructure[] = $current;
        }

        foreach ($rolesList as $currentRole) {
            if ($currentRole['name'] != 'ROLE_ADMIN_FIREWALL') {
                if (is_null($currentRole['aavv_id'])) {
                    $mainObject       = array();
                    $mainObject['id'] = $currentRole['id'];

                    $mainObject['name'] = $currentRole['userReadableName'];


                    $mainObject['modules'] = array();
                    foreach ($modulesStructure as $currentMainModule) {
                        if ($currentMainModule['name'] != 'aavv') {
                            $mainObject['modules'][$currentMainModule['name']] = array();
                            foreach ($currentMainModule['subModules'] as $currentModule) {
                                $Object         = array();
                                $Object['id']   = $currentModule['id'];
                                $Object['name'] = $currentModule['name'];

                                $grantedPermissions = $rolesRepo->getPermissionsInModule($currentRole['id'],
                                    $currentModule['id'], 'es_VE');

                                $modulePermissions = $modulesRepo->getModulePermissions($currentModule['id'], 'es_VE');

                                $this->mergePermissions($grantedPermissions, $modulePermissions);

                                usort($grantedPermissions, function ($a, $b) {
                                    return $a['name'] > $b['name'];
                                });

                                $Object['permissions'] = $grantedPermissions;

                                $mainObject['modules'][$currentMainModule['name']][] = $Object;
                            }
                        }
                    }
                    $response[] = $mainObject;
                }
            }
        }

        return new ResponseCommandBus(200, 'ok', $response);
    }

    private function mergePermissions(&$grantedPermissions, $modulePermissions)
    {
        foreach ($modulePermissions as $currenPermission) {
            if (!in_array($currenPermission['id'], array_column($grantedPermissions, 'id')))
                array_push($grantedPermissions, $currenPermission);
        }
    }
}