<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Departament
 * Representa el conjunto de departamentos dentro de la empresa
 * que pueden estar asociado un usuario NvcProfile
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 */
class Departament
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Código que representa el tipo de departamento
     * @var string
     */
    private $code;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $nvc_profile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nvc_profile = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Departament
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add nvc_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfle $nvcProfile
     * @return Departament
     */
    public function addNvcProfile(\Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile)
    {
        $this->nvc_profile[] = $nvcProfile;

        return $this;
    }

    /**
     * Remove nvc_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfle $nvcProfile
     */
    public function removeNvcProfile(\Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile)
    {
        $this->nvc_profile->removeElement($nvcProfile);
    }

    /**
     * Get nvc_profile
     *
     * @return ArrayCollection
     */
    public function getNvcProfile()
    {
        return $this->nvc_profile;
    }
    /**
     * @var integer
     */
    private $role;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Departament
     */
    private $parent;


    /**
     * Set role
     *
     * @param integer $role
     * @return Departament
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add children
     *
     * @param \Navicu\Core\Domain\Model\Entity\Departament $children
     * @return Departament
     */
    public function addChild(\Navicu\Core\Domain\Model\Entity\Departament $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Navicu\Core\Domain\Model\Entity\Departament $children
     */
    public function removeChild(\Navicu\Core\Domain\Model\Entity\Departament $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Navicu\Core\Domain\Model\Entity\Departament $parent
     * @return Departament
     */
    public function setParent(\Navicu\Core\Domain\Model\Entity\Departament $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Navicu\Core\Domain\Model\Entity\Departament 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
