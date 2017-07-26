<?php

namespace Navicu\InfrastructureBundle\Entity;

/**
 * RoleTranslation
 */
class RoleTranslation
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\Role
     */
    private $role;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return RoleTranslation
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
     * Set locale
     *
     * @param string $locale
     *
     * @return RoleTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set role
     *
     * @param \Navicu\InfrastructureBundle\Entity\Role $role
     *
     * @return RoleTranslation
     */
    public function setRole(\Navicu\InfrastructureBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Navicu\InfrastructureBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
