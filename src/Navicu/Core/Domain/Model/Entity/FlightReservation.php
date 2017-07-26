<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

/**
 * FlightReservation
 */
class FlightReservation
{
    /**
     * @var integer
     */
    private $id;

	/**
	 * @var string
	 */
	private $public_id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var \DateTime
     */
    private $reservation_date;

    /**
     * @var integer
     */
    private $state;

	/**
	 * @var float
	 */
	private $total_to_pay;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $passengers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Flights;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $payments;

    /**
     * @var integer
     */
    private $child_number;

    /**
     * @var integer
     */
    private $adult_number;

    /**
     * @var float
     */
    private $tax;

    /**
     * @var boolean
     */
     private $round;

    /**
     * informacion cambiaria para moneda extranjera
     *
     * @var array
     */
    private $currency_convertion_information;

    /**
     * @var string
     */
    private $currency;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->passengers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Flights = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
	    $publicId = new PublicId('dateHex');
	    $this->public_id = $publicId->toString();
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
     * Set code
     *
     * @param string $code
     * @return FlightReservation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set reservation_date
     *
     * @param \DateTime $reservationDate
     * @return FlightReservation
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
     * Set state
     *
     * @param integer $state
     * @return FlightReservation
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add passengers
     *
     * @param \Navicu\Core\Domain\Model\Entity\Passenger $passengers
     * @return FlightReservation
     */
    public function addPassenger(\Navicu\Core\Domain\Model\Entity\Passenger $passengers)
    {
        $this->passengers[] = $passengers;

	    $passengers->setReservation($this);

        return $this;
    }

    /**
     * Remove passengers
     *
     * @param \Navicu\Core\Domain\Model\Entity\Passenger $passengers
     */
    public function removePassenger(\Navicu\Core\Domain\Model\Entity\Passenger $passengers)
    {
        $this->passengers->removeElement($passengers);
    }

    /**
     * Get passengers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPassengers()
    {
        return $this->passengers;
    }

    /**
     * Add Flights
     *
     * @param \Navicu\Core\Domain\Model\Entity\Flight $flights
     * @return FlightReservation
     */
    public function addFlight(\Navicu\Core\Domain\Model\Entity\Flight $flights)
    {
        $this->Flights[] = $flights;

	    $flights->setReservation($this);

        return $this;
    }

    /**
     * Remove Flights
     *
     * @param \Navicu\Core\Domain\Model\Entity\Flight $flights
     */
    public function removeFlight(\Navicu\Core\Domain\Model\Entity\Flight $flights)
    {
        $this->Flights->removeElement($flights);
    }

    /**
     * Get Flights
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFlights()
    {
        return $this->Flights;
    }

    /**
     * Add payments
     *
     * @param \Navicu\Core\Domain\Model\Entity\FlightPayment $payments
     * @return FlightReservation
     */
    public function addPayment(\Navicu\Core\Domain\Model\Entity\FlightPayment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \Navicu\Core\Domain\Model\Entity\FlightPayment $payments
     */
    public function removePayment(\Navicu\Core\Domain\Model\Entity\FlightPayment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set public_id
     *
     * @param string $publicId
     * @return FlightReservation
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
     * Set child_number
     *
     * @param integer $childNumber
     * @return FlightReservation
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
     * @return FlightReservation
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
     * Set tax
     *
     * @param float $tax
     * @return FlightReservation
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
     * Set total_to_pay
     *
     * @param float $totalToPay
     * @return Reservation
     */
    public function setTotalToPay($totalToPay)
    {
        $this->total_to_pay = round($totalToPay,4);

        return $this;
    }

    /**
     * Get total_to_pay
     *
     * @return float 
     */
    public function getTotalToPay( $whitTax = true )
    {
        $aux = $whitTax ? 1 : 1 + (isset($this->tax) ? $this->tax : 0);
        if($this->round)
            return round($this->total_to_pay / $aux);
        else
            return $this->total_to_pay / $aux;

    }

    /**
     * Get tax_pay
     *
     * @return float 
     */
    public function getTaxPay()
    {
        $response = $this->total_to_pay - $this->getTotalToPay(false);
        if($this->round)
            return round($response);
        else
            return $response;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return FlightReservation
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
