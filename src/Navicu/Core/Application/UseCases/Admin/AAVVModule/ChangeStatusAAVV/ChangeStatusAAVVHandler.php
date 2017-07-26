<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ChangeStatusAAVV;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVV;

/**
 *  Caso de uso para cambiar el estado a una agencia de viaje a activa / inactiva
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 10-11-2016
 */
class ChangeStatusAAVVHandler implements Handler
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
        $rp = $rf->get('AAVV');
        $aavv = $rp->findOneByArray(['slug' => $command->get('slug')]);

        if ($aavv) {

            if ($aavv->getStatusAgency() == AAVV::STATUS_ACTIVE || $aavv->getStatusAgency() == AAVV::STATUS_INACTIVE) {

                $arrayPersist = [];
                $aavv->setStatusAgency($command->get('status'));
                $profiles = $aavv->getAavvProfile();

                foreach ($profiles as $profile) {

                    $user = $profile->getUser();

                    if ($profile->getStatus()!=3) {
                        if ($command->get('status') == 1) {
                            $profile->setStatus(1);
                            $user->setEnabled(true);
                            $profile->setLastActivation(new \DateTime('now'));
                            $aavv->setDeactivateReason(0);
                        } else {
                            $user->setEnabled(false);
                            $profile->setStatus(2);
                            $aavv->setDeactivateReason(1);
                        }
                    }

                    $arrayPersist[] = $profile;
                    $arrayPersist[] = $user;
                }

                $arrayPersist[] = $aavv;
                if ($rp->save($arrayPersist))
                    return new ResponseCommandBus(201, 'ok');

                return new ResponseCommandBus(400,'Bad Request',['message'=>'impossible_to_save']);
            }

            return new ResponseCommandBus(400,'Bad Request',['message'=>'unregistered_user']);
        }

        return new ResponseCommandBus(404,'Not Found');
    }
}