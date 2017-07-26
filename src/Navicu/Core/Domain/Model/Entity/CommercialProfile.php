<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\InfrastructureBundle\Entity\User;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * La entidad representa el perfil del ejecutivo de ventas
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/03/2016
 *
 * CommercialProfile
 */
class CommercialProfile
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $full_name;

    /**
     * @var string
     */
    private $identity_card;

    /**
     * @var User
     */
    private $user;

    /**
     * Conjuntos de establecimientos asociados al ejecutivo de venta
     *
     * @var ArrayCollection Property
     */
    private $properties;

    /**
     * Conjunto d establecimientos temporales asociados al ejecutivo de venta
     *
     * @var ArrayCollection Property
     */
    private $temp_owners;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->temp_owners = new ArrayCollection();
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
     * Set full_name
     *
     * @param string $fullName
     * @return CommercialProfile
     */
    public function setFullName($fullName)
    {
        $this->full_name = $fullName;

        return $this;
    }

    /**
     * Get full_name
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Set identity_card
     *
     * @param string $identityCard
     * @return CommercialProfile
     */
    public function setIdentityCard($identityCard)
    {
        $this->identity_card = $identityCard;

        return $this;
    }

    /**
     * Get identity_card
     *
     * @return string 
     */
    public function getIdentityCard()
    {
        return $this->identity_card;
    }

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return CommercialProfile
     */
    public function setUser(\Navicu\InfrastructureBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add properties
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $properties
     *
     * @return CommercialProfile
     */
    public function addProperty(\Navicu\Core\Domain\Model\Entity\Property $properties)
    {
        $this->properties[] = $properties;

        return $this;
    }

    /**
     * Remove properties
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $properties
     */
    public function removeProperty(\Navicu\Core\Domain\Model\Entity\Property $properties)
    {
        $this->properties->removeElement($properties);
    }

    /**
     * Get properties
     *
     * @return ArrayCollection
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add temp_owners
     *
     * @param \Navicu\Core\Domain\Model\Entity\TempOwner $tempOwners
     * @return CommercialProfile
     */
    public function addTempOwner(\Navicu\Core\Domain\Model\Entity\TempOwner $tempOwners)
    {
        $this->temp_owners[] = $tempOwners;

        return $this;
    }

    /**
     * Remove temp_owners
     *
     * @param \Navicu\Core\Domain\Model\Entity\TempOwner $tempOwners
     */
    public function removeTempOwner(\Navicu\Core\Domain\Model\Entity\TempOwner $tempOwners)
    {
        $this->temp_owners->removeElement($tempOwners);
    }

    /**
     * Get temp_owners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTempOwners()
    {
        return $this->temp_owners;
    }
}
