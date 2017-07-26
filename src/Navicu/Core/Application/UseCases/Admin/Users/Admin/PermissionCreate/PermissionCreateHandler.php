<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\Admin\PermissionCreate;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Adapter\CoreParseYaml;

/**
 * Metodo usado para listar los usuarios del sistema
 * dado un conjunto de parametros de busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PermissionCreateHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     * @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();
        $rolesRepo = $rf->get('Role');
        $modulesRepo = $rf->get('ModuleAccess');

        $role = $rolesRepo->findByName('ROLE_'.strtoupper($request['roleName']));
        if (is_object($role))
            return new ResponseCommandBus(400,gettype($role));

        $role = new Role($request['roleName'], false, true);
        $rolesRepo->save($role);

        $mainObject = array();
        $mainObject['name'] = $request['roleName'];
        $mainObject['id'] = $role->getId();

        $mainModulesList = $modulesRepo->getMainModules();

        foreach ($mainModulesList as $currentModule) {
            $current = array();

            $current['name'] = $currentModule['name'];
            $current['subModules'] = $modulesRepo->getChildModules($currentModule['id'], 'es_VE');

            $modulesStructure[] = $current;
        }

        foreach ($modulesStructure as $currentMainModule) {
            if ($currentMainModule['name'] != 'aavv') {
                $mainObject['modules'][$currentMainModule['name']] = array();
                foreach ($currentMainModule['subModules'] as $currentModule) {
                    $Object = array();
                    $Object['id'] = $currentModule['id'];
                    $Object['name'] = $currentModule['name'];


                    $modulePermissions = $modulesRepo->getModulePermissions($currentModule['id'], 'es_VE');

                    $Object['permissions'] = $modulePermissions;

                    $mainObject['modules'][$currentMainModule['name']][] = $Object;
                }
            }
        }
        return new ResponseCommandBus(201,'Ok', $mainObject);

    }


}