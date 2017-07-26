<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 19/09/16
 * Time: 11:00 AM
 */

namespace Navicu\InfrastructureBundle\Resources\PaymentGateway;


use Navicu\Core\Application\Contract\json;
use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\AAVVAccountingService;
use Navicu\Core\Application\Services\RateExteriorCalculator;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Payment para procesar las reservas exitosamente sobre la aavv cuando tenga saldo suficiente
 * y devolver un msj en caso de exito y en caso contrario
 *
 * Class AAVVPaymentGateway
 * @package Navicu\InfrastructureBundle\Resources\PaymentGateway
 */
class AAVVPaymentGateway implements PaymentGateway
{
    /**
     * define una antelación para este tipo de pago
     */
    const CUTOFF = 0;

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
     * @param $request
     * @return array|json|Object
     * @throws EntityValidationException
     */
    public function processPayment($request)
    {
        if ($this->validateRequestData($request)) {
            $request = $this->formaterRequestData($request);
            $response = $this->formaterResponseData($request);
            $this->success = $this->success && $response['success'];
        }
        return $response;
    }

    /**
     * este metodo toma un conjunto de pagos, los valida y los procesa
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @param $request
     * @param int $totalAmount
     * @return array|json|Object
     */
    public function processPayments($request)
    {
        $this->success = true;
        return $this->processPayment($request);

    }

    /**
     * debe validar que todos los campos requeridos para hacer la solicitud esten completos y sean correctos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  boolean
     */
    public function validateRequestData($request)
    {
        if (empty($request['date']) || !$this->checkDate($request['date']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_date');
        if (empty($request['amount']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_amount');
        if(empty($request['checkInDate']) && !($request['checkInDate'] instanceof \DateTime))
            throw new EntityValidationException('payments',\get_class($this),'invalid_reservation_check_in');
        if (empty($request['aavv']))
            throw new Exception('no aavv selected', 400);

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
        $amount = str_replace(",","",(string)$request['amount']);  //Eliminar las comas del monto a cobrar

        return [
            'amount' => (string)$amount,
            'sign' => "+",
            'desciption' => $request['description'],
            'aavv' => $request['aavv'],
            'ip' => $request['ip'],
            'total_amount_per_aavv' => $request['total_amount_per_aavv'],
        ];
    }

    /**
     * debe devolver la data requerida para ser devuelta a el caso de uso segun el formato que necesite
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterResponseData($response)
    {
        $response['success'] = false;
        $amount = str_replace(",","",(string)$response['amount']);  //Eliminar las comas del monto a cobrar

        $availabilityCredit = $response['aavv']->getCreditAvailable();
        // Credito disponible de la aavv mas la mitad del credito de navicu
        $availabilityCreditPlusNavicuGain = ($availabilityCredit + ($response['aavv']->getNavicuGain() / 2) );
        $totalBilling = $response['total_amount_per_aavv'];
        // Si el credito que tiene la aavv es mayor al monto que se quiere reservar
        if (($availabilityCredit >= 0) AND ($availabilityCreditPlusNavicuGain - $totalBilling >= 0) )  {
            $response['success'] = true;
            $code = 201;
        }
        else
            $code = 400;

        return $response = [
            'success' => $response['success'],
            'code' => $code,
            'status' => $response["success"] ? 1 : 0,
            'amount' => $amount,
        ];
    }

    /**
     * devueleve un entero que representa el estado de la reserva segun la condicion de los pagos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function getStatusReservation(Reservation $reservation)
    {
        $status = 0; //default pre-reserva pendiente por pago
        if (count($reservation->getPayments()) == 0)
            return $status;
        $paid = true;
        foreach ($reservation->getPayments() as $payment) {
            $paid = $paid && ($payment->getState() == 1);
        }
        if($paid)
            $status = 2; //confirmada
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

    /**
     *  funcion que devuelve el tipo de pago definido entre las constantes de esta interfaz
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17-08-2016
     */
    public function getTypePayment()
    {
        return PaymentGateway::AAVV;
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
     * devuelve un array clave => valor con los estados posibles de el tipo de reservas que implementa
     */
    public function getStates()
    {
        return [];
    }

    /**
     * establece en que moneda se realizaran los cobros
     *
     * @param string $currency
     * @param RepositoryFactoryInterface $rf
     * @return Object
     */
    public function setCurrency($currency = 'VEF', RepositoryFactoryInterface $rf = null)
    {
        $this->currency = $currency;
        $this->rf = $rf;
    }

    /**
     * devuelve el tipo de moneda que maneja la instancia de paymentgateway
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    private function checkDate($date)
    {
        return strtotime($date) >= strtotime(\date('d-m-Y'));
    }
}