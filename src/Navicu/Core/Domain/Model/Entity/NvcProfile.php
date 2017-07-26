<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * NvcProfile
 *
 * La entidad contiene el conjunto de datos del perfil administrador
 */
class NvcProfile extends EntityBase
{
    /**
     * Identificador de la clase
     * @var integer
     */
    protected $id;

    /**
     * Nombre y Apellido del usuario
     * @var string
     */
    protected $full_name;

    /**
     * Cedula de identidad del usuario
     * @var string
     */
    protected $identity_card;

    /**
     * Genero del usuario
     * 1: mujer , 2: hombre
     * @var integer
     */
    protected $gender;

    /**
     * Email corporativo del usuario
     * @var string
     */
    protected $company_email;

    /**
     * Telefono celular
     * @var string
     */
    protected $cell_phone;

    /**
     * Fecha de nacimiento
     * @var \DateTime
     */
    protected $birth_date;

    /**
     * Email personal del usuario
     * @var string
     */
    protected $personal_email;

    /**
     * Telefono personal del usuario
     * @var string
     */
    protected $personal_phone;

    /**
     * Fecha de incorporación del usuario
     * @var \DateTime
     */
    protected $incorporation_date;

    /**
     * Representa el conjunto de permisos que tiene el usuario en admin
     * @var array
     */
    protected $permissions;

    /**
     * El perfil de usuario asociado al NvcProfile
     *
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    protected $user;

    /**
     * Los establecimientos asociados al usuario
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $properties;

    /**
     * Los establecimiento en proceso de registro asociados al usuario
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $temp_owners;

    /**
     * Las localidades asociados al usuario
     *
     * @var
     */
    protected $location;

    /**
     * Trato hacia el usuario (sr, sra, srta)
     * @var integer
     */
    protected $treatment;

    /**
     * Representa el cargo y departamento en cual se encuentra el usuario
     *
     * @var \Navicu\Core\Domain\Model\Entity\Departament
     */
    protected $departament;

    /**
     * Representa la fecha de creación del usuario
     *
     * @var \DateTime
     */
    protected $joined_date;

    /**
     * Telefono corporativo
     *
     * @var string
     */
    protected $corporate_phone;

    /**
     * Establecimientos que este usuario afilio a navicu
     * @var \Doctrine\Common\Collections\Collection
     */
    private $properties_recruit;

    /**
     * Establecimientos temporales que éste usuario afilio a navicu
     * @var \Doctrine\Common\Collections\Collection
     */
    private $temp_owner_recruit;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->permissions = [];
        $this->joined_date = new \DateTime();
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
     * @return NvcProfile
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
     * @return NvcProfile
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
     * Set gender
     *
     * @param integer $gender
     * @return NvcProfile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set company_email
     *
     * @param string $companyEmail
     * @return NvcProfile
     */
    public function setCompanyEmail($companyEmail)
    {
        $this->company_email = $companyEmail;

        return $this;
    }

    /**
     * Get company_email
     *
     * @return string
     */
    public function getCompanyEmail()
    {
        return $this->company_email;
    }

    /**
     * Set cell_phone
     *
     * @param string $cellPhone
     * @return NvcProfile
     */
    public function setCellPhone($cellPhone)
    {
        $this->cell_phone = $cellPhone;

        return $this;
    }

    /**
     * Get cell_phone
     *
     * @return string
     */
    public function getCellPhone()
    {
        return $this->cell_phone;
    }

    /**
     * Set birth_date
     *
     * @param \DateTime $birthDate
     * @return NvcProfile
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;

        return $this;
    }

    /**
     * Get birth_date
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set personal_email
     *
     * @param string $personalEmail
     * @return NvcProfile
     */
    public function setPersonalEmail($personalEmail)
    {
        $this->personal_email = $personalEmail;

        return $this;
    }

    /**
     * Get personal_email
     *
     * @return string
     */
    public function getPersonalEmail()
    {
        return $this->personal_email;
    }

    /**
     * Set personal_phone
     *
     * @param string $personalPhone
     * @return NvcProfile
     */
    public function setPersonalPhone($personalPhone)
    {
        $this->personal_phone = $personalPhone;

        return $this;
    }

    /**
     * Get personal_phone
     *
     * @return string
     */
    public function getPersonalPhone()
    {
        return $this->personal_phone;
    }


    /**
     * Set incorporation_date
     *
     * @param \DateTime $incorporationDate
     * @return NvcProfile
     */
    public function setIncorporationDate($incorporationDate)
    {
        $this->incorporation_date = $incorporationDate;

        return $this;
    }

    /**
     * Get incorporation_date
     *
     * @return \DateTime
     */
    public function getIncorporationDate()
    {
        return $this->incorporation_date;
    }

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return NvcProfile
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
     * @return mixed
     */
    public function getLocalPhone()
    {
        return $this->local_phone;
    }

    /**
     * @param mixed $local_phone
     */
    public function setLocalPhone($local_phone)
    {
        $this->local_phone = $local_phone;
    }

    /**
     * @return mixed
     */
    public function getTreatment()
    {
        return $this->treatment;
    }

    /**
     * @param mixed $treatment
     */
    public function setTreatment($treatment)
    {
        $this->treatment = $treatment;
    }

    /**
     * @param $adminProfile
     */
    public  function updateObject($adminProfile){

        if(isset($adminProfile['fullName']))
            $this->setFullName($adminProfile['fullName']);
        if(isset($adminProfile['identityCard']))
            $this->setIdentityCard($adminProfile['identityCard']);
        if(isset($adminProfile['gender']))
            $this->setGender($adminProfile['gender']);
        if(isset($adminProfile['companyEmail']))
           $this->setCompanyEmail($adminProfile['companyEmail']);
        if(isset($adminProfile['personalEmail']))
            $this->setPersonalEmail($adminProfile['personalEmail']);
        if(isset($adminProfile['cellPhone']))
            $this->setCellPhone($adminProfile['cellPhone']);
        if(isset($adminProfile['treatment']))
            $this->setTreatment($adminProfile['treatment']);
        if(isset($adminProfile['birthDate']))
            $this->setBirthDate(new \DateTime($adminProfile ['birthDate']));
        if(isset($adminProfile['personalPhone']))
            $this->setPersonalPhone($adminProfile['personalPhone']);
        if(isset($adminProfile['corporatePhone']))
            $this->setCorporatePhone($adminProfile['corporatePhone']);
        if(isset($adminProfile['incorporationDate']))
            $this->setIncorporationDate(new \DateTime($adminProfile['incorporationDate']));
        return $this;
    }

    /**
     * Set permissions
     *
     * @param array $permissions
     * @return NvcProfile
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * La función verifica actualiza los permisos del usuario
     * en el modulo del administrador
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $module
     * @param null $permission
     * @return $this
     */
    public function updatePermissions($module, $permission = null)
    {
        $flag = false;
        foreach ($this->permissions as &$currentModule) {
            if (isset($currentModule['module']) and $currentModule['module'] == $module) {
                if (is_null($permission) and empty($currentModule['permissions'])) {
                    $this->permissions[] = ['module' => $module, 'permissions' => []];

                } else if (!in_array($permission,$currentModule['permissions'])) {
                    $currentModule['permissions'][] = $permission;

                }
                $flag = true;
                break;
            }
        }

        if (!$flag)
            $this->permissions[] = $permission ?
                ['module' => $module, 'permissions' => [$permission]] :
                ['module' => $module, 'permissions' => []];

        return $this;
    }

    /**
     * Verifica si el usuario tiene permisos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $module
     * @param null $permission
     * @return bool
     */
    public function havePermissons($module, $permission = null)
    {
        $response = false;

        foreach ($this->permissions as $currentModule) {
            if (isset($currentModule['module']) and $currentModule['module'] == $module) {
                if (is_null($permission)) {
                    $response = true;
                    break;
                } else {
                    if (in_array($permission,$currentModule['permissions'])) {
                        $response = true;
                        break;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Asignanción por defecto de los permisos por rol
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 14/06/2016
     */
    public function setDefaultsPermissions($role = null)
    {
        $role = $this->getUser()->getRoles()[0];

        $this->permissions = CoreSession::setDefaultsPermissions($role);
    }


    /**
     * Add properties
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $properties
     * @return NvcProfile
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add temp_owners
     *
     * @param \Navicu\Core\Domain\Model\Entity\TempOwner $tempOwners
     * @return NvcProfile
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

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Add location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return NvcProfile
     */
    public function addLocation(\Navicu\Core\Domain\Model\Entity\Location $location)
    {
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     */
    public function removeLocation(\Navicu\Core\Domain\Model\Entity\Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
     * Set departament
     *
     * @param \Navicu\Core\Domain\Model\Entity\Departament $departament
     * @return NvcProfile
     */
    public function setDepartament(\Navicu\Core\Domain\Model\Entity\Departament $departament = null)
    {
        $this->departament = $departament;

        return $this;
    }

    /**
     * Get departament
     *
     * @return \Navicu\Core\Domain\Model\Entity\Departament
     */
    public function getDepartament()
    {
        return $this->departament;
    }

    /**
     * Set joined_date
     *
     * @param \DateTime $joinedDate
     * @return NvcProfile
     */
    public function setJoinedDate($joinedDate)
    {
        $this->joined_date = $joinedDate;

        return $this;
    }

    /**
     * Get joined_date
     *
     * @return \DateTime
     */
    public function getJoinedDate()
    {
        return $this->joined_date;
    }

    /**
     * Set corporate_phone
     *
     * @param string $corporatePhone
     * @return NvcProfile
     */
    public function setCorporatePhone($corporatePhone)
    {
        $this->corporate_phone = $corporatePhone;

        return $this;
    }

    /**
     * Get corporate_phone
     *
     * @return string
     */
    public function getCorporatePhone()
    {
        return $this->corporate_phone;
    }

    /**
     * Función ejecutada antes de persistir
     * esta entidad para validar el tipo de dato
     * de la fecha a un \DateTime
     */
    public function updateDate()
    {
        if ($this->birth_date && gettype($this->birth_date) == 'string')
            $this->birth_date = new \DateTime($this->birth_date);
    }

    /**
     * Add properties_recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $propertiesRecruit
     * @return NvcProfile
     */
    public function addPropertiesRecruit(\Navicu\Core\Domain\Model\Entity\Property $propertiesRecruit)
    {
        $this->properties_recruit[] = $propertiesRecruit;

        return $this;
    }

    /**
     * Remove properties_recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $propertiesRecruit
     */
    public function removePropertiesRecruit(\Navicu\Core\Domain\Model\Entity\Property $propertiesRecruit)
    {
        $this->properties_recruit->removeElement($propertiesRecruit);
    }

    /**
     * Get properties_recruit
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertiesRecruit()
    {
        return $this->properties_recruit;
    }

    /**
     * Add temp_owner_recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\TempOwner $tempOwnerRecruit
     * @return NvcProfile
     */
    public function addTempOwnerRecruit(\Navicu\Core\Domain\Model\Entity\TempOwner $tempOwnerRecruit)
    {
        $this->temp_owner_recruit[] = $tempOwnerRecruit;

        return $this;
    }

    /**
     * Remove temp_owner_recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\TempOwner $tempOwnerRecruit
     */
    public function removeTempOwnerRecruit(\Navicu\Core\Domain\Model\Entity\TempOwner $tempOwnerRecruit)
    {
        $this->temp_owner_recruit->removeElement($tempOwnerRecruit);
    }

    /**
     * Get temp_owner_recruit
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTempOwnerRecruit()
    {
        return $this->temp_owner_recruit;
    }
}
