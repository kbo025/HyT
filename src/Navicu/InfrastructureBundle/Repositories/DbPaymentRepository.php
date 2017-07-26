<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 26/12/16
 * Time: 02:49 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\PaymentRepository;
use Navicu\Core\Domain\Repository\PermissionRepository;
use Navicu\InfrastructureBundle\Entity\Permission;

class DbPaymentRepository extends DbBaseRepository implements PaymentRepository
{
    /**
     * Funcion que actualiza en una sola consulta a la base de datos los montos introducidos
     * por el admin
     *
     * @param $paramToBuildSql
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     * @author Isabel Nieto
     * @version 26/12/2016
     */
    public function updatePayments($paramToBuildSql)
    {
        /**
         * La estructura ParamToBuildSql es la siguiente
         *
            "status": 1, //cumplido = 1 o rechazada = 2
            "arrayTransferred": [{
                "amountTransferred":15000,
                "paymentId":439
            },
            {
                "amountTransferred":1800,
                "paymentId":438
            }]
         *
         */
        // Vamos construyendo la estructura para ejecutar la consulta SQL
        $update = 'UPDATE payment SET status = '.$paramToBuildSql['status'].', amounttransferred = CASE id ';
        $values = "";
        $where = "WHERE id IN (";
        $paymentIds = "";
        $entry = false;

        foreach ($paramToBuildSql["arrayTransferred"] as $dataToApply) {
            if (!is_null($dataToApply['amountTransferred'])) {
                $entry = true;
                $values = "WHEN " . $dataToApply['paymentId'] . " " .
                    "THEN " . $dataToApply['amountTransferred'] .
                    " " . $values;

                $id[] = $dataToApply['paymentId'];
                $paymentIds = $dataToApply['paymentId'] . "," . $paymentIds;
            }
        }

        // Si al menos existe un campo que actualizar
        if ($entry) {
            $values = $values . "END ";
            // Eliminamos la ultima coma introducida
            $paymentIds = rtrim($paymentIds, ",");
            $sql = $update . $values . $where . $paymentIds . ")";

            return $this->getEntityManager()
                ->getConnection()
                ->executeUpdate($sql);
        }
        return 0;
//        Paso de parametros cuando sea necesario
//            ->executeUpdate('UPDATE aavv SET merchant_id = 1 WHERE aavv.id = :id', array('id' => $value));
    }
}