<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\Entity\Location;

/**
 * CurrencyType
 */
class CurrencyType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $round;

    /**
     * @var string
     */
    private $alfa3;

    /**
     * @var string
     */
    private $simbol;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $exchange_rate_history;

    /**
     * Variable que determina si la moneda esta siendo utilizada
     * @var boolean
     */
    private $active;

    /**
     * Entero que indica por que numero se debe multiplicar un monto para que llegue a su denominación mas baja, es usado
     * por el api STRIPE ya que el monto se debe expresar de esta manera
     *
     * @var integer
     */
    private $zero_decimal_base;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->exchange_rate_history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return CurrencyType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set title
     *
     * @param string $title
     *
     * @return CurrencyType
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alfa3
     *
     * @param string $alfa3
     *
     * @return CurrencyType
     */
    public function setAlfa3($alfa3)
    {
        $this->alfa3 = $alfa3;

        return $this;
    }

    /**
     * Get alfa3
     *
     * @return string 
     */
    public function getAlfa3()
    {
        return $this->alfa3;
    }

    /**
     * Set simbol
     *
     * @param string $simbol
     *
     * @return CurrencyType
     */
    public function setSimbol($simbol)
    {
        $this->simbol = $simbol;

        return $this;
    }

    /**
     * Get simbol
     *
     * @return string 
     */
    public function getSimbol()
    {
        return $this->simbol;
    }

    /**
     * Add exchange_rate_history
     *
     * @param \Navicu\Core\Domain\Model\Entity\ExchangeRateHistory $exchangeRateHistory
     * @return CurrencyType
     */
    public function addExchangeRateHistory(\Navicu\Core\Domain\Model\Entity\ExchangeRateHistory $exchangeRateHistory)
    {
        $this->exchange_rate_history[] = $exchangeRateHistory;

        return $this;
    }

    /**
     * Remove exchange_rate_history
     *
     * @param \Navicu\Core\Domain\Model\Entity\ExchangeRateHistory $exchangeRateHistory
     */
    public function removeExchangeRateHistory(\Navicu\Core\Domain\Model\Entity\ExchangeRateHistory $exchangeRateHistory)
    {
        $this->exchange_rate_history->removeElement($exchangeRateHistory);
    }

    /**
     * Get exchange_rate_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExchangeRateHistory()
    {
        return $this->exchange_rate_history;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return CurrencyType
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set round
     *
     * @param integer $round
     * @return CurrencyType
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return integer
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set zero_decimal_base
     *
     * @param integer $zeroDecimalBase
     * @return CurrencyType
     */
    public function setZeroDecimalBase($zeroDecimalBase)
    {
        $this->zero_decimal_base = $zeroDecimalBase;

        return $this;
    }

    /**
     * Get zero_decimal_base
     *
     * @return integer 
     */
    public function getZeroDecimalBase()
    {
        return $this->zero_decimal_base;
    }
}
