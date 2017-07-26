<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Clase AAVVAllocationOfInvoicePayment.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * asignaciones de Pago a Facturas y viceversa.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVAllocationOfInvoicePayment
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
     * Esta propiedad es usada para interactuar con la fecha del Pago.
     *
     * @var \DateTime
     */
    private $date;

    /**
     * Esta propiedad es usada para interactuar con el monto con al que se le asigna
     * a la factura.
     *
     * @var float
     */
    private $allocation_amount;

    /**
     * Esta propiedad es usada para interactuar con las facturas asignadas a un pago.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVInvoice
     */
    private $invoice;

    /**
     * Esta propiedad es usada para interactuar con los pagos asignados a una factura.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments
     */
    private $payment;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->invoice = new ArrayCollection();
        $this->payment = new ArrayCollection();
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
     * @return AAVVAllocationOfInvoicePayment
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
     * Set allocation_amount
     *
     * @param string $allocationAmount
     * @return AAVVAllocationOfInvoicePayment
     */
    public function setAllocationAmount($allocationAmount)
    {
        $this->allocation_amount = $allocationAmount;

        return $this;
    }

    /**
     * Get allocation_amount
     *
     * @return string 
     */
    public function getAllocationAmount()
    {
        return $this->allocation_amount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVAllocationOfInvoicePayment
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
     * @return AAVVAllocationOfInvoicePayment
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
     * @return AAVVAllocationOfInvoicePayment
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
     * @return AAVVAllocationOfInvoicePayment
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
     * Set invoices
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoices
     * @return AAVVAllocationOfInvoicePayment
     */
    public function setInvoice(\Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoices
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVInvoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set payments
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments $payments
     * @return AAVVAllocationOfInvoicePayment
     */
    public function setPayments(\Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payments
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments 
     */
    public function getPayments()
    {
        return $this->payment;
    }

    /**
     * Set payment
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments $payment
     * @return AAVVAllocationOfInvoicePayment
     */
    public function setPayment(\Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVInvoicePayments 
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
