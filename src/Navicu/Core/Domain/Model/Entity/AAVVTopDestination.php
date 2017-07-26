<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clase encargada de llevar el registro sobre los destinos mas buscados por la aavv
 *
 * AAVVTopDestination
 */
class AAVVTopDestination
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Numero de visitas a esa localidad
     * @var integer
     */
    private $number_visits;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Location
     */
    private $location;

    public function __construct()
    {
        $this->number_visits = 1;
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
     * Set number_visits
     *
     * @param integer $numberVisits
     * @return AAVVTopDestination
     */
    public function setNumberVisits($numberVisits)
    {
        $this->number_visits = $numberVisits;

        return $this;
    }

    /**
     * Get number_visits
     *
     * @return integer 
     */
    public function getNumberVisits()
    {
        return $this->number_visits;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVTopDestination
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
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return AAVVTopDestination
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
}
