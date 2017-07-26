<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\ValueObject\BankAccount;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\CurrencyType;
use Navicu\Core\Domain\Model\Entity\Location;

/**
 * PaymentInfoProperty
 */
class PaymentInfoProperty
{
    /**
     * identificador en la BD
     * @var string
     */
    private $id;

    /**
     * indica si se utiliza los mismos datos del establecimiento
     * @var boolean
     */
    private $same_data_property;

    /**
     * tipo de transaccion bajo la cual se pagara
     * 1: Transferencia bancaria
     * @var integer
     */
    private $charging_system;

    /**
     * codigo fiscal bajo el cual se emitira la factura (RIF)
     * @var string
     */
    private $tax_id;

    /**
     * codigo que identifica las entidades bancarias a nivel mundial
     * @var string
     */
    private $swift;

    /**
     * nombre fiscal para la facturacion
     * @var string
     */
    private $name;

    /**
     * direccion de facturacion
     * @var string
     */
    private $address;

    /**
     * relacion con el establecimiento al cual pertenece
     * @var Property
     */
    private $property;

    /**
     * indica el tipo de moneda en la que se pagara
     * @var CurrencyType
     */
    private $currency;

    /**
     * indica la localidad de facturacion
     * @var Location
     */
    private $location;

    /**
     * OV que representa el numero de cuenta asociado al establecimiento
     * @var array
     */
    private $account;

    /**
     * Set id
     *
     * @param string $id
     * @return PaymentInfoProperty
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set same_data_property
     *
     * @param boolean $sameDataProperty
     * @return PaymentInfoProperty
     */
    public function setSameDataProperty($sameDataProperty)
    {
        $this->same_data_property = $sameDataProperty;

        return $this;
    }

    /**
     * Get same_data_property
     *
     * @return boolean 
     */
    public function getSameDataProperty()
    {
        return $this->same_data_property;
    }

    /**
     * Set charging_system
     *
     * @param integer $chargingSystem
     * @return PaymentInfoProperty
     */
    public function setChargingSystem($chargingSystem)
    {
        $this->charging_system = $chargingSystem;

        return $this;
    }

    /**
     * Get charging_system
     *
     * @return integer 
     */
    public function getChargingSystem()
    {
        return $this->charging_system;
    }

    /**
     * Set tax_id
     *
     * @param string $taxId
     * @return PaymentInfoProperty
     */
    public function setTaxId($taxId)
    {
        $this->tax_id = $taxId;

        return $this;
    }

    /**
     * Get tax_id
     *
     * @return string 
     */
    public function getTaxId()
    {
        return $this->tax_id;
    }

    /**
     * Set swift
     *
     * @param string $swift
     * @return PaymentInfoProperty
     */
    public function setSwift($swift)
    {
        $this->swift = $swift;

        return $this;
    }

    /**
     * Get swift
     *
     * @return string 
     */
    public function getSwift()
    {
        return $this->swift;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentInfoProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return PaymentInfoProperty
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set property
     *
     * @param Property $property
     * @return PaymentInfoProperty
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set currency
     *
     * @param CurrencyType $currency
     * @return PaymentInfoProperty
     */
    public function setCurrency(CurrencyType $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return CurrencyType
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set location
     *
     * @param Location $location
     * @return PaymentInfoProperty
     */
    public function setLocation(Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set account
     *
     * @param array $account
     * @return PaymentInfoProperty
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return array 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
    *   funcion que se ejectuta antes de almacenar una entidad en la BD
    *
    */
    public function prePersist()
    {
        if(isset($this->account) AND ($this->account instanceof BankAccount)) {
            $this->account = $this->account->toArray();
        }
    }
    /**
     * @var \Navicu\Core\Domain\Model\Entity\Document
     */
    private $rif;


    /**
     * Set rif
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $rif
     *
     * @return PaymentInfoProperty
     */
    public function setRif(\Navicu\Core\Domain\Model\Entity\Document $rif = null)
    {
        $this->rif = $rif;

        return $this;
    }

    /**
     * Get rif
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document
     */
    public function getRif()
    {
        return $this->rif;
    }
}
