<?php

namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase IpCollection.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * la lista de ip reservadas para los paises.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class IpCollection
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ip_start;

    /**
     * @var integer
     */
    private $ip_end;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var Object
     */
    private $location;

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
     * Set ip_start
     *
     * @param integer $ipStart
     * @return IpCollection
     */
    public function setIpStart($ipStart)
    {
        $this->ip_start = $ipStart;

        return $this;
    }

    /**
     * Get ip_start
     *
     * @return integer 
     */
    public function getIpStart()
    {
        return $this->ip_start;
    }

    /**
     * Set ip_end
     *
     * @param integer $ipEnd
     * @return IpCollection
     */
    public function setIpEnd($ipEnd)
    {
        $this->ip_end = $ipEnd;

        return $this;
    }

    /**
     * Get ip_end
     *
     * @return integer 
     */
    public function getIpEnd()
    {
        return $this->ip_end;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return IpCollection
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
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return IpCollection
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
