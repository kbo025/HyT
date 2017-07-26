<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
 * Airport
 */
class Airport
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
    private $slug;

    /**
     * @var integer
     */
    private $type = 0;

    /**
     * @var string
     */
    private $iata;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Location
     */
    private $location;

	/**
	 * @var string
	 */
	private $lat;

	/**
	 * @var string
	 */
	private $lon;


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
     * @return Airport
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
     * Set slug
     *
     * @param string $slug
     * @return Airport
     */
    public function setSlug()
    {
        $this->slug = Slug::generateSlug(str_replace('-',' ',$this->name));
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Airport
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set iata
     *
     * @param string $iata
     * @return Airport
     */
    public function setIata($iata)
    {
        $this->iata = $iata;

        return $this;
    }

    /**
     * Get iata
     *
     * @return string 
     */
    public function getIata()
    {
        return $this->iata;
    }

    /**
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return Airport
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
     * Set lat
     *
     * @param string $lat
     * @return Airport
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon
     * @return Airport
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return string 
     */
    public function getLon()
    {
        return $this->lon;
    }
}
