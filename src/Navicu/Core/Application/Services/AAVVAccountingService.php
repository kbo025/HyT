<?php
namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions;
use Navicu\Core\Domain\Model\Entity\AAVV;

/**
 * clase que implementa metodos estaticos para el manejo de los movimientos
 * financieros de una agencia de viaje.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVAccountingService
{

    /**
     * @var RepositoryFactoryInterface $rf
     */
    static private $rf;

    /**
     * AAVVAccountingService constructor.
     *
     * @param RepositoryFactoryInterface $rf
     */
    public function __construct(RepositoryFactoryInterface $rf)
    {
        self::$rf = $rf;
    }

    /**
     * Función usada para alamacenar un movimiento financiero
     * de una Agencia de Viaje dado un conjunto de datos.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $data             Array Un arreglo con: desciption, sign y amount
     * @param AAVV $aavv        Objeto AAVV
     * @return boolean
     */
    public static function setMovement($data,AAVV $aavv)
    {
        $financialTransactions = new AAVVFinancialTransactions();
        $financialTransactions->updateObject($data);
        $financialTransactions->setAavv($aavv);
        $aavv->addFinancialTransaction($financialTransactions);

        self::$rf->get("AAVVFinancialTransactions")->save($financialTransactions);
        
        return true;
    }

    /**
     * Función usada para calcular el balance de una Agencia de Viaje
     * dado un conjunto de movimientos financieros.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $aavv             Objeto AAVV
     * @return boolean
     */
    public static function balanceCalculator(AAVV $aavv)
    {
        if (!$aavv->getFinancialTransactions())
            return false;

        $transactions = $aavv->getFinancialTransactions()->toArray();

        $creditAvailable = 0;
        for ($t = 0; $t < count($transactions); $t++) {
            $sign = $transactions[$t]->getSign() == "-" ? -1 : 1;
            $creditAvailable += ($sign * $transactions[$t]->getAmount());
        }

        $aavv->setCreditAvailable($creditAvailable * -1);
        self::$rf->get("AAVV")->save($aavv);

        return true;
    }
}
