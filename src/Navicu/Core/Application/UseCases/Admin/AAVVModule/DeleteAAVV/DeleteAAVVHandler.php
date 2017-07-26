<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\DeleteAAVV;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVV;

class DeleteAAVVHandler implements Handler
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
            if ($aavv->getStatusAgency() == AAVV::STATUS_REGISTERED || $aavv->getStatusAgency() == AAVV::STATUS_REGISTRATION_PROCESS) {
                if ($rp->delete($aavv))
                    return new ResponseCommandBus(201, 'ok');
            }

            return new ResponseCommandBus(400,'Bad Request');
        }

        return new ResponseCommandBus(404,'Not Found');
    }
}