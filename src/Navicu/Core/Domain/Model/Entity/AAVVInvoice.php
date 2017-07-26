<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVInvoice
 */
class AAVVInvoice
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha de la factura.
     *
     * @var \DateTime
     */
    private $date;

    /**
     * Esta propiedad es usada para interactuar con la fecha de vencimiento de la factura.
     * @var \DateTime
     */
    private $due_date;

    /**
     * Esta propiedad es usada para interactuar con la descripción de la factura.
     *
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $number;

    /**
     * Esta propiedad es usada para interactuar con el estatus de la factura.
     *  0: Pendiente
     *  1: Pagada/Cobrada
     *  2: Cancelada/Anulada
     *  3: Vencida
     *
     * @var integer
     */
    private $status;

    /**
     * Valor resultando de la aplicaicon del tax_rate de las facturas
     *
     * @var float
     */
    private $tax;

    /**
     * Esta propiedad es usada para interactuar con el porcentaje de iva de la factura.
     *
     * @var float
     */
    private $tax_rate;

    /**
     * @var float
     */
    private $subtotal;

    /**
     * Esta propiedad es usada para interactuar con el monto total incluido el iva de la factura.
     *
     * @var float
     */
    private $total_amount;

    /**
     * @var string
     */
    private $type;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aavv_reservation_group;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lines;

    /**
     * Esta propiedad es usada para interactuar con la agencia dueña de la factura.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * Esta propiedad es usada para interactuar con el tipo divisa de la factura.
     *
     * @var \Navicu\Core\Domain\Model\Entity\CurrencyType
     */
    private $currency_type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocation = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aavv_reservation_group = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AAVVInvoice
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
     * Set due_date
     *
     * @param \DateTime $dueDate
     * @return AAVVInvoice
     */
    public function setDueDate($dueDate)
    {
        $this->due_date = $dueDate;

        return $this;
    }

    /**
     * Get due_date
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->due_date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AAVVInvoice
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return AAVVInvoice
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AAVVInvoice
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
     * Set tax
     *
     * @param float $tax
     * @return AAVVInvoice
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return float 
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set tax_rate
     *
     * @param float $taxRate
     * @return AAVVInvoice
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return float 
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return AAVVInvoice
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float 
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set total_amount
     *
     * @param float $totalAmount
     * @return AAVVInvoice
     */
    public function setTotalAmount($totalAmount)
    {
        $this->total_amount = $totalAmount;

        return $this;
    }

    /**
     * Get total_amount
     *
     * @return float 
     */
    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return AAVVInvoice
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVInvoice
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
     * @return AAVVInvoice
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
     * @return AAVVInvoice
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
     * @return AAVVInvoice
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
     * @return AAVVInvoice
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
     * Add aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     * @return AAVVInvoice
     */
    public function addAavvReservationGroup(\Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup)
    {
        $this->aavv_reservation_group[] = $aavvReservationGroup;

        return $this;
    }

    /**
     * Remove aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     */
    public function removeAavvReservationGroup(\Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup)
    {
        $this->aavv_reservation_group->removeElement($aavvReservationGroup);
    }

    /**
     * Get aavv_reservation_group
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAavvReservationGroup()
    {
        return $this->aavv_reservation_group;
    }

    /**
     * Add lines
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoiceDetail $lines
     * @return AAVVInvoice
     */
    public function addLine(\Navicu\Core\Domain\Model\Entity\AAVVInvoiceDetail $lines)
    {
        $this->lines[] = $lines;

        return $this;
    }

    /**
     * Remove lines
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoiceDetail $lines
     */
    public function removeLine(\Navicu\Core\Domain\Model\Entity\AAVVInvoiceDetail $lines)
    {
        $this->lines->removeElement($lines);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVInvoice
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
     * Set currency_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\CurrencyType $currencyType
     * @return AAVVInvoice
     */
    public function setCurrencyType(\Navicu\Core\Domain\Model\Entity\CurrencyType $currencyType = null)
    {
        $this->currency_type = $currencyType;

        return $this;
    }

    /**
     * Get currency_type
     *
     * @return \Navicu\Core\Domain\Model\Entity\CurrencyType 
     */
    public function getCurrencyType()
    {
        return $this->currency_type;
    }
}
