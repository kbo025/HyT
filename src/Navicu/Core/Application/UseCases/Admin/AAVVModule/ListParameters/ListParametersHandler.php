<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\ListParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class ListParametersHandler implements Handler
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
        $data = array();

        $quota_rep = $rf->get('AAVVAdditionalQuota');
        $credit_rep = $rf->get('AAVVCreditNVC');
        $globals_rep = $rf->get('AAVVGlobal');
        $staging_quota_rep = $rf->get('AAVVStagingAdditionalQouta');

        $typeCredit = $globals_rep->getParameter('depositVariationType');

        $nextquotas = $staging_quota_rep->findCurrentlyStaged();

        if (empty($nextquotas)) {
            $data['nextQuotaMaintenance'] = null;
            $data['nextQuotaLicence'] = null;
            $data['nextQuotaEmail'] = null;
            $data['nextDate'] = null;
        } else {
            $data['nextDate'] = $nextquotas[0]->getValidsince()->format('d-m-Y');
            foreach($nextquotas as $currentQuota) {
                $quotaDescription = $currentQuota->getTargetid()->getDescription();

                switch ($quotaDescription) {
                    case 'maintenance':
                        $data['nextQuotaMaintenance'] = $currentQuota->getNewamount();
                        break;
                    case 'licence':
                        $data['nextQuotaLicence'] = $currentQuota->getNewamount();
                        break;
                    case 'email':
                        $data['nextQuotaEmail'] = $currentQuota->getNewamount();
                        break;
                }
            }
        }

        $data['typeCredit'] = $typeCredit;

        $emailquota = $quota_rep->findOneByArray(['description' => 'email']);
        $data['quotaEmail'] = $emailquota->getAmount();
        $maintenancequota = $quota_rep->findOneByArray(['description' => 'maintenance']);
        $data['quotaMaintenance'] = $maintenancequota->getAmount();
        $licencequota = $quota_rep->findOneByArray(['description' => 'licence']);
        $data['quotaLicence'] = $licencequota->getAmount();



        $credits = $credit_rep->findAll();

        $creditsArray = array();
        foreach ($credits as $credit) {
            $currentCredit = array();
            $currentCredit['amount'] = $credit->getCredit();
            $currentCredit['credit_awarded'] = $credit->getAmountRate();
            $currentCredit['delete'] = $credit->getDelete();
            $currentCredit['id'] = $credit->getId();
            $creditsArray[] = $currentCredit;
        }

        $data['depositTypes'] = $creditsArray;

        //die(var_dump($data));


        return new ResponseCommandBus(200,'OK', $data);
    }
}
