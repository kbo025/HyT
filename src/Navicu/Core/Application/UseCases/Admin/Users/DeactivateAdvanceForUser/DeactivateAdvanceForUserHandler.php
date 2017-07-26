<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\DeactivateAdvanceForUser;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\DisableAdvance;

/**
 *  Caso de uso para desactivar la antelacion por pago por transferencia  
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 10-11-2016
 */
class DeactivateAdvanceForUserHandler implements Handler
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
        $rp = $rf->get('User');
        $by = $rp->findOneByArray(['id' => $command->get('deactivateBy')]);
        $to = $rp->findOneByArray(['id' => $command->get('id')]);

        if (!empty($by) && !empty($to)) {

            $to->setDisableAdvance($command->get('deactivate'));
            $save = [];
            if ( $to->getDisableAdvance() ) {
                $disableAdvance = new DisableAdvance();
                $disableAdvance
                    ->setDate(new \Datetime('now'))
                    ->setReason($command->get('reason'))
                    ->setDeactiveBy($by)
                    ->setUserId($to);
                $to->addAdvanceDeactivation($disableAdvance);
                $save[] = $disableAdvance;
            }
            $save[] = $to;
            $rp->save($save);
            return new ResponseCommandBus(201,'OK');
        }

        return new ResponseCommandBus(404,'Not Found');
    }
}