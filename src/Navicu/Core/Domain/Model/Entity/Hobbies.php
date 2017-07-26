<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Hobbies
 */
class Hobbies
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var ArrayCollection
     */
    private $clients;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Hobbies
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
     * Add clients
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clients
     * @return Hobbies
     */
    public function addClient(\Navicu\Core\Domain\Model\Entity\ClientProfile $clients)
    {
        $this->clients[] = $clients;

        return $this;
    }

    /**
     * Remove clients
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clients
     */
    public function removeClient(\Navicu\Core\Domain\Model\Entity\ClientProfile $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return ArrayCollection
     */
    public function getClients()
    {
        return $this->clients;
    }
}
