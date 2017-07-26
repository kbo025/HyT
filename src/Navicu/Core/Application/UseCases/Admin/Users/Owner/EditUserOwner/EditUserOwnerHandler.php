<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\EditUserOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * EditUserHandler
 *
 * Editar usuario administrador (nvc_profile)
 *
 * @author Freddy Contreras
 */
class EditUserOwnerHandler implements Handler
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
        $rpOwnerProfile = $rf->get('OwnerProfile');
        $request = $command->getRequest();

        $userOwner = $rpOwnerProfile->findOneBy(['user' => $request['user_id']]);
        if (!$userOwner)
            return new ResponseCommandBus(404,'No Found');

        $userOwner->setAtributes($request);
        $user = $userOwner->getUser();

        if ($user)
            $user->setAtributes($request);

        $rpOwnerProfile->save($userOwner);

        return new ResponseCommandBus(201,'Ok');
    }
}