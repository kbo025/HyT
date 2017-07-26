<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Passenger
 */
class Passenger
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $document_type;

    /**
     * @var integer
     */
    private $document_number;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

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
     * Set name
     *
     * @param string $name
     * @return Passenger
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
     * Set lastname
     *
     * @param string $lastname
     * @return Passenger
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set document_type
     *
     * @param string $documentType
     * @return Passenger
     */
    public function setDocumentType($documentType)
    {
        $this->document_type = $documentType;

        return $this;
    }

    /**
     * Get document_type
     *
     * @return string 
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * Set document_number
     *
     * @param integer $documentNumber
     * @return Passenger
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->document_number = $documentNumber;

        return $this;
    }

    /**
     * Get document_number
     *
     * @return integer 
     */
    public function getDocumentNumber()
    {
        return $this->document_number;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Passenger
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
     * Set email
     *
     * @param string $email
     * @return Passenger
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
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\FlightReservation $reservation
     * @return Passenger
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
