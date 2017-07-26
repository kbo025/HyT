<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\StateMachine;
use Navicu\Core\Domain\Adapter\EntityValidationException;


class Payment extends StateMachine
{
    /**
     * identificador de la entidad
     *
     * @var integer
     */
    private $id;

    /**
     * codigo de pago generado por el banco
     *
     * @var string
     */
    private $code;

    /**
     * referencia de transaccion generada por el banco
     *
     * @var string
     */
    private $reference;

    /**
     * fecha en la que se hizo la transaccion
     *
     * @var \DateTime
     */
    private $date;

    /**
     * cantidad por la que se hizo la transaccion
     *
     * @var float
     */
    private $amount;

    /**
     * tipo de pago, corresponde con los paymentgateway que existen en la app
     * 1 : instapago
     * 2 : transferencia nacional
     * 3 : stripe
     * 4 : transferencia internacional
     * 5 : Credito de AAVV
     *
     * @var integer
     */
    private $type;

    /**
     * relacion con la reservacion a la cual
     *
     * @var \Navicu\Core\Domain\Model\Entity\Reservation
     */
    private $reservation;

    /**
     * nombre de quien realizó el pago
     *
     * @var string
     */
    private $holder;

    /**
     * documento de identifiacion de quien realizo el pago
     *
     * @var string
     */
    private $holderId;

    /**
     * respuesta de entidades externas o datos adicionales
     *
     * @var array
     */
    private $response;

    /**
     * estado del pago
     * 0: pendiente
     * 1: cumplido
     * 2: rechazado
     *
     * @var integer;
     */
    private $status;

    /**
     * relacion con el objeto BankType representa un banco relacionado con el pago
     *
     * @var \Navicu\Core\Domain\Model\Entity\BankType
     */
    private $bank;

    /**
     * informafcion de cambio para moneda extranjera
     *
     * @var array
     */
    private $currency_convertion_information;

    /**
     *
     * @var integer
     */
    protected $state;

    protected $states = [
        'initial' => 0,
        'approved' => 1,
        'cancelled' => 2,
    ];

    protected $transitions = [
        'approve_payment' => [0,1],
        'reject_payment' => [0,2],
    ];

    /**
     * monto real transferido, valor introducido por admin
     *
     * @var float
     */
    private $amountTransferred;

    /**
     * referencia al banco receptor
     * 
     * @var \Navicu\Core\Domain\Model\Entity\BankType
     */
    private $receiverBank;

    /**
     * dirección ip del pago
     *
     * @var string
     */
    private $ip_address;

    public function __construct()
    {
        $this->state = 0;
        $this->status = 0;
        $this->currency_convertion_information = [];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Payment
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Payment
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Payment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservation
     * @return Payment
     */
    public function setReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Navicu\Core\Domain\Model\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set holder
     *
     * @param string $holder
     * @return Payment
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
    }

    /**
     * Get holder
     *
     * @return string
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * Set holderId
     *
     * @param string $holderId
     * @return Payment
     */
    public function setHolderId($holderId)
    {
        $this->holderId = $holderId;

        return $this;
    }

    /**
     * Get holderId
     *
     * @return string
     */
    public function getHolderId()
    {
        return $this->holderId;
    }

    /**
     * devuelve una representacion del pago en array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'code' => $this->code,
            'reference' => $this->reference,
            'date' => $this->date,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'bank' => isset($this->bank) ? $this->bank->getId() : null,
            'holder' => $this->holder,
            'holderId' => $this->holderId,
            'response' => $this->response,
        ];
    }

    /**
     * Set response
     *
     * @param array $response
     * @return Payment
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get status
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Payment
     */
    public function setStatus($status)
    {
        parent::setState($status);
        $this->status = $status;

        return $this;
    }


    /**
     * Set bank
     *
     * @param \Navicu\Core\Domain\Model\Entity\BankType $bank
     * @return Payment
     */
    public function setBank(\Navicu\Core\Domain\Model\Entity\BankType $bank = null)
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * Get bank
     *
     * @return \Navicu\Core\Domain\Model\Entity\BankType 
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * Set currency_convertion_information
     *
     * @param array $currencyConvertionInformation
     * @return Payment
     */
    public function setCurrencyConvertionInformation($currencyConvertionInformation)
    {
        $this->currency_convertion_information = $currencyConvertionInformation;

        return $this;
    }

    /**
     * Get currency_convertion_information
     *
     * @return array
     */
    public function getCurrencyConvertionInformation()
    {
        return $this->currency_convertion_information;
    }

    /**
     * indica si es un pago en moneda extranjera o no
     */
    public function isForeignCurrency()
    {
        return !empty($this->currency_convertion_information);
    }

    public function setAlphaCurrency($currency)
    {
        $this->currency_convertion_information['alphaCurrency'] = $currency;

        return $this;
    }

    /**
     * indica la moneda en que se expresa el monto del pago
     */
    public function getAlphaCurrency()
    {
        return isset($this->currency_convertion_information['alphaCurrency']) ?
                $this->currency_convertion_information['alphaCurrency'] :
                 null;
    }

    /**
     * indica la taza de conversion entre el dolar y el bolivar
     */
    public function getDollarPrice()
    {
        return isset($this->currency_convertion_information['dollarPrice']) ?
            $this->currency_convertion_information['dollarPrice'] :
            null;
    }

    /**
     * set del dollarRate
     */
    public function setDollarPrice($dr)
    {
        $this->currency_convertion_information['dollarPrice'] = $dr;

        return $this;
    }

    /**
     * indica la taza de conversion la moneda usada y el dolar
     */
    public function getNationalPrice()
    {
        return isset($this->currency_convertion_information['nationalPrice']) ?
            $this->currency_convertion_information['nationalPrice'] :
            null;
    }

    /**
     * set del currencyRate
     */
    public function setNationalPrice($cr)
    {
        $this->currency_convertion_information['nationalPrice'] = $cr;

        return $this;
    }

    /**
     * Set amountTransferred
     *
     * @param float $amountTransferred
     * @return Payment
     */
    public function setAmountTransferred($amountTransferred)
    {
        $this->amountTransferred = $amountTransferred;

        return $this;
    }

    /**
     * Get amountTransferred
     *
     * @return float 
     */
    public function getAmountTransferred()
    {
        return $this->amountTransferred;
    }

    /**
     * Set receiverBank
     *
     * @param \Navicu\Core\Domain\Model\Entity\BankType $receiverBank
     * @return Payment
     */
    public function setReceiverBank(\Navicu\Core\Domain\Model\Entity\BankType $receiverBank = null)
    {
        $this->receiverBank = $receiverBank;

        return $this;
    }

    /**
     * Get receiverBank
     *
     * @return \Navicu\Core\Domain\Model\Entity\BankType 
     */
    public function getReceiverBank()
    {
        return $this->receiverBank;
    }

    /**
     * Set ip_address
     *
     * @param string $ipAddress
     * @return Payment
     */
    public function setIpAddress($ipAddress)
    {
        $this->ip_address = $ipAddress;

        return $this;
    }

    /**
     * Get ip_address
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * esta funcion es usada para que tanto status como state mantenga el mismo valor tras una actualización
     * esto funcionará asi hasta que se pueda eliminar la columna status de reservation 
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setStatusByState()
    {
        $this->status = $this->state;

        return $this;
    }

    public function setState($state) 
    {
        parent::setState($state);
        $this->status = $state;

        return $this;
    }
}
