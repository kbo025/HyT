<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVInvoicePayments
 */
class AAVVInvoicePayments
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha del Pago.
     *
     * @var \DateTime
     */
    private $date;

    /**
     * Esta propiedad es usada para interactuar con el tipo de pago.
     * 0: Deposito
     * 1: Transferencia
     *
     * @var integer
     */
    private $type;

    /**
     * Esta propiedad es usada para interactuar con el estatus del Pago.
     *  0: Pendiente
     *  1: Pagada/Cobrada
     *  2: Cancelada/Anulada
     *
     * @var integer
     */
    private $status;

    /**
     * Esta propiedad es usada para interactuar con el numero de deposito o transferencia.
     *
     * @var String
     */
    private $number_reference;

    /**
     * Esta propiedad es usada para interactuar con el monto con el que se hizo el pago.
     *
     * @var float
     */
    private $amount;

    /**
     * Esta propiedad es usada para interactuar con el monto actual disponible del pago.
     *
     * @var float
     */
    private $amount_current;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $createdBy;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $updatedBy;

    /**
     * Esta propiedad es usada para interactuar con los pagos asignados a una factura.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment
     */
    private $allocation;

    /**
     * Esta propiedad es usada para interactuar con el tipo de banco del pago.
     *
     * @var \Navicu\Core\Domain\Model\Entity\BankType
     */
    private $bank_type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocation = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * Set amount_current
     *
     * @param float $amountCurrent
     * @return AAVVInvoicePayments
     */
    public function setAmountCurrent($amountCurrent)
    {
        $this->amount_current = $amountCurrent;

        return $this;
    }

    /**
     * Get amount_current
     *
     * @return float 
     */
    public function getAmountCurrent()
    {
        return $this->amount_current;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * @return AAVVInvoicePayments
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
     * Add allocation
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment $allocation
     * @return AAVVInvoicePayments
     */
    public function addAllocation(\Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment $allocation)
    {
        $this->allocation[] = $allocation;

        return $this;
    }

    /**
     * Remove allocation
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment $allocation
     */
    public function removeAllocation(\Navicu\Core\Domain\Model\Entity\AAVVAllocationOfInvoicePayment $allocation)
    {
        $this->allocation->removeElement($allocation);
    }

    /**
     * Get allocation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocation()
    {
        return $this->allocation;
    }

    /**
     * Set bank_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\BankType $bankType
     * @return AAVVInvoicePayments
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
}
