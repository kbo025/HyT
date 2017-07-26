<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\StatusChangeUsers;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * Conjunto de objetos para eliminar los valores ingresados del usuario por perteneciente al caso de uso deleteusers
 * @author	Mary sanchez
 * @version 15-06-2016
 */
class StatusChangeUsersHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {

        $this->rf = $rf;
        $rpUser = $this->rf->get('User');

        //Se verifica los datos del usuario
        $user = $rpUser->findOneBy(array('id' => $command->getId()));
         if($user->isEnabled()) {

             $user->setEnabled(false);

         }else if(!$user->isEnabled()){

            $user->setEnabled(true);

         }
        
          $rpUser->save($user);

          return new ResponseCommandBus(201,'OK');


    }


}