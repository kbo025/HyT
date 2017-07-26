<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

/**
 * Grupo creado por reservas en un rango de fecha
 * AAVVReservationGroup
 */
class AAVVReservationGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * La agencia de viajes a la que pertenece
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * Numero unico en base a una marca de tiempo unix para el codigo de la reserva
     *
     * @var string
     */
    private $public_id;

    /**
     * @var \DateTime
     */
    private $date_check_in;

    /**
     * @var \DateTime
     */
    private $date_check_out;

    /**
     * Conjunto de reservas que conforman este grupo
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reservation;

    /**
     * La factura a la cual este conjunto de reservas esta ligado mensualmente
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVInvoice
     */
    private $aavv_invoice;

    /**
     * Responsable de la realizacion de la reserva
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVProfile
     */
    private $aavv_profile;

    /**
     * Responsable del manejo del monto total por el cual el grupo de la reserva fue realizada
     *
     * @var float
     */
    private $total_amount;

    /**
     * Ubicacion hacia donde fue realizada la reserva
     * 
     * @var \Navicu\Core\Domain\Model\Entity\Location
     */
    private $location;

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


    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $hash_url;

    /**
     * AAVVReservationGroup constructor.
     */
    public function __construct()
    {
        $publicId = new PublicId("dateHex","NAV-");
        $this->public_id = $publicId;
        $this->reservation = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set public_id
     *
     * @param string $publicId
     * @return AAVVReservationGroup
     */
    public function setPublicId($publicId)
    {

    }

    /**
     * Get public_id
     *
     * @return string
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * Set date_check_in
     *
     * @param \DateTime $dateCheckIn
     * @return AAVVReservationGroup
     */
    public function setDateCheckIn($dateCheckIn)
    {
        $this->date_check_in = $dateCheckIn;

        return $this;
    }

    /**
     * Get date_check_in
     *
     * @return \DateTime
     */
    public function getDateCheckIn()
    {
        return $this->date_check_in;
    }

    /**
     * Set date_check_out
     *
     * @param \DateTime $dateCheckOut
     * @return AAVVReservationGroup
     */
    public function setDateCheckOut($dateCheckOut)
    {
        $this->date_check_out = $dateCheckOut;

        return $this;
    }

    /**
     * Get date_check_out
     *
     * @return \DateTime
     */
    public function getDateCheckOut()
    {
        return $this->date_check_out;
    }


    /**
     * Add reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservation
     * @return AAVVReservationGroup
     */
    public function addReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservation)
    {
        $this->reservation[] = $reservation;

        return $this;
    }

    /**
     * Remove reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservation
     */
    public function removeReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservation)
    {
        $this->reservation->removeElement($reservation);
    }

    /**
     * Get reservation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVReservationGroup
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
     * Set aavv_invoice
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoice $aavvInvoice
     * @return AAVVReservationGroup
     */
    public function setAavvInvoice(\Navicu\Core\Domain\Model\Entity\AAVVInvoice $aavvInvoice = null)
    {
        $this->aavv_invoice = $aavvInvoice;

        return $this;
    }

    /**
     * Get aavv_invoice
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVInvoice
     */
    public function getAavvInvoice()
    {
        return $this->aavv_invoice;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVReservationGroup
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
     * @return AAVVReservationGroup
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
     * @return AAVVReservationGroup
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
     * @return AAVVReservationGroup
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
     * Funcion para actualizar los objetos
     * @param $data
     */
    public function updateObject($data)
    {
        if (isset($data['aavv']))
            $this->aavv = $data['aavv'];
    }

    /**
     * Funcion para retornar el objeto aavvReservation en forma de string
     * @param $data
     * @return array
     */
    public function toArray($data)
    {
        return [
            'aavv' => $this->aavv
        ];
    }

    /**
     *  convierte el OV PublicId a su representacion String para el almacenamiento
     */
    public function publicIdToString()
    {
        if ($this->public_id instanceof PublicId) {
            $this->public_id = $this->public_id->toString();
        }
    }

    /**
     * Set aavv_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile
     * @return AAVVReservationGroup
     */
    public function setAavvProfile(\Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile = null)
    {
        $this->aavv_profile = $aavvProfile;

        return $this;
    }

    /**
     * Get aavv_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVProfile
     */
    public function getAavvProfile()
    {
        return $this->aavv_profile;
    }

    /**
     * Set total_amount
     *
     * @param float $totalAmount
     * @return AAVVReservationGroup
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
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return AAVVReservationGroup
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
     * Set status
     *
     * @param integer $status
     * @return AAVVReservationGroup
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
     * Funcion pre-persist para generar el hash_url por reserva
     */
    public function generateHashUrl()
    {
        if(empty($this->hash_url)) {
            if(!empty($this->public_id)) {
                $publicId = $this->public_id;
                if ($publicId instanceof PublicId)
                    $this->hash_url = hash("sha256", $publicId->toString());
                else
                    $this->hash_url = hash("sha256", $publicId);

            }
        }
    }

    /**
     * Set hash_url
     *
     * @param string $hashUrl
     * @return AAVVReservationGroup
     */
    public function setHashUrl($hashUrl)
    {
        $this->hash_url = $hashUrl;

        return $this;
    }

    /**
     * Get hash_url
     *
     * @return string 
     */
    public function getHashUrl()
    {
        return $this->hash_url;
    }
}
