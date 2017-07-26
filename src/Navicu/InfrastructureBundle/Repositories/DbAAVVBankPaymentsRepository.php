<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 25/10/16
 * Time: 03:18 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\AAVVBankPaymentsRepository;

class DbAAVVBankPaymentsRepository extends DbBaseRepository implements AAVVBankPaymentsRepository 
{
    /**
     * Funcion encargada de buscar dado un id de aavv los movimientos realizados
     *
     * @param $aavvId
     * @param $initDate
     * @param $lastDate
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function findMovementsByAavv($aavvId, $initDate, $lastDate)
    {
        return $this->createQueryBuilder('bp')
            ->join('bp.aavv','aavv')
            ->where('
                    aavv.id = :aavvId and
                    bp.date >= :iniDate and
                    bp.date <= :lastDate
                ')
            ->setParameters(
                array(
                    'aavvId' => $aavvId,
                    'iniDate' => $initDate,
                    'lastDate' => $lastDate,
                )
            )
            ->getQuery()
            ->getResult();
    }
}