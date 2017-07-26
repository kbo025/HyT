<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

/**
 * DeniedReservation
 */
class DeniedReservation
{
    /**
     * identificador de bd
     *
     * @var integer
     */
    private $id;

    /**
     * id publico asignado a la reserva
     *
     * @var string
     */
    private $public_id;

    /**
     * fecha de checkin de la reserva
     *
     * @var \DateTime
     */
    private $date_check_in;

    /**
     * fecha de checkout de la reserva
     *
     * @var \DateTime
     */
    private $date_check_out;

    /**
     * numero de niños en la reserva
     *
     * @var integer
     */
    private $child_number;

    /**
     * numero de adultos en la rerserva
     *
     * @var integer
     */
    private $adult_number;

    /**
     * requerimientos especiales del cliente
     *
     * @var string
     */
    private $special_request;

    /**
     * monto total de la reserva
     *
     * @var float
     */
    private $total_to_pay;

    /**
     * taza de descuento del hotel al momento de la reserva
     *
     * @var float
     */
    private $discount_rate = 0;

    /**
     * monto del impuesto de la reserva
     *
     * @var float
     */
    private $tax = 0;

    /**
     * fecha de intento de reserva
     *
     * @var \DateTime
     */
    private $reservation_date;

    /**
     * requerimientos especiales
     *
     * @var array
     */
    private $guest;

    /**
     * estado de la reserva al ser denegada
     *
     * @var integer
     */
    private $status = 0;

    /**
     * tipo de pago (1: TDC 2: Transferencia)
     *
     * @var integer
     */
    private $payment_type;

    /**
     * informacion de los pack de reservacion
     *
     * @var array
     */
    private $reservation_packages;

    /**
     * informacion de los pagos o de los intentos de pago
     *
     * @var array
     */
    private $payments;

    /**
     * relacion con el cliente que reservaria
     *
     * @var \Navicu\Core\Domain\Model\Entity\ClientProfile
     */
    private $client_id;

    /**
     * relacion con el establecimiento donde se reservaria
     *
     * @var \Navicu\Core\Domain\Model\Entity\Property
     */
    private $property_id;

    /**
     * motivo de la reserva denegada
     *
     * @var string
     */
    private $description;

    public function __construct(Reservation $reservation) {
        $this->public_id = $reservation->getPublicId() instanceof PublicId ?
            $reservation->getPublicId()->toString() :
            $reservation->getPublicId();
        $this->date_check_in = $reservation->getDateCheckIn();
        $this->date_check_out = $reservation->getDateCheckOut();
        $this->adult_number = $reservation->getAdultNumber();
        $this->status = $reservation->getStatus();
        $this->payment_type = $reservation->getPaymentType();
        $this->child_number = $reservation->getChildNumber();
        $this->reservation_date = $reservation->getReservationDate();
        $this->special_request = $reservation->getSpecialRequest();
        $this->total_to_pay = $reservation->getTotalToPay();
        $this->discount_rate = $reservation->getDiscountRate();
        $this->tax = $reservation->getTaxPay();
        $this->guest = $reservation->getGuest();
        $this->client_id = $reservation->getClientId();
        $this->property_id = $reservation->getPropertyId();
        $this->reservation_packages = [];
        foreach ( $reservation->getReservationPackages() as $rp ) {
            $this->reservation_packages[] = $rp->toArray();
        }

        $this->payments = [];
        foreach ( $reservation->getPayments() as $payment ) {
            $this->payments[] = $payment->toArray();
        }
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
     * @return DeniedReservation
     */
    public function setPublicId($publicId)
    {
        $this->public_id = $publicId;

        return $this;
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
     * @return DeniedReservation
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
     * @return DeniedReservation
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
     * Set child_number
     *
     * @param integer $childNumber
     * @return DeniedReservation
     */
    public function setChildNumber($childNumber)
    {
        $this->child_number = $childNumber;

        return $this;
    }

    /**
     * Get child_number
     *
     * @return integer 
     */
    public function getChildNumber()
    {
        return $this->child_number;
    }

    /**
     * Set adult_number
     *
     * @param integer $adultNumber
     * @return DeniedReservation
     */
    public function setAdultNumber($adultNumber)
    {
        $this->adult_number = $adultNumber;

        return $this;
    }

    /**
     * Get adult_number
     *
     * @return integer 
     */
    public function getAdultNumber()
    {
        return $this->adult_number;
    }

    /**
     * Set special_request
     *
     * @param string $specialRequest
     * @return DeniedReservation
     */
    public function setSpecialRequest($specialRequest)
    {
        $this->special_request = $specialRequest;

        return $this;
    }

    /**
     * Get special_request
     *
     * @return string 
     */
    public function getSpecialRequest()
    {
        return $this->special_request;
    }

    /**
     * Set total_to_pay
     *
     * @param float $totalToPay
     * @return DeniedReservation
     */
    public function setTotalToPay($totalToPay)
    {
        $this->total_to_pay = $totalToPay;

        return $this;
    }

    /**
     * Get total_to_pay
     *
     * @return float 
     */
    public function getTotalToPay()
    {
        return $this->total_to_pay;
    }

    /**
     * Set discount_rate
     *
     * @param float $discountRate
     * @return DeniedReservation
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

    /**
     * Set tax
     *
     * @param float $tax
     * @return DeniedReservation
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
     * Set reservation_date
     *
     * @param \DateTime $reservationDate
     * @return DeniedReservation
     */
    public function setReservationDate($reservationDate)
    {
        $this->reservation_date = $reservationDate;

        return $this;
    }

    /**
     * Get reservation_date
     *
     * @return \DateTime 
     */
    public function getReservationDate()
    {
        return $this->reservation_date;
    }

    /**
     * Set guest
     *
     * @param array $guest
     * @return DeniedReservation
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * Get guest
     *
     * @return array 
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return DeniedReservation
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
     * Set payment_type
     *
     * @param integer $paymentType
     * @return DeniedReservation
     */
    public function setPaymentType($paymentType)
    {
        $this->payment_type = $paymentType;

        return $this;
    }

    /**
     * Get payment_type
     *
     * @return integer 
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * Set reservation_packages
     *
     * @param array $reservationPackages
     * @return DeniedReservation
     */
    public function setReservationPackages($reservationPackages)
    {
        $this->reservation_packages = $reservationPackages;

        return $this;
    }

    /**
     * Get reservation_packages
     *
     * @return array 
     */
    public function getReservationPackages()
    {
        return $this->reservation_packages;
    }

    /**
     * Set payments
     *
     * @param array $payments
     * @return DeniedReservation
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;

        return $this;
    }

    /**
     * Get payments
     *
     * @return array 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set client_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clientId
     * @return DeniedReservation
     */
    public function setClientId(\Navicu\Core\Domain\Model\Entity\ClientProfile $clientId = null)
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\ClientProfile 
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set property_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $propertyId
     * @return DeniedReservation
     */
    public function setPropertyId(\Navicu\Core\Domain\Model\Entity\Property $propertyId = null)
    {
        $this->property_id = $propertyId;

        return $this;
    }

    /**
     * Get property_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\Property 
     */
    public function getPropertyId()
    {
        return $this->property_id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return DeniedReservation
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
}
