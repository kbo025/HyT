<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlightPayment
 */
class FlightPayment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $amountTransferred;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $holder;

    /**
     * @var string
     */
    private $holderId;

    /**
     * @var array
     */
    private $response;

    /**
     * @var integer
     */
    private $status = 0;

    /**
     * @var integer
     */
    private $state = 0;

    /**
     * @var array
     */
    private $currency_convertion_information;

    /**
     * @var string
     */
    private $ip_address;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\FlightReservation
     */
    private $reservation;


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
     * @return FlightPayment
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
     * @return FlightPayment
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
     * @return FlightPayment
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
     * @return FlightPayment
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
     * Set amountTransferred
     *
     * @param float $amountTransferred
     * @return FlightPayment
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
     * Set type
     *
     * @param integer $type
     * @return FlightPayment
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
     * Set holder
     *
     * @param string $holder
     * @return FlightPayment
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
     * @return FlightPayment
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
     * Set response
     *
     * @param array $response
     * @return FlightPayment
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
     * Set status
     *
     * @param integer $status
     * @return FlightPayment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return FlightPayment
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set currency_convertion_information
     *
     * @param array $currencyConvertionInformation
     * @return FlightPayment
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
     * Set ip_address
     *
     * @param string $ipAddress
     * @return FlightPayment
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
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\FlightReservation $reservation
     * @return FlightPayment
     */
    public function setReservation(\Navicu\Core\Domain\Model\Entity\FlightReservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Navicu\Core\Domain\Model\Entity\FlightReservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }
}
