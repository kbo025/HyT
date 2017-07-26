<?php
namespace Navicu\InfrastructureBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**

 * 
 * @author Alejandro Conde <adcs2008@gmail.com>
 */
class ModuleAccess 
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

	private $permissions;

    /**
     * Esta propiedad es usada para interactuar con el nodo padre de la lista.
     * 
     * @var ModuleAccess Type Object
     */
    protected $parent;

    /**
     * @var ArrayCollection 
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;


	public function __construct($module = '')
	{
		if (0 !== strlen($module)) {
            $this->name = strtoupper($module);
        }
        $this->roles = new ArrayCollection();

        $this->permissions = new ArrayCollection();

        $this->children = new ArrayCollection();
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
    public function setParent($module = null)
    {
        $this->parent = $module;
    }
    public function getParent()
    {
        return $this->parent;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function addRole($role)
    {
        $this->roles->add($role);
        
    }
    public function removeRole($role)
    {
        $this->roles->removeElement($role);
    }

    public function addPermission($perm)
    {
    	$this->permissions->add($perm);
        $perm->setModule($this);
    }

    public function removePermission($perm)
    {
    	$this->permissions->removeElement($perm);
    }

    public function addChild($module){
        $this->children->add($module);
        $module->setParent($this);
    }
    public function removeChild($module)
    {
        $this->children->removeElement($module);
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ModuleAccess
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
     * @return ModuleAccess
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
     * @return ModuleAccess
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
     * @return ModuleAccess
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
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * Add translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\ModuleTranslation $translation
     *
     * @return ModuleAccess
     */
    public function addTranslation(\Navicu\InfrastructureBundle\Entity\ModuleTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\ModuleTranslation $translation
     */
    public function removeTranslation(\Navicu\InfrastructureBundle\Entity\ModuleTranslation $translation)
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
