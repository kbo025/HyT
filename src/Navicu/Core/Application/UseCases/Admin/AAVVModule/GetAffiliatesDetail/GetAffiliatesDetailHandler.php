<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAffiliatesDetail;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Caso de uso para retornar el detalle de una agencia de viaje
 * dado un slug.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetAffiliatesDetailHandler implements Handler
{
    private $rf;

    /**
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso.
     *
     * @param SetDataReservationCommand $command
     * @param RepositoryFactory $rf
     * @return ResponseCommandBus Object
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {

        $request = $command->getRequest();

        $aavv = $rf->get('AAVV')->findOneByArray(["slug"=>$request["slug"]]);

        if(!$aavv)
            return new ResponseCommandBus(400, 'Ok', null);

        $Profiles = $aavv->getAavvProfile()->toArray();

        $resp["aavv"]["name"] = $aavv->getCommercialName();
        $resp["aavv"]["slug"] = $aavv->getSlug();
        $resp["aavv"]["statusAgency"] = $aavv->getStatusAgency();

        $resp["user"] = [];
        foreach ($Profiles as $Profile) {
            $aux["name"] = $Profile->getFullname();
            $aux["identityCard"] = $Profile->getDocumentId();
            $aux["phone"] = $Profile->getPhone();
            $aux["email"] = $Profile->getEmail();

            if ($Profile->getLocation()) {
                if ($Profile->getLocation()->getlvl() != 0) {
                    $aux["country"] = $Profile->getLocation()->getRoot()->getTitle();
                    $aux["state"] = $Profile->getLocation()->getParent()->getTitle();
                } else {
                    $aux["country"] = $Profile->getLocation()->getTitle();
                    $aux["state"] = null;
                }
            } else {
                $aux["country"] = null;
                $aux["state"] = null;
            }

            $aux["position"] = $Profile->getPosition();

            if($Profile->getUser()->getRoles()[0] == "AAVV_ADMIN")
                array_unshift($resp["user"], $aux);
            else
                array_push($resp["user"], $aux);

        }

        $financialTransactions = $aavv->getFinancialTransactions()->toArray();

//        Credito todal consumido a lo largo de toda su vida util en navicu.
//        $consumedCredit = 0;
//        foreach ($financialTransactions as $financialTransaction) {
//            if ($financialTransaction->getSign() == "+")
//                $consumedCredit += $financialTransaction->getAmount();
//        }

        $resp["finance"]["creditAvailable"] = $aavv->getCreditAvailable();
        $resp["finance"]["creditInitial"] = $aavv->getCreditInitial();
//        $resp["finance"]["consumedCredit"] = $consumedCredit;
        $resp["finance"]["consumedCredit"] = $aavv->getCreditInitial() - $aavv->getCreditAvailable();
        $resp["finance"]["averageVolume"] = null;
        $resp["finance"]["cumulativeVolume"] = null;

        $resp["finance"]["numberReservation"] = 0;

        foreach ($aavv->getAavvReservationGroup() as $reservationGroup) {
            $resp["finance"]["numberReservation"] += count($reservationGroup->getReservation());
        }

        $resp["additionalQuota"] = [];
        $additionalQuotas = $aavv->getAdditionalQuota()->toArray();

        foreach ($additionalQuotas as $additionalQuota) {

            $quota["description"] = CoreTranslator::getTransChoice(
                "aavv.additionalQuota.".$additionalQuota->getDescription(),
                $aavv->getAavvProfile()->count() > 1 ? 1 : 0
            );

            if ($additionalQuota->getDescription() == "licence")
                $quota["amount"] = $additionalQuota->getAmount() * $aavv->getAavvProfile()->count();
            else
                $quota["amount"] = $additionalQuota->getAmount();

            array_push($resp["additionalQuota"], $quota);
        }

        return new ResponseCommandBus(200,'Ok', $resp);
    }
}
