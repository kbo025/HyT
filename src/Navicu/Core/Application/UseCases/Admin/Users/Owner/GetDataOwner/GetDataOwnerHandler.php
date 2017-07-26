<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\GetDataOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * GetDataOwnerHandler
 *
 * El siguiente caso obtiene datos de un usuario administrador
 * dado un id del usuario de fos_user
 *
 * @author Freddy Contreras
 */
class GetDataOwnerHandler implements Handler
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
        $ownerProfile = $rf->get('OwnerProfile');

        $user = $ownerProfile->findOwnerByUserId($userId);
        if ($user)
            return new ResponseCommandBus(200,'Ok',$user);
        else
            return new ResponseCommandBus(404,'No Found');
    }
}