<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step2\SetInfoStep2;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\AAVVBankPayments;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\Core\Application\Services\AAVVAccountingService;

/**
 * Comando para Guardar la información necesaria para el manejo del paso
 * 2 de registro de AAVV.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SetInfoStep2Handler implements Handler
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


        $request = $command->getRequest();
        $user = CoreSession::getUser();
        $profile = null;
        if ($user instanceof User)
            $profile = $user->getAavvProfile();

        if(is_null($profile)) {
            $aavv_rep = $rf->get('AAVV');
            $slug = CoreSession::get('sessionAavv');
            if(!is_null($slug))
                $aavv = $aavv_rep->findOneByArray(['slug' => $slug]);
            else
                return new ResponseCommandBus(400, 'OK','Destination not found');
        } else {
            $aavv = $profile->getAavv();

        }


        $bank = $request["bankId"] ? $rf->get("BankType")->find($request["bankId"]) : null;
        $amountCredit = $request["creditId"] ? $rf->get("AAVVCreditNVC")->find($request["creditId"]) : null;
        $variationType = $rf->get("AAVVGlobal")->getParameter('depositVariationType');
        
        

        if($aavv->getStatusAgency() == AAVV::STATUS_ACTIVE || $aavv->getStatusAgency() == AAVV::STATUS_INACTIVE ) {    
            
            $initialCreditAAavv = $aavv->getCreditInitial();
            $amount = ($variationType == 1) ?
                $amountCredit->getCredit() * $amountCredit->getAmountRate() :
                $amountCredit->getAmountRate(); 

            if ($initialCreditAAavv != $amount) {
                $rest = $amount - $initialCreditAAavv;
                //$aavv->setCreditInitial($amount);
                new AAVVAccountingService($rf);
                AAVVAccountingService::setMovement(
                    ['description' => 'update_initial_credit', 'sign' => ($rest > 0) ? '-' : '+', 'amount' => abs($rest)],
                    $aavv
                );
                AAVVAccountingService::balanceCalculator($aavv);
            }
        } else {
            if ($amountCredit) {
                if ($variationType == 1)
                    $request["amount"] = $amountCredit->getCredit() * ($amountCredit->getAmountRate());
                else
                    $request["amount"] = $amountCredit->getAmountRate();

                $request['amount'] = round($request['amount'], 0, PHP_ROUND_HALF_DOWN);
                $aavv->setCreditInitial($request["amount"]);
                $aavv->setNavicuGain($amountCredit->getCredit() - $request['amount']);
            } else {
                $aavv->setCreditInitial(null);
            }
        }


        $bankPayment = empty($aavv->getBankDeposit()->toArray()) ?  new AAVVBankPayments() : $aavv->getBankDeposit()->toArray()[0];
        $bankPayment->updateObject($request);

        $bankPayment->setBankType($bank);
        $aavv->addBankDeposit($bankPayment);
        $bankPayment->setAavv($aavv);

        $aavv->setHaveCreditZero(
            $aavv->getCreditAvailable() <= 0
        );

        $rf->get("AAVV")->save($aavv);
        return new ResponseCommandBus(200, 'Ok', true);
    }
}
