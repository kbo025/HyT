<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 25/10/16
 * Time: 11:03 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Deactivate;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeactivateHandler implements Handler
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
        $AavvRf = $rf->get('AAVV');
        $aavvs = $AavvRf->findAllObjects();

        $response = $this->findDeactivateAavv($rf, $aavvs);
        return $response;
    }

    /**
     * Funcion para desactivar una agencia de viaje si no encuentra un monto igual al costo
     * de la sumatoria de las quotas
     *
     * @param $rf
     * @param $aavvs
     * @return ResponseCommandBus
     * @author Isabel Nieto
     */
    public function findDeactivateAavv($rf, $aavvs)
    {
        $jj = 0;
        $emailCost = $maintenanceCost = $userCost = 0;
        $bankPaymentRf = $rf->get('AAVVBankPayments');
        $additionalQuotaRf = $rf->get('AAVVAdditionalQuota');

        $lastDate = new \DateTime("now 00:00:00");
        $initDate = $this->buildInitDate($lastDate);
        
        $quotas = $additionalQuotaRf->findAllObjects();
        $this->existingQuotas($quotas, $emailCost, $maintenanceCost, $userCost);

        $lengthAavvs = count($aavvs);
        // Buscamos el listado de todas las aavv
        for ($ii = 0; $ii < $lengthAavvs; $ii++) {
            if ($aavvs[$ii]->getActive()) {
                $personalizedMail = $this->hasPersonalizedMail($aavvs[$ii]);

                $lengthAavvUsers = count($aavvs[$ii]->getAavvProfile());
                $totalAmount = (($lengthAavvUsers * $userCost) + $maintenanceCost);
                if ($personalizedMail)
                    $totalAmount = $totalAmount + $emailCost;

                $transactions = $bankPaymentRf->findMovementsByAavv($aavvs[$ii]->getId(), $initDate, $lastDate);
                $found = false;
                foreach ($transactions as $payment) {
                    if ($payment->getAmount() == $totalAmount)
                        $found = true;
                }
                // Si no encontramos algun pago con el monto total entonces la aavv la desactivamos
                if (!$found) {
                    $aavvs[$ii]->setActive($found);
                    $jj++;
                }
            }
        }
        return new ResponseCommandBus(200, 'ok', $jj);
    }

    /**
     * Funcion encargada de buscar en la relacion de aavv con las quotas adicionales
     * si existe la quota "email"
     *
     * @param $aavv
     * @return bool
     * @version /10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function hasPersonalizedMail($aavv)
    {
        $additionalQuotas = $aavv->getAdditionalQuota();
        foreach ($additionalQuotas as $Quota) {
            if (strcmp($Quota->getDescription(),"email") == 0)
                return true;
        }
        return false;
    }

    /**
     * Funcion encargada de conseguir las quotas que existen en el sistema
     *
     * @param $quotas object coleccion de quotas existentes en el sistema
     * @param $emailCost float costo de la cuota por correo
     * @param $maintenanceCost float costo de la couta por mantenimiento del sitio
     * @param $userCost float costo de la couta por licencia por usuarios
     * @author Isabel Nieto
     */
    public function existingQuotas($quotas, &$emailCost, &$maintenanceCost, &$userCost)
    {
        // Obtenemos los costos de cada quota que exista
        foreach ($quotas as $quota) {
            if (strcmp($quota->getDescription(), 'email') == 0)
                $emailCost = $quota->getAmount();
            else if (strcmp($quota->getDescription(), 'maintenance') == 0)
                $maintenanceCost = $quota->getAmount();
            else
                $userCost = $quota->getAmount();
        }
    }

    /**
     * Funcion para construir la fecha inicial de la consulta
     *
     * @param $lastDate
     * @return \DateTime
     * @author Isabel Nieto
     * @verion
     */
    public function buildInitDate($lastDate)
    {
        $day = 1;
        $month = $lastDate->format('m');
        $year = $lastDate->format('Y');
        $newDate = $day . "-" . $month . "-" . $year;
        $initDate = new \DateTime("$newDate 00:00:00");

        return $initDate;
    }
}