<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flight
 */
class Flight
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $number;

    /**
     * @var string
     */
    private $airline_code;

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var \DateTime
     */
    private $departure_time;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\FlightReservation
     */
    private $reservation;

    /**
     * @var boolean
     */
    private $return_flight = false;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $currency;


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
     * Set number
     *
     * @param integer $number
     * @return Flight
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set airline_code
     *
     * @param string $airlineCode
     * @return Flight
     */
    public function setAirlineCode($airlineCode)
    {
        $this->airline_code = $airlineCode;

        return $this;
    }

    /**
     * Get airline_code
     *
     * @return string 
     */
    public function getAirlineCode()
    {
        return $this->airline_code;
    }

    /**
     * Set origin
     *
     * @param string $origin
     * @return Flight
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return Flight
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set departure_time
     *
     * @param \DateTime $departureTime
     * @return Flight
     */
    public function setDepartureTime($departureTime)
    {
        $this->departure_time = $departureTime;

        return $this;
    }

    /**
     * Get departure_time
     *
     * @return \DateTime 
     */
    public function getDepartureTime()
    {
        return $this->departure_time;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Flight
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\FlightReservation $reservation
     * @return Flight
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

    /**
     * Set return_flight
     *
     * @param boolean $returnFlight
     * @return Flight
     */
    public function setReturnFlight($returnFlight)
    {
        $this->return_flight = $returnFlight;

        return $this;
    }

    /**
     * Get return_flight
     *
     * @return boolean 
     */
    public function getReturnFlight()
    {
        return $this->return_flight;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Flight
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Flight
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
