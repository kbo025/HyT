<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;

/**
 * Clase ClientProfile
 *
 * representa los datos de un cliente
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Carlos Aguilera <ceaf.21@gmail.com>
 */
class ClientProfile
{

    /**
     * @var integer
     */
    private $id;

    /**
     * Obejeto de usuario de FOSUser
     * @var Object user
     */
    private $user;

    /**
     * Representa el nombre completo del cliente (Nombre y Apellido)
     * @var string
     */
    private $full_name;

    /**
     * Representa la cedula del cliente
     * @var string
     */
    private $identity_card;

    /**
     * Representa el genero del cliente (Masculino o Femenino)
     * @var integer
     */
    private $gender;

    /**
     * Representa el correo electronico del cliente
     * @var EmailAddress
     */
    private $email;

    /**
     * Representa el telefeno del cliente
     * @var Phone
     */
    private $phone;

    /**
     * Representa si el usuario esta dispuesto a recibir promociones por email
     * @var boolean
     */
    private $email_news;

    /**
     * Fecha de nacimiento del cliente
     * @var date
     */
    private $birth_date;

    /**
     * Son las reservaciones realizadas por el cliente
     *
     * @var ArrayCollection
     */
    private $reservations;

    /**
     * Redes sociales relacionas con el cliente
     *
     * @var ArrayCollection
     */
    private $social;

    /**
     * Dirección de residencia del usuario
     * @var string
     */
    private $address;

    /**
     * Código del area asociado a su localidad
     * @var string
     */
    private $zip_code;

    /**
     * Fecha en la cual el cliente fue registrado en el sistema
     * @var \DateTime
     */
    private $joined_date;

    /**
     * Hobbies que practica el usuario
     * @var ArrayCollection
     */
    private $hobbies;

    /**
     * Profesion que ejerce el usuario
     * @var ArrayCollection
     */
    private $professions;

    /**
     * Representa la compañia donde trabaja el usuario
     * @var string
     */
    private $company;

    /**
     * Cargo el cual desempeña el usuario
     * @var string
     */
    private $position;

    /**
     * Estado donde se encuentra el usuario
     * @var integer
     */
    private $state;

    /**
     * localidad que contiene
     * @var ArrayCollection
     */
    private $location;

    /**
     * Trato del usuario (sr, sra, srta).
     * @var integer
     */
    private $treatment;

    /**
     * @var integer
     */
    private $country;

    /**
     * Variable que indica si es de tipo juridico o natural
     * @var string
     */
    private $typeIdentity;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->social = new ArrayCollection();
        $this->hobbies = new ArrayCollection();
        $this->professions = new ArrayCollection();
        $today = new \DateTime("now");
        $this->joined_date = $today;
        $this->email_news = true;
    }


    /** set Id
     * @return inteher
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
     * Set full_name
     *
     * @param string $fullName
     * @return ClientProfile
     */
    public function setFullName($fullName)
    {
        if(empty($fullName))
            throw new EntityValidationException('full_name',\get_class($this),'is_null');
        else if (!is_string($fullName))
            throw new EntityValidationException('full_name',\get_class($this),'invalid_type');
        else
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
     * Set gender
     *
     * @param integer $gender
     * @return ClientProfile
     */
    public function setGender($gender)
    {
        if(is_null($gender))
            $this->gender = null;
        else {
            if (!is_integer($gender))
                throw new EntityValidationException('gender', \get_class($this), 'invalid_type');
            else if (($gender != 0) && ($gender != 1) && ($gender != 2))
                throw new EntityValidationException('gender', \get_class($this), 'illegal_type');
            else
                $this->gender = $gender;
        }
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
     * Set email
     *
     * @param string $email
     * @return ClientProfile
     */
    public function setEmail(EmailAddress $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return ClientProfile
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email_news
     *
     * @param boolean $emailNews
     * @return ClientProfile
     */
    public function setEmailNews($emailNews)
    {
        $this->email_news = !empty($emailNews);

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
     * Add reservations
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservations
     * @return ClientProfile
     */
    public function addReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;

        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservations
     */
    public function removeReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Get reservations
     *
     * @return ArrayCollection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     *  convierte el OV EmailAdress a su representacion String para el almacenamiento
     */
    public function emailToString()
    {
        if($this->email instanceof EmailAddress)
            $this->email = $this->email->toString();
    }

    /**
     *  convierte el OV Phone a su representacion String para el almacenamiento
     */
    public function phoneToString()
    {
        if($this->phone instanceof Phone)
            $this->phone = $this->phone->toString();
    }

    /**
     * Set identity_card
     *
     * @param string $identityCard
     * @return ClientProfile
     */
    public function setIdentityCard($identityCard)
    {
        if(empty($identityCard))
            throw new EntityValidationException('identityCard',\get_class($this),'is_null');
        else if (!is_string($identityCard))
            throw new EntityValidationException('identityCard',\get_class($this),'invalid_type');
        else
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
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return ClientProfile
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Add social
     *
     * @param \Navicu\Core\Domain\Model\Entity\RedSocial $social
     * @return ClientProfile
     */
    public function addSocial(\Navicu\Core\Domain\Model\Entity\RedSocial $social)
    {
        $this->social[] = $social;

        return $this;
    }

    /**
     * Remove social
     *
     * @param \Navicu\Core\Domain\Model\Entity\RedSocial $social
     */
    public function removeSocial(\Navicu\Core\Domain\Model\Entity\RedSocial $social)
    {
        $this->social->removeElement($social);
    }

    /**
     * Get social
     *
     * @return ArrayCollection
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return ClientProfile
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
     * Set address
     *
     * @param string $address
     * @return ClientProfile
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /** Set country
     *
     * @param integer $country
     * @return ClientProfile
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /** Get country
     *
     * @return integer
     */
    public function getCountry()
    {
        return $this->country();
    }

    /**
     * Set zip_code
     *
     * @param string $zipCode
     * @return ClientProfile
     */
    public function setZipCode($zipCode)
    {
        $this->zip_code = $zipCode;
        return $this;
    }

    /**
     * Get zip_code
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return ClientProfile
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /** Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set joined_date
     *
     * @param \DateTime $joinedDate
     * @return ClientProfile
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
     * Add hobbies
     *
     * @param \Navicu\Core\Domain\Model\Entity\Hobbies $hobbies
     * @return ClientProfile
     */
    public function addHobby(\Navicu\Core\Domain\Model\Entity\Hobbies $hobbies)
    {
        $this->hobbies[] = $hobbies;

        return $this;
    }

    /**
     * Remove hobbies
     *
     * @param \Navicu\Core\Domain\Model\Entity\Hobbies $hobbies
     */
    public function removeHobby(\Navicu\Core\Domain\Model\Entity\Hobbies $hobbies)
    {
        $this->hobbies->removeElement($hobbies);
    }

    /**
     * Get hobbies
     *
     * @return ArrayCollection
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * Add professions
     *
     * @param \Navicu\Core\Domain\Model\Entity\Profession $professions
     * @return ClientProfile
     */
    public function addProfession(\Navicu\Core\Domain\Model\Entity\Profession $professions)
    {
        $this->professions[] = $professions;

        return $this;
    }

    /**
     * Remove professions
     *
     * @param \Navicu\Core\Domain\Model\Entity\Profession $professions
     */
    public function removeProfession(\Navicu\Core\Domain\Model\Entity\Profession $professions)
    {
        $this->professions->removeElement($professions);
    }

    /**
     * Get professions
     *
     * @return ArrayCollection
     */
    public function getProfessions()
    {
        return $this->professions;
    }

    /**
     * Se eliminaran las profesiones pertenecientes al usuario
     *
     * @author Isabel Nieto <isabel.cnd@gmail.com>
     */
    public function removeAllProfessions()
    {
        $this->professions = new ArrayCollection();
    }

    /**
     * Se eliminaran los hobbies pertenecientes al usuario
     *
     * @author Isabel Nieto <isabel.cnd@gmail.com>
     */
    public function removeAllHobbies()
    {
        $this->hobbies = new ArrayCollection();
    }


    /**
     * Set company
     *
     * @param string $company
     * @return ClientProfile
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
     * @param string $position
     * @return ClientProfile
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return ArrayCollection
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param ArrayCollection $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
    /**
     * @return int
     */
    public function getTreatment()
    {
        return $this->treatment;
    }

    /**
     * @param int $treatment
     */
    public function setTreatment($treatment)
    {
        $this->treatment = $treatment;
    }

    /**
     * @return int
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * @param int $municipality
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;
    }



    /**
     * Esta función es usada para devolver un array con información del
     * objeto ClientProfile.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function toArray()
    {
        $data["fullName"] = $this->full_name;
        $data["identityCard"] = $this->identity_card;
        $data["gender"] = $this->gender;
        $data["email"] = $this->email;
        $data["phone"] = $this->phone;
        $data["address"] = $this->address;
        $data["zipCode"] = $this->zip_code;
        $data["state"] = $this->state;
        $data["country"] = $this->country;
        $data['joinedDate'] = ($this->joined_date != null) ? date_format($this->joined_date, 'Y-m-d') : null;
        $data['birthDate'] = ($this->birth_date != null) ? date_format($this->birth_date, 'Y-m-d') : null;
        $data['type'] = is_null($this->typeIdentity) ? "" : $this->typeIdentity;

        return $data;
    }

    /**
     * Esta funcion actualiza los valores primitivos ingresados para la edicion del
     * perfil del usuario
     *
     * @author Isabel Nieto <isabel.cnd@gmail.com>
     * @param $value array con los parametros a actualizar
     * @return $this
     */
    public function updateObject($value)
    {
        if(isset($value['treatment']))
            $this->setTreatment($value['treatment']);
        if (isset($value['fullName']))
            $this->setFullName($value['fullName']);
        if (isset($value['identityCard']))
            $this->setIdentityCard($value['identityCard']);
        if (isset($value['gender']))
            $this->setGender($value['gender']);
        if (isset($value['email']))
            $this->setEmail($value['email']);
        if (isset($value['phone']))
            $this->setPhone($value['phone']);
        if (isset($value['company']))
            $this->setCompany($value['company']);
        if (isset($value['position']))
            $this->setPosition($value['position']);
        if (isset($value['birthDate']))
            $this->setBirthDate(date_create($value['birthDate']));
        if (isset($value['zipCode']))
            $this->setZipCode($value['zipCode']);
        if (isset($value['emailNews']))
            $this->setEmailNews($value['emailNews']);
        if (isset($value['address']))
            $this->setAddress($value['address']);
        if (isset($value['type']))
            $this->setAddress($value['type']);
        if (isset($value['location'])) {
            $this->setLocation($value['location']);
            $this->setCountry($value['location']->getRoot()->getId());
            $this->setState($value['location']->getId());
        }
          return $this;
    }

    /**
     * Add location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return ClientProfile
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
     * Set typeIdentity
     *
     * @param string $typeIdentity
     * @return ClientProfile
     */
    public function setTypeIdentity($typeIdentity)
    {
        $this->typeIdentity = $typeIdentity;

        return $this;
    }

    /**
     * Get typeIdentity
     *
     * @return string 
     */
    public function getTypeIdentity()
    {
        return $this->typeIdentity;
    }
}
