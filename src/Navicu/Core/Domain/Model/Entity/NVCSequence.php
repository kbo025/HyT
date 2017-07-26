<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NVCSequence
 */
class NVCSequence
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
    private $prefix;

    /**
     * @var string
     */
    private $sufix;

    /**
     * @var integer
     */
    private $currentnext;


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
     * @return NVCSequence
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
     * Set prefix
     *
     * @param string $prefix
     * @return NVCSequence
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set sufix
     *
     * @param string $sufix
     * @return NVCSequence
     */
    public function setSufix($sufix)
    {
        $this->sufix = $sufix;

        return $this;
    }

    /**
     * Get sufix
     *
     * @return string 
     */
    public function getSufix()
    {
        return $this->sufix;
    }

    /**
     * Set currentnext
     *
     * @param integer $currentnext
     * @return NVCSequence
     */
    public function setCurrentnext($currentnext)
    {
        $this->currentnext = $currentnext;

        return $this;
    }

    /**
     * Get currentnext
     *
     * @return integer 
     */
    public function getCurrentnext()
    {
        return $this->currentnext;
    }
}
