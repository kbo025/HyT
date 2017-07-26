<?php

namespace Navicu\InfrastructureBundle\Entity;

/**
 * ModuleTranslation
 */
class ModuleTranslation
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
     * @var \Navicu\InfrastructureBundle\Entity\ModuleAccess
     */
    private $module;


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
     * @return ModuleTranslation
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
     * @return ModuleTranslation
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
     * Set module
     *
     * @param \Navicu\InfrastructureBundle\Entity\ModuleAccess $module
     *
     * @return ModuleTranslation
     */
    public function setModule(\Navicu\InfrastructureBundle\Entity\ModuleAccess $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return \Navicu\InfrastructureBundle\Entity\ModuleAccess
     */
    public function getModule()
    {
        return $this->module;
    }
}
