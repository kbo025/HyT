<?php

namespace Navicu\InfrastructureBundle\Entity;

/**
 * PermissionTranslation
 */
class PermissionTranslation
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
    private $locale;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\Permission
     */
    private $permission;


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
     *
     * @return PermissionTranslation
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
     * @return PermissionTranslation
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
     * Set permission
     *
     * @param \Navicu\InfrastructureBundle\Entity\Permission $permission
     *
     * @return PermissionTranslation
     */
    public function setPermission(\Navicu\InfrastructureBundle\Entity\Permission $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \Navicu\InfrastructureBundle\Entity\Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
