<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ApplyParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class ApplyParametersHandler implements Handler
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
        $staging_quota_rep = $rf->get('AAVVStagingAdditionalQouta');
        $quota_rep = $rf->get('AAVVAdditionalQuota');

        $quotas = $staging_quota_rep->findQuotasToApply();


        foreach($quotas as $quota){
            $quotaInstance = $quota_rep->find($quota->getTargetid());
            $quotaInstance->setAmount($quota->getNewamount());
            $quota_rep->save($quotaInstance);

            $quota->setApplied(true);
            $staging_quota_rep->save($quota);
        }

        return new ResponseCommandBus(201,'OK');
    }
}