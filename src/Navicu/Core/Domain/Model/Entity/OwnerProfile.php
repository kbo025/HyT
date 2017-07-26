<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * Clase OwnerProfile.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del perfil del propietario.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class OwnerProfile extends EntityBase
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $names;

    /**
     * @var string
     */
    protected $last_names;

    /**
     * Nombre y Apellido del usuario
     * @var string
     */
    protected $full_name;

    /**
     * Representa la fecha de creación del usuario en el sistema
     * @var \DateTime
     */
    protected $joined_date;

    /**
     * Esta propiedad es usada para interactuar con el valor asignado en la lista de Cargo de Oficina.
     *
     * @var Category Type Object
     */
    protected $office;

    /**
     * Esta propiedad es usada para interactuar con los telefonos del Propietario.
     * 
     * @var String
     */
    protected $phones;

    /**
     * Esta propiedad es usada para interactuar con el fax del Propietario.
     * 
     * @var String
     */
    protected $fax;

    /**
     * Esta propiedad es usada para interactuar de forma jerarquica con el Propietario Padre.
     * 
     * @var OwnerProfile Type Object
     */
    protected $parent;

    /**
     * Esta propiedad es usada para interactuar de forma jerarquica con los Propietarios hijos.
     *
     * @var OwnerProfile Type Object
     */
    protected $children;

    /**
     * Esta propiedad es usada para interactuar con la cuenta de usuario asignada por el sistema.
     * 
     * @var User Type Object
     **/
    protected $user;

    /**
     * Esta propiedad es usada para interactuar con el o los establecimiento/s.
     *
     * @var ArrayCollection
     */
    protected $properties;

    /**
     * Esta propiedad es usada para interactuar con el historial del usuario.
     *
     * @var ArrayCollection
     */
    protected $logs_owners;
    /**
     * @var string
     */
    protected $identity_card;

    /**
     * @var \DateTime
     */
    protected $birth_date;

    /**
     * el genero del usuario
     * @var integer
     */
    protected $gender;

    /**
     * La compañias en el que pertenece el usuario
     * @var string
     */
    protected $company;

    /**
     * Los cargos del usuario
     * @var integer
     */
    protected $position;

    /**
     * Télefono del hotelero
     *
     * @var string
     */
    protected $cell_phone;

    /**
     * La relacion que tiene un hotelero con los usuarios
     * @var
     */
    protected $location;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->logs_owners = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->joined_date = new \DateTime();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return OwnerProfile
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set names
     *
     * @param string $names
     * @return OwnerProfile
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set last_names
     *
     * @param string $lastNames
     * @return OwnerProfile
     */
    public function setLastNames($lastNames)
    {
        $this->last_names = $lastNames;

        return $this;
    }

    /**
     * Get last_names
     *
     * @return string 
     */
    public function getLastNames()
    {
        return $this->last_names;
    }

    /**
     * Set phones
     *
     * @param array $phones
     * @return OwnerProfile
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return array 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return OwnerProfile
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return OwnerProfile
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add children
     *
     * @param OwnerProfile $children
     * @return OwnerProfile
     */
    public function addChild(OwnerProfile $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param OwnerProfile $children
     */
    public function removeChild(OwnerProfile $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add logs_owners
     *
     * @param LogsOwner $logsOwners
     * @return OwnerProfile
     */
    public function addLogsOwner(LogsOwner $logsOwners)
    {
        $this->logs_owners[] = $logsOwners;

        return $this;
    }

    /**
     * Remove logs_owners
     *
     * @param LogsOwner $logsOwners
     */
    public function removeLogsOwner(LogsOwner $logsOwners)
    {
        $this->logs_owners->removeElement($logsOwners);
    }

    /**
     * Get logs_owners
     *
     * @return ArrayCollection
     */
    public function getLogsOwners()
    {
        return $this->logs_owners;
    }

    /**
     * Set parent
     *
     * @param OwnerProfile $parent
     * @return OwnerProfile
     */
    public function setParent(OwnerProfile $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return OwnerProfile 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set office
     *
     * @param Category $office
     * @return OwnerProfile
     */
    public function setOffice(Category $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return Category 
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Add properties
     *
     * @param Property $properties
     * @return OwnerProfile
     */
    public function addProperty(Property $properties)
    {
        $this->properties[] = $properties;

        return $this;
    }

    /**
     * Remove properties
     *
     * @param Property $properties
     */
    public function removeProperty(Property $properties)
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
     * Set identity_card
     *
     * @param string $identityCard
     * @return OwnerProfile
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
     * Set birth_date
     *
     * @param \DateTime $birthDate
     * @return OwnerProfile
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
     * Set gender
     *
     * @param integer $gender
     * @return OwnerProfile
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
     * Set company
     *
     * @param string $company
     * @return OwnerProfile
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return OwnerProfile
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return OwnerProfile
     */


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

    /**Esta funcion realiza la actualizacion de los datos al crear usuario hotelero
     *  @author Mary sanchez <msmarycarmen@gmail.com>
     * @param $ownerProfile
     * @return $this
     */
    public  function updateObject($ownerProfile){

        if(isset($ownerProfile['names']))
            $this->setNames($ownerProfile['names']);
        if(isset($ownerProfile['fullName']))
            $this->setFullName($ownerProfile['fullName']);
        if(isset($ownerProfile['identityCard']))
            $this->setIdentityCard($ownerProfile['identityCard']);
        if(isset($ownerProfile['gender']))
            $this->setGender($ownerProfile['gender']);
        if(isset($ownerProfile['company']))
            $this->setCompany($ownerProfile['company']);
        if(isset($ownerProfile['position']))
            $this->setPosition($ownerProfile['position']);
        if(isset($ownerProfile['phones']))
            $this->setPhones($ownerProfile['phones']);
        if(isset($ownerProfile['birthDate']))
            $this->setBirthDate(date_create($ownerProfile['birthDate']));
        if (isset($ownerProfile['treatment']))
            $this->setTreatment($ownerProfile['treatment']);
        if (isset($ownerProfile['emailNews']))
            $this->setEmailNews($ownerProfile['emailNews']);

        if (isset($ownerProfile['phone']))
            $this->setPhones(json_encode([$ownerProfile['phone']]));

        return $this;
    }

    /**
     * Se envia el correo electronico como un string
     * @var string
     * @autor Mary sanchez <msmarycarmen@gmail.com>
     */
    public function getEmailString()
    {
        $user = $this->getUser();
        if ($user->getEmail() instanceof EmailAddress)
            return $user->getEmail()->toString();
    }

    /**
     * Set full_name
     *
     * @param string $fullName
     * @return OwnerProfile
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
     * Set joined_date
     *
     * @param \DateTime $joinedDate
     * @return OwnerProfile
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
     * @var boolean
     */
    protected $email_news = true;


    /**
     * Set email_news
     *
     * @param boolean $emailNews
     * @return OwnerProfile
     */
    public function setEmailNews($emailNews)
    {
        $this->email_news = $emailNews;

        return $this;
    }

    /**
     * Get email_news
     *
     * @return boolean 
     */
    public function getEmailNews()
    {
        return $this->email_news;
    }
    /**
     * @var integer
     */
    protected $treatment;


    /**
     * Set treatment
     *
     * @param integer $treatment
     * @return OwnerProfile
     */
    public function setTreatment($treatment)
    {
        $this->treatment = $treatment;

        return $this;
    }

    /**
     * Get treatment
     *
     * @return integer 
     */
    public function getTreatment()
    {
        return $this->treatment;
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
     * Set cell_phone
     *
     * @param string $cellPhone
     * @return OwnerProfile
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
}
