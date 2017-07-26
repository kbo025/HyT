<?php
namespace Navicu\InfrastructureBundle\Resources\PaymentGateway;

use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;


class BanckTransferPaymentGateway implements  PaymentGateway
{

    /**
     * define una antelación para este tipo de pago
     */
    const CUTOFF = 2;

    /**
     * indica si el pago se realizco con exito
     */
    private $success;

    /**
     * constructor de la clase
     *
     * @var Array;
     */
    public function __construct()
    {

    }

    /**
     * este metodo debe establecer la comunicacion con el banco y solicitar procesar el pago
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  Object | \Navicu\Core\Application\Contract\json | array
     */
    public function processPayment($request)
    {
        $this->validateRequestData($request);
        $response = $this->formaterResponseData($request);
        $this->success = $this->success && $response['success'];
        return $response;
    }

    /**
     * @param $request
     * @return array
     */
    public function processPayments($request)
    {
        $this->success = true;
        $response = [];
        foreach ($request as $payment)
            $response[] = $this->processPayment($this->formaterRequestData($payment));
        return $response;
    }

    /**
     * debe validar que todos los campos requeridos para hacer la solicitud esten completos y sean correctos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @param $request
     * @throws EntityValidationException
     * @return  boolean
     */
    public function validateRequestData($request)
    {
        $request['amount'] = str_replace(",","",(string)$request['amount']);  //Eliminar las comas del monto a cobrar

        if (empty($request['date']) || !$this->checkDate($request['date']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_date');
        if (empty($request['amount']) || !is_numeric($request['amount']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_amount'.$request['amount']);
        if (empty($request['description']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_description');
        if (empty($request['ip']))
            throw new EntityValidationException('payments',\get_class($this),'empty_ip');
        if (empty($request['reference']))
            throw new EntityValidationException('payments',\get_class($this),'reference');
        if (empty($request['bank']) || !is_string($request['bank']) || strlen($request['bank'])!=4)

            throw new EntityValidationException('payments',\get_class($this),'invalid_issuing_bank');
        if (empty($request['receivingBank']) || !is_string($request['receivingBank']) || strlen($request['receivingBank'])!=4)
            throw new EntityValidationException('payments',\get_class($this),'invalid_receiving_bank');
        return true;
    }

    /**
     * debe devolver la data requerida para hacer la solicitud formateada segun los requerimientos de la entidad
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterRequestData($request)
    {
        return array_merge(
            $request,
            [
                'reference' => $request['confirmationId'],
                'holder' => null,
                'holderId' => null
            ]
        );
    }

    /**
     * debe devolver la data requerida para ser devuelta a el caso de uso segun el formato que necesite
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterResponseData($response)
    {
        $response['amount'] = str_replace(",","",(string)$response['amount']);  //Eliminar las comas del monto a cobrar

        return array_merge($response,[
            'id' => null,
            'success' => true,
            'code' => '201',
            'status' => 0,
            'response' => null,
            'responsecode' => 'success',
            'message' => 'success',
        ]);
    }

    public function getTypePayment()
    {
        return PaymentGateway::NATIONAL_TRANSFER;
    }

    private function checkDate($date)
    {
        return strtotime($date) >= strtotime(\date('d-m-Y'));
    }

    /**
     * devueleve un entero que representa el estado de la reserva segun la condicion de los pagos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function getStatusReservation(Reservation $reservation)
    {
        $status = 0;
        $amountPaid = 0;
        $amountConfirmed = 0;
        $payments = $reservation->getPayments();
        $rate = 1 - (empty($reservation->getDiscountRateAavv()) ? 0 : $reservation->getDiscountRateAavv());
        if (!$payments->isEmpty()) {
            foreach ($payments as $payment) {
                if ($payment->getStatus() == 0 || $payment->getStatus() == 1)
                    $amountPaid = $amountPaid + $payment->getAmount();
                if ($payment->getStatus() == 1)
                    $amountConfirmed = $amountConfirmed + $payment->getAmount();
            }
            if (round($amountPaid,2) >= round($reservation->getTotalToPay() * $rate, 2))
                $status = 1;
            if (round($amountConfirmed,2) >= round($reservation->getTotalToPay() * $rate, 2))
                $status = 2;
        }
        return $status;
    }

    public function getStates()
    {
        return [];
    }

    /**
    * indica si el pago es valido y correcto
    */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * devuleve la antelacion por defecto para el tipo de pago
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17-08-2016
     */
    public function getCutOff()
    {
        return self::CUTOFF;
    }

    /**
     * establece en que moneda se realizaran los cobros
     */
    public function setCurrency($currency = 'VEF', RepositoryFactoryInterface $rf = null)
    {
        if ($currency!='VEF')
            throw new EntityValidationException('invalid_currency_for_paymentgateway',1);
    }

    public function getCurrency()
    {
        return 'VEF';
    }
}
