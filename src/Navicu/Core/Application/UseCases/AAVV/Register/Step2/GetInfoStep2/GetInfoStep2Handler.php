<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step2\GetInfoStep2;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * Comando para devolver la información necesaria para el manejo del paso
 * 2 de registro de AAVV.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetInfoStep2Handler implements Handler
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
        $user = CoreSession::getUser();
        $profile = null;
        if($user instanceof User)
            $profile = $user->getAavvProfile();
        if(is_null($profile)) {
            $aavv_rep = $rf->get('AAVV');
            $slug = CoreSession::get('sessionAavv');
            $aavv = $aavv_rep->findOneByArray(['slug' => $slug]);
            //return new ResponseCommandBus(400, 'OK',"Destiny No Found");
        } else {
            $aavv = $profile->getAAVV();
        }

        $bankRepository = $rf->get("BankType");
        $response["bank"]= $bankRepository->getListBanksArray();

        $creditRepository = $rf->get("AAVVCreditNVC")->findAll();
        $variationType = $rf->get("AAVVGlobal")->getParameter('depositVariationType');

        $response["credit"] = [];
        $response["aavv"]["creditId"] = null;
        foreach ($creditRepository as $credit) {

            if ($variationType == 1)
                $amount = $credit->getCredit() * ($credit->getAmountRate());
            else
                $amount = $credit->getAmountRate();

            if ($amount == $aavv->getCreditInitial())
                $response["aavv"]["creditId"] = (string)$credit->getId();

            $aux["id"] = $credit->getId();
            $aux["credit"] = $credit->getCredit();
            $aux["creditNVC"] = $credit->getCredit() - $amount;
            $aux["amountRate"] = $amount;
            $aux["variationType"] = $variationType;
            $aux["variationType"] = $variationType;

            array_push($response["credit"],$aux);
        }

        $bankDposit = $aavv->getBankDeposit()->toArray();
        if ($bankDposit) {
            $response["aavv"]["bankId"] = $bankDposit[0]->getBankType() ? $bankDposit[0]->getBankType()->getId() : null;
            $response["aavv"]["paymentType"] = (string)$bankDposit[0]->getType();
            $response["aavv"]["number"] = $bankDposit[0]->getNumberReference();
        } else {
            $response["aavv"]["bankId"] = null;
            $response["aavv"]["paymentType"] = null;
            $response["aavv"]["number"] = null;
        }

        $response["validation"] = empty(ValidateRegistrationHandler::getValidations($aavv, $rf, 2)->getData()) ? false : true;

        return new ResponseCommandBus(200, 'Ok', $response);
    }
}
