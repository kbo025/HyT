<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\EditRolePermissions;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;

use Navicu\Core\Domain\Adapter\CoreTranslator;

class EditRolePermissionsHandler implements Handler
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
        $translator = new CoreTranslator();

        $id = $command->get('id');

        $permissions = $command->get('permissions');

        $rolerep = $rf->get('Role');

        $modulerep = $rf->get('ModuleAccess');

        $permrep = $rf->get('Permission');

        $role = $rolerep->findById($id);


        if ($role) {

            try {

                    foreach($permissions as $module => $newpermissions) {

                        $module = $translator->getTranslator($module, 'modules');
                        //Es la primera vez que se otorga accesso a dicho modulo al rol
                        if(!($role->hasModuleAccess($module))) {

                            
                            $modinstance = $modulerep->findByName($module);

                            
                            $role->addModule($modinstance);

                            foreach($newpermissions as $permission) {

                                $perminstance = $permrep->findByNameAndModule($permission, $module);
                                $role->addPermission($perminstance);
                                $rolerep->save($role);
                            }
                        } else {

                            $oldperms = $role->getModulePerms($module);

                            //Primero los permisos a eliminar
                            if(!empty($oldperms)) {
                                foreach($oldperms as $perm) {
                                    if(!in_array($perm, $newpermissions)){
                                        $perminstance = $permrep->findByNameAndModule($perm,$module);
                                        $role->removePermission($perminstance);
                                        $rolerep->save($role);
                                    }
                                }
                            }

                            $currentroleperms = $role->getModulePerms($module);

                            if(empty($currentroleperms)){
                                foreach($newpermissions as $permission) {
                                    $perminstance = $permrep->findByNameAndModule($permission,$module);
                                    $role->addPermission($perminstance);
                                    $rolerep->save($role);
                                }
                            } else {
                                foreach($newpermissions as $permission) {

                                    if(!in_array($permission, $currentroleperms)) {

                                        $perminstance = $permrep->findByNameAndModule($permission,$module);
                                        $role->addPermission($perminstance);
                                        $rolerep->save($role);

                                    }

                                }
                            }

                        }

                        

                    }


                    
                
            } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage());
        }
            
            return new ResponseCommandBus(201,'ok');
        } else {
            return new ResponseCommandBus(404,'El rol no existe');
        }
    }
}