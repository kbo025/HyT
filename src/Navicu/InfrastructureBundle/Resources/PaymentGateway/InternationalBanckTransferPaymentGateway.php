<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 17/08/16
 * Time: 10:00 AM
 */

namespace Navicu\InfrastructureBundle\Resources\PaymentGateway;

use Navicu\Core\Application\Contract\json;
use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\RateExteriorCalculator;

class InternationalBanckTransferPaymentGateway implements PaymentGateway
{

    /**
     * define una antelación para este tipo de pago
     */
    const CUTOFF = 5;

    /**
     * indica si el pago se realizco con exito
     */
    private $success;


    /**
     * indica la moneda de la operacion
     */
    private $currency;

    /**
     * repósitory factory
     */
    private $rf;

    /**
     * este metodo toma un conjunto de pagos, los valida y los procesa
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  Object | json | array
     */
    public function processPayment($request)
    {
        $this->validateRequestData($request);
        $request = $this->formaterRequestData($request);
        $response = $this->formaterResponseData($request);
        $this->success = $this->success && $response['success'];
        return $response;
    }

    /**
     * este metodo toma un conjunto de pagos, los valida y los procesa
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  Object | json | array
     */
    public function processPayments($request)
    {
        $this->success = true;
        $response = [];
        foreach($request as $payment) {
            $response[] = $this->processPayment($payment);
        }
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
        if (empty($request['date']) || !$this->checkDate($request['date']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_date');
        if (empty($request['amount']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_amount');
        if (empty($request['description']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_description');
        if (empty($request['ip']))
            throw new EntityValidationException('payments',\get_class($this),'empty_ip');
        if (empty($request['confirmationId']))
            throw new EntityValidationException('payments',\get_class($this),'confirmationId');
        if (empty($request['bank']) || !is_string($request['bank']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_issuing_bank');
        if (empty($request['receivingBank']) || !is_string($request['receivingBank']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_receiving_bank');
        if(empty($request['checkInDate']) && !($request['checkInDate'] instanceof \DateTime))
            throw new EntityValidationException('payments',\get_class($this),'invalid_reservation_check_in');

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
        new RateExteriorCalculator($this->rf,$this->currency,$request['checkInDate']);
        $amount = str_replace(",","",(string)$request['amount']);  //Eliminar las comas del monto a cobrar
        //$amount = RateExteriorCalculator::calculateRateChange($amount); //cambia el monto a la moneda seleccionada

        return array_merge(
            $request,
            [
                'amount' => $amount,
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

        $np = RateExteriorCalculator::calculateRateChangeToBs($response['amount']);

        new RateExteriorCalculator($this->rf,'USD',$response['checkInDate']);

        return array_merge($response,[
            'id' => null,
            'success' => true,
            'code' => '201',
            'status' => '0',
            'response' => null,
            'currency' => $this->currency,
            'dollarPrice' => RateExteriorCalculator::calculateRateChange($np),
            'nationalPrice' => $np,
            'responsecode' => 'success',
            'message' => 'success',
        ]);
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
            if (round($amountPaid,2) >= round($reservation->getCurrencyPrice() * $rate, 2))
                $status = 1;
            if (round($amountConfirmed,2) >= round($reservation->getCurrencyPrice() * $rate, 2))
                $status = 2;
        }
        return $status;
    }

    /**
     * indica si el pago es valido y correcto
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 18-03-2015
     */
    public function isSuccess()
    {
        return $this->success;
    }

    public function getTypePayment()
    {
        return PaymentGateway::INTERNATIONAL_TRANSFER;
    }

    public function getStates()
    {
        return [];
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

    private function checkDate($date)
    {
        return strtotime($date) >= strtotime(\date('d-m-Y'));
    }

    /**
     * establece en que moneda se realizaran los cobros
     *
     * @param string $currency
     * @param RepositoryFactoryInterface $rf
     * @return Object
     */
    public function setCurrency($currency = 'VEF',RepositoryFactoryInterface $rf = null)
    {
        $this->currency = $currency;
        $this->rf = $rf;
    }


    public function getCurrency()
    {
        return $this->currency;
    }
}