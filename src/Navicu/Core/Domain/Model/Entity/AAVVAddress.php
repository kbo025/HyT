<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Direcciones tanto fisicas como unica direccion de facturacion
 * AAVVAddress
 */
class AAVVAddress
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $address direccion especifica de la aavv
     */
    private $address;

    /**
     * @var string $bank_account numero de cuenta donde se facturara a la aavv
     */
    private $bank_account;

    /**
     * @var string $email correo electronico de la direccion de facturacion de la aavv
     */
    private $email;

    /**
     * @var string $phone numero de telefono de la direccion de facturacion
     */
    private $phone;

    /**
     * @var string $swift codigo de validacion bancaria
     */
    private $swift;

    /**
     * @var integer $type_address tipo de direccion 2 = direccion de facturacion, 0 = cualquier otra
     */
    private $type_address;

    /**
     * @var integer $zip_code codigo de la ciudad
     */
    private $zip_code;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Location $location ciudad donde se encuentra
     */
    private $location;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVV $aavv entidad agencia de viaje
     */
    private $aavv;

    /**
     * @var \DateTime $createdAt fecha de creacion
     */
    private $createdAt;

    /**
     * @var integer $createdBy creado por X usuario
     */
    private $createdBy;

    /**
     * @var \DateTime $updatedAt actualizado a  X hora
     */
    private $updatedAt;

    /**
     * @var integer $updatedBy actualizado por X usuario
     */
    private $updatedBy;

    public function __construct($type = 0 ,$location = 1)
    {
        if ($location instanceof Location)
            $this->location = $location;
        $this->type_address = $type;
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
     * Set address
     *
     * @param string $address
     * @return AAVVAddress
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
     * Set bank_account
     *
     * @param string $bankAccount
     * @return AAVVAddress
     */
    public function setBankAccount($bankAccount)
    {
        $this->bank_account = $bankAccount;

        return $this;
    }

    /**
     * Get bank_account
     *
     * @return string
     */
    public function getBankAccount()
    {
        return $this->bank_account;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AAVVAddress
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return AAVVAddress
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set swift
     *
     * @param string $swift
     * @return AAVVAddress
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
     * Set zip_code
     *
     * @param integer $zipCode
     * @return AAVVAddress
     */
    public function setZipCode($zipCode)
    {
        $this->zip_code = $zipCode;

        return $this;
    }

    /**
     * Get zip_code
     *
     * @return integer
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return AAVVAddress
     */
    public function setLocation(\Navicu\Core\Domain\Model\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Navicu\Core\Domain\Model\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVAddress
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
     * Set type_address
     *
     * @param integer $typeAddress
     * @return AAVVAddress
     */
    public function setTypeAddress($typeAddress)
    {
        $this->type_address = $typeAddress;

        return $this;
    }

    /**
     * Get type_address
     *
     * @return integer
     */
    public function getTypeAddress()
    {
        return $this->type_address;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVAddress
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
     * @return AAVVAddress
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
     * @return AAVVAddress
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
     * @return AAVVAddress
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
     * Funcion para devolver el objeto en forma de array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'aavv' => $this->aavv,
            'address' => $this->address,
            'bank_account' => $this->bank_account,
            'email' => $this->email,
            'location' => $this->location,
            'phone' => $this->phone,
            'swift' => $this->swift,
            'type_address' => $this->type_address,
            'zip_code' => $this->zip_code
        ];
    }

    /**
     * Actualizacion del objeto Address
     * @param $data
     */
    public function updateObject($data)
    {
        $this->address = !isset($data['address']) ? null: $data['address'];
        $this->aavv = !isset($data['aavv']) ? null : $data['aavv'];
        $this->bank_account = !isset($data['bank_account']) ? null : $data['bank_account'];
        $this->email = !isset($data['email']) ? null : $data['email'];
        $this->location = !isset($data['location']) ? null : $data['location'];
        $this->phone = !isset($data['phone']) ? null : $data['phone'];
        $this->swift = !isset($data['swift']) ? null : $data['swift'];
        $this->type_address = !isset($data['type_address']) ? null : $data['type_address'];
        $this->zip_code = !isset($data['zip_code']) ? null : $data['zip_code'];
    }
}
