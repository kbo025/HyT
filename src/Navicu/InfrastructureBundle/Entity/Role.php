<?php
namespace Navicu\InfrastructureBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**

 * 
 * @author Alejandro Conde <adcs2008@gmail.com>
 */
class Role implements RoleInterface
{
    /**

     */
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
    /**
     
     */
    private $name;
    
    /**
   
     */
    private $users;

    private $modules;

    private $permissions;

    private $aavv;

    private $admin;

    public function __construct($role = '', $admin = false, $fromInterface = false)
    {
        if (0 !== strlen($role)) {
            if ($fromInterface) {
                $this->name = 'ROLE_'.strtoupper($role);
            } else {
                $this->name = strtoupper($role);
            }
            $this->userReadableName = $role;
        }

        $this->admin = $admin;
        $this->users = new ArrayCollection();

        $this->modules = new ArrayCollection();

        $this->permissions = new ArrayCollection();
    }


    public function getModulePerms($module)
    {
        $perms = array();

        $allperms = $this->permissions;

        foreach($allperms as $perm) {

            if($perm->getModule()->getName() == $module) {

                $perms[] = $perm->getName();
            }
        }

        return $perms;


    }

    public function hasModuleAccess($modulename)
    {
        $access = false;
        $modules = $this->modules;

        foreach($modules as $module) {

            if($module->getName() == $modulename)
                $access = true;
        }

        return $access;
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->name;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVImage
     */
    public function setAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavv = null)
    {
        $this->aavv = $aavv;

        return $this;
    }

    /**
     * Get aavv
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVV 
     */
    public function getAavv()
    {
        return $this->aavv;
    }
    
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getUsers()
    {
        return $this->users;
    }
    public function getModules()
    {
        return $this->modules;
    }
    public function getPermissions()
    {
        return $this->permissions;
    }
    public function addUser($user, $addRoleToUser = true)
    {
        $this->users->add($user);
        $addRoleToUser && $user->addRole($this, false);
    }
    public function removeUser($user)
    {
        $this->users->removeElement($user);
    }

    public function addModule($module)
    {
        $this->modules->add($module);
        $module->addRole($this);
    }

    public function removeModule($module)
    {
        $this->modules->removeElement($module);
    }

    public function addPermission($perm)
    {
        if (!$this->permissions->contains($perm)) {
            $this->permissions->add($perm);
            $perm->addRole($this);
        }
    }

    public function removePermission($module)
    {
        $this->permissions->removeElement($module);
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Role
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
     * @return Role
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
     * @return Role
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
     * @return Role
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

    public function isAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }
    /**
     * @var string
     */
    private $userReadableName;


    /**
     * Set userReadableName
     *
     * @param string $userReadableName
     *
     * @return Role
     */
    public function setUserReadableName($userReadableName)
    {
        $this->userReadableName = $userReadableName;

        return $this;
    }

    /**
     * Get userReadableName
     *
     * @return string
     */
    public function getUserReadableName()
    {
        return $this->userReadableName;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;


    /**
     * Add translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\RoleTranslation $translation
     *
     * @return Role
     */
    public function addTranslation(\Navicu\InfrastructureBundle\Entity\RoleTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Navicu\InfrastructureBundle\Entity\RoleTranslation $translation
     */
    public function removeTranslation(\Navicu\InfrastructureBundle\Entity\RoleTranslation $translation)
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
