<?php


namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ParametersLog;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;


class ParametersLogHandler implements Handler
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

        $quotas = $staging_quota_rep->findApplied();

        $response = [];

        foreach ($quotas as $quota) {
            $currentQuota = [];
            $quotaInstance = $quota_rep->find($quota->getTargetid());
            $currentQuota['name'] = $quotaInstance->getDescription();
            $currentQuota['oldvalue'] = $quota->getOldamount();
            $currentQuota['newValue'] = $quota->getNewamount();
            $currentQuota['applied_at'] = $quota->getValidSince()->format('d-m-Y');

            $response[] = $currentQuota;
        }
        die(var_dump($response));
    }
}