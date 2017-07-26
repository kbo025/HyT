<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Clase DestinationsType
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del listado
 * de destinos habilitados por los estableicmientos dentro de la BD.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DestinationsType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Nombre del tipo de destino.
     *
     * @var string
     */
    private $title;

    /**
     * @var ArrayCollection
     */
    private $locations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return DestinationsType
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
     * Add locations
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $locations
     * @return DestinationsType
     */
    public function addLocation(\Navicu\Core\Domain\Model\Entity\Location $locations)
    {
        $this->locations[] = $locations;

        return $this;
    }

    /**
     * Remove locations
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $locations
     */
    public function removeLocation(\Navicu\Core\Domain\Model\Entity\Location $locations)
    {
        $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocations()
    {
        return $this->locations;
    }
}
