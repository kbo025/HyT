<?php

namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase AAVVCreditNVC.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * los creditos y sus importes ofrecidos por Navicu a AAVV.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVCreditNVC
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Esta propiedad es usada para interactuar con el credito ofrecido a AAVV
     * en su registro.
     *
     * @var float
     */
    private $credit;

    /**
     * Esta propiedad es usada para interactuar con el importe que tendra la AAVV
     * una vez registrada.
     *
     * @var float
     */
    private $amount_rate;

    /**
     * @var boolean
     */
    private $delete;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set credit
     *
     * @param float $credit
     * @return AAVVCreditNVC
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float 
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set amount_rate
     *
     * @param float $amountRate
     * @return AAVVCreditNVC
     */
    public function setAmountRate($amountRate)
    {
        $this->amount_rate = $amountRate;

        return $this;
    }

    /**
     * Get amount_rate
     *
     * @return float 
     */
    public function getAmountRate()
    {
        return $this->amount_rate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVCreditNVC
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
     * @return AAVVCreditNVC
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
     * @return AAVVCreditNVC
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
     * @return AAVVCreditNVC
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
     * Set delete
     *
     * @param boolean $delete
     * @return AAVVCreditNVC
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * Get delete
     *
     * @return boolean 
     */
    public function getDelete()
    {
        return $this->delete;
    }
}
