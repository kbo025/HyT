<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step2\SetInfoStep2;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para Guardar la información necesaria para el manejo del paso
 * 2 de registro de AAVV.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SetInfoStep2Command implements Command
{

    /**
     * @var integer Id del credito inicial para la AAVV.
     */
    private $credit_id;

    /**
     * @var integer Id del banco con el que la AAVV hizo el deposito.
     */
    private $bank_id;

    /**
     * @var integer Tipo de pago con el que la AAVV hizo el deposito.
     */
    private $payment_type;

    /**
     * @var string Tipo de comprobante con el que la AAVV hizo el deposito.
     */
    private $number;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->credit_id = $data["creditId"] == "" ? null : $data["creditId"];
        $this->bank_id = $data["bankId"] == "" ? null : $data["bankId"];
        $this->payment_type = $data["paymentType"];
        $this->number = $data["number"];
    }

    /**
     *  Metodo getRequest devuelve un array con los parametros del command
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @return  Array
     */
    public function getRequest()
    {
        return [
            "creditId" => $this->credit_id,
            "bankId" => $this->bank_id,
            "paymentType" => $this->payment_type,
            "number" => $this->number
        ];
    }
}