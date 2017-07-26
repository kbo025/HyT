<?php

namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase AAVVBankPayments.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * depositos y transferencias realizados por la Agencia de Viajes a Navicu.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVBankPayments
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var date
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $createdBy;

    /**
     * @var date
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $updatedBy;

    /**
     * Esta propiedad es usada para interactuar con la fecha en la que se realizo el pago.
     *
     * @var \DateTime
     */
    private $date;

    /**
     * Esta propiedad es usada para interactuar con el numero de deposito o transferencia.
     *
     * @var String
     */
    private $number_reference;

    /**
     * Esta propiedad es usada para interactuar con el monto con el que se realizo el pago.
     *
     * @var float
     */
    private $amount;

    /**
     * Esta propiedad es usada para interactuar con el tipo de pago.
     * 0: Deposito
     * 1: Transferencia
     *
     * @var integer
     */
    private $type;

    /**
     * Esta propiedad es usada para interactuar con el estatus del pago.
     *  0: Pendiente
     *  1: Pagada
     *  2: Cancelada
     *
     * @var integer
     */
    private $status;

    /**
     * Esta propiedad es usada para interactuar con el tipo de banco del pago.
     *
     * @var \Navicu\Core\Domain\Model\Entity\BankType
     */
    private $bank_type;

    /**
     * Esta propiedad es usada para interactuar con la agendcia dueña del pago.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->status = 0;
        $this->date = new \DateTime("now");
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
     * Set date
     *
     * @param \DateTime $date
     * @return AAVVBankPayments
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
     * Set type
     *
     * @param integer $type
     * @return AAVVBankPayments
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
     * Set status
     *
     * @param integer $status
     * @return AAVVBankPayments
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
     * Set number_reference
     *
     * @param string $numberReference
     * @return AAVVBankPayments
     */
    public function setNumberReference($numberReference)
    {
        $this->number_reference = $numberReference;

        return $this;
    }

    /**
     * Get number_reference
     *
     * @return string 
     */
    public function getNumberReference()
    {
        return $this->number_reference;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return AAVVBankPayments
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVBankPayments
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return AAVVBankPayments
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return AAVVBankPayments
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param integer $updatedBy
     * @return AAVVBankPayments
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVBankPayments
     */
    public function setAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavv = null)
    {
        $this->aavv = $aavv;

        return $this;
    }

    /**
     * Get aavv
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVV 
     */
    public function getAavv()
    {
        return $this->aavv;
    }

    /**
     * Set bank_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\BankType $bankType
     * @return AAVVBankPayments
     */
    public function setBankType(\Navicu\Core\Domain\Model\Entity\BankType $bankType = null)
    {
        $this->bank_type = $bankType;

        return $this;
    }

    /**
     * Get bank_type
     *
     * @return \Navicu\Core\Domain\Model\Entity\BankType 
     */
    public function getBankType()
    {
        return $this->bank_type;
    }

    /**
     * La función actualiza los datos de un AAVVBankPayments,
     * dado un array ($data) con los valores asociados a un deposito
     * bancario.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return void
     */
    public function updateObject($data)
    {
        if (isset($data['number']))
            $this->number_reference = $data['number'];
        if (isset($data['amount']))
            $this->amount = $data['amount'];
        if (isset($data['paymentType']))
            $this->type = $data['paymentType'] =="" ?null :$data['paymentType'];
        if (isset($data['status']))
            $this->status = $data['status'];

        return $this;
    }
}
