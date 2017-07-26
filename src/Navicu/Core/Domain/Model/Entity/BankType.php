<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankType
 */
class BankType
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * 1: Banco Nacional
     * 2: Banco Internacional
     * @var integer
     */
    private $location_zone = 1;

    /**
     * indica si navico trabaja con este banco 
     *
     * @var boolean
     */
    private $receiver;


    /**
     * Set string
     *
     * @param string $id
     * @return BankType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return BankType
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
     * Set location_zone
     *
     * @param integer $locationZone
     * @return BankType
     */
    public function setLocationZone($locationZone)
    {
        $this->location_zone = $locationZone;

        return $this;
    }

    /**
     * Get location_zone
     *
     * @return integer 
     */
    public function getLocationZone()
    {
        return $this->location_zone;
    }

    /**
     * Set receiver
     *
     * @param boolean $receiver
     * @return BankType
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return boolean 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
