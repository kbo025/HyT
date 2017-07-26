<?php
namespace Navicu\Core\Application\UseCases\Admin\DeleteTempOwner;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
* Clase para ejecutar el caso de uso DeleteTempOwner
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 22/12/2015
     */

class DeleteTempOwnerHandler implements Handler
{
    private $command;

    private $rf;

    /**
     *
     * @param Command $command Objeto Command contenedor
     *                                             de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     *
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->command = $command;
        $this->rf = $rf;

        $rpTempOwner  = $this->rf->get('TempOwner');

        $rpUser  = $this->rf->get('User');

        //Se verifica los datos del usuario

        $tempOwner = $rpTempOwner->findOneByArray(
            ['id' => $this->command->get('id')]
        );

        //Si existe el establecimiento temporal
		if ($tempOwner) {
            $tempOwner->setUserId(null);
            $user = $tempOwner->getUserId();
            $rpTempOwner->save($tempOwner);
            if (is_null($user)) {
                $user = $rpUser->findOneByArray(['username' => $tempOwner->getSlug()]);
            }
            if (!is_null($user)) {
                $user->setTempOwner(null);
                $user->deleteRole(3);
                $rpUser->save($user);
            }
            $rpTempOwner->delete($tempOwner);

            return new ResponseCommandBus(201,'OK');

		}
		return new ResponseCommandBus(404,'Not Found');

    }
}