<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVAgreement
 */
class AAVVAgreement
{
    /**
     * identificador
     *
     * @var integer
     */
    private $id;

    /**
     * direccion ip desde donde el cliente aceptó los terminos y condiciones
     *
     * @var string
     */
    private $client_ip_address;

    /**
     * feccha y hora en que se acepto los terminos y condiciones
     *
     * @var \DateTime
     */
    private $date;

    /**
     * dias de credito acordados con la agencia
     *
     * @var integer
     */
    private $credit_days = 30;

    /**
     * relacion con la agencia a quien pertenece
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * relacion con el objeto document que representa el pdf
     *
     * @var \Navicu\Core\Domain\Model\Entity\Document
     */
    private $pdf;

    /**
     * taza de descuento acordada con la agencia
     *
     * @var float
     */
    private $discount_rate = 0.1;


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
     * Set client_ip_address
     *
     * @param string $clientIpAddress
     * @return AAVVAgreement
     */
    public function setClientIpAddress($clientIpAddress)
    {
        $this->client_ip_address = $clientIpAddress;

        return $this;
    }

    /**
     * Get client_ip_address
     *
     * @return string 
     */
    public function getClientIpAddress()
    {
        return $this->client_ip_address;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return AAVVAgreement
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
     * Set credit_days
     *
     * @param integer $creditDays
     * @return AAVVAgreement
     */
    public function setCreditDays($creditDays)
    {
        $this->credit_days = $creditDays;

        return $this;
    }

    /**
     * Get credit_days
     *
     * @return integer 
     */
    public function getCreditDays()
    {
        return $this->credit_days;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVAgreement
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
     * Set pdf
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $pdf
     * @return AAVVAgreement
     */
    public function setPdf(\Navicu\Core\Domain\Model\Entity\Document $pdf = null)
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * Get pdf
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document 
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Set discount_rate
     *
     * @param float $discountRate
     * @return AAVVAgreement
     */
    public function setDiscountRate($discountRate)
    {
        $this->discount_rate = $discountRate;

        return $this;
    }

    /**
     * Get discount_rate
     *
     * @return float 
     */
    public function getDiscountRate()
    {
        return $this->discount_rate;
    }
}
