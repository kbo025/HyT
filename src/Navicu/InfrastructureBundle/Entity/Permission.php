<?php
namespace Navicu\InfrastructureBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**

 * 
 * @author Alejandro Conde <adcs2008@gmail.com>
 */
class Permission
{
	private $id;

    /**
     * @var date
     */
    private $createdAt;
    /**
     * @var integer
     */
    private $createdBy;
    /**
     * @var date
     */
    private $updatedAt;
    /**
     * @var integer
     */
    private $updatedBy;

	private $name;

	private $roles;

	private $module;


	public function __construct($perm = '')
	{
		if (0 !== strlen($perm)) {
            $this->name = strtoupper($perm);
        }
        $this->roles = new ArrayCollection();

        $this->module = null;
	}

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getModule()
    {
        return $this->module;
    }
    public function setModule($module)
    {
        $this->module = $module;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function addRole($role)
    {
        $this->roles->add($role);
        
    }
    public function removeRole($role)
    {
        $this->roles->removeElement($role);
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Permission
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Permission
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Permission
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param integer $updatedBy
     * @return Permission
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;


    /**
     * Add translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\PermissionTranslation $translation
     *
     * @return Permission
     */
    public function addTranslation(\Navicu\InfrastructureBundle\Entity\PermissionTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\PermissionTranslation $translation
     */
    public function removeTranslation(\Navicu\InfrastructureBundle\Entity\PermissionTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
