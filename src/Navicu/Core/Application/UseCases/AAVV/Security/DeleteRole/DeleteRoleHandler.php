<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\DeleteRole;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;

class DeleteRoleHandler implements Handler
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
        $id = $command->get('id');

        $rolerep = $rf->get('Role');

        $role = $rolerep->findById($id);

        if ($role) {

            try {

                $rolerep->delete($role);
                
            } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
        }
            
            return new ResponseCommandBus(201,'ok');
        } else {
            return new ResponseCommandBus(404,'El rol no existe');
        }
    }
}