<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\EditParameters;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVCreditNVC;
use Navicu\Core\Domain\Model\Entity\AAVVStagingAdditionalQouta;

class EditParametersHandler implements Handler
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
        $data = $command->getRequest();

        $quota_rep = $rf->get('AAVVAdditionalQuota');
        $credit_rep = $rf->get('AAVVCreditNVC');
        $globals_rep = $rf->get('AAVVGlobal');
        $staging_quota_rep = $rf->get('AAVVStagingAdditionalQouta');

        $quotaMaintenance = $data['nextQuotaMaintenance'];
        $quotaLicence = $data['nextQuotaLicence'];
        $quotaEmail = $data['nextQuotaEmail'];
        $validFrom = $data['nextDate'];

        if (!empty($validFrom)) {
            
            $persistArray = [];
            $maintenancequota = $quota_rep->findOneByArray(['description' => 'maintenance']);
            $licencequota = $quota_rep->findOneByArray(['description' => 'licence']);
            $emailquota = $quota_rep->findOneByArray(['description' => 'email']);

            $staging_quota_rep->deleteCurrentlyStaged($maintenancequota->getId());
            if (!empty($quotaMaintenance)) {
                $staging_quota = $this->createStagingQuota($maintenancequota, $quotaMaintenance,$validFrom);
                $persistArray[] = $staging_quota;
                $staging_quota_rep->save($staging_quota);
            }
            
            $staging_quota_rep->deleteCurrentlyStaged($licencequota->getId());
            if (!empty($quotaLicence) && ($quotaLicence != $licencequota->getAmount())) {
                $staging_quota = $this->createStagingQuota($licencequota, $quotaLicence,$validFrom);
                $staging_quota_rep->save($staging_quota);
            }
            
            $staging_quota_rep->deleteCurrentlyStaged($emailquota->getId());
            if (!empty($quotaEmail) && ($quotaEmail != $emailquota->getAmount())) {
                $staging_quota = $this->createStagingQuota($emailquota, $quotaEmail,$validFrom);
                $staging_quota_rep->save($staging_quota);
            }
        } else {
            $staging_quota_rep->deleteCurrentlyStaged();
        }

        $typeCredit = $data['typeCredit'];
        $depositTypes = $data['depositTypes'];
        foreach ($depositTypes as $deposit) {

            if (!empty($deposit['add']))
                $creditInstance = new AAVVCreditNVC();
            else
                $creditInstance = $credit_rep->find($deposit['id']);

            $creditInstance->setCredit($deposit['amount']);
            $creditInstance->setAmountRate($deposit['credit_awarded']);
            $creditInstance->setDelete($deposit['delete']);

            $credit_rep->save($creditInstance);
        }

        $globalDepositVariation = $globals_rep->findOneByArray(['name' => 'depositVariationType']);
        $globalDepositVariation->setValue($typeCredit);
        $globals_rep->save($globalDepositVariation);

        //Retorno de los datos

        $response = array();

        $quota_rep = $rf->get('AAVVAdditionalQuota');
        $credit_rep = $rf->get('AAVVCreditNVC');
        $globals_rep = $rf->get('AAVVGlobal');
        $staging_quota_rep = $rf->get('AAVVStagingAdditionalQouta');

        $typeCredit = $globals_rep->getParameter('depositVariationType');

        $nextquotas = $staging_quota_rep->findCurrentlyStaged();


        if (empty($nextquotas)) {
            $response['nextQuotaMaintenance'] = null;
            $response['nextQuotaLicence'] = null;
            $response['nextQuotaEmail'] = null;
            $response['nextDate'] = null;
        } else {
            $response['nextDate'] = $nextquotas[0]->getValidsince()->format('d-m-Y');
            foreach($nextquotas as $currentQuota) {
                $quotaDescription = $currentQuota->getTargetid()->getDescription();

                switch ($quotaDescription) {
                    case 'maintenance':
                        $response['nextQuotaMaintenance'] = $currentQuota->getNewamount();
                        break;
                    case 'licence':
                        $response['nextQuotaLicence'] = $currentQuota->getNewamount();
                        break;
                    case 'email':
                        $response['nextQuotaEmail'] = $currentQuota->getNewamount();
                        break;
                }
            }
        }

        $response['typeCredit'] = $typeCredit;

        $emailquota = $quota_rep->findOneByArray(['description' => 'email']);
        $response['quotaEmail'] = $emailquota->getAmount();
        $maintenancequota = $quota_rep->findOneByArray(['description' => 'maintenance']);
        $response['quotaMaintenance'] = $maintenancequota->getAmount();
        $licencequota = $quota_rep->findOneByArray(['description' => 'licence']);
        $response['quotaLicence'] = $licencequota->getAmount();

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

        $response['depositTypes'] = $creditsArray;

        return new ResponseCommandBus(201,'OK',$response);
    }



    public function createStagingQuota($quota,$newvalue, $date)
    {
        $staging_quota = new AAVVStagingAdditionalQouta();
        $staging_quota->setOldamount($quota->getAmount());
        $staging_quota->setNewamount($newvalue);
        $staging_quota->setValidSince(new \DateTime($date));
        $staging_quota->setTargetid($quota);
        $staging_quota->setApplied(false);

        return $staging_quota;

    }
}
