<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * AAVVProfile
 *
 * La entidad representa el conjunto de perfiles del usuario
 */
class AAVV
{
    const STATUS_REGISTRATION_PROCESS = 0;
    const STATUS_COMPLETED_REGISTRATION = 1;
    const STATUS_REGISTERED = 2;
    const STATUS_ACTIVE = 2;
    const STATUS_INACTIVE = 3;

    /**
     * @var integer
     */
    private $id;

    /**
     * slug generado automaticamente para el establecimiento
     * @var string
     */
    protected $slug;

    /**
     * id publico de la aavv
     * @var string
     */
    protected $public_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $roles;

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
     * @var boolean
     */
    private $active;

    /**
     * rif del encargo en la agencia de viaje
     * @var string
     */
    private $rif;

    /**
     * @var string
     */
    private $social_reason;

    /**muestra correo electronico de la empresa  del encargado en la agencia de viaje
     * @var string
     */
    private $company_email;

    /** muestra el telefono local de agencia de viaje
     * @var string
     */
    private $phone;

    /**
     * Esta propiedad es un objeto valor coordenadas y representa la ubicacion geografica del
     * establecieminto
     *
     * @var Coordinate
     */
    protected $coordinates;

    /**
     * Numero de registro mercatil
     * @var String
     */
    private $merchant_id;

    /**
     * Nombre comercial de la aavv
     * @var String
     */
    private $commercial_name;

    /**
     * Año de apertura de la aavv
     * @var integer
     */
    private $opening_year;

    /**
     * Manejo de Correo personalizado
     * @var boolean
     */
    private $personalized_mail;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aavv_address;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aavv_profile;

    /**
     * Ruta al documento de arrendamiento del local donde se ubica la aavv.
     * @var integer
     */
    private $documents;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVVAgreement
     */
    private $agreement;

    /**
     * indica el estatus del registro mercantil
     * @var boolean
     */
    private $status;

    /**
     * Esta propiedad es usada para interactuar con las facturas asignadas a una AAVV.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVAgreement
     */
    private $invoices;

    /**
     * Esta propiedad es usada para interactuar con el conjunto de costo Adicionales
     * asignadas a la AAVV
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVVAdditionalQuota
     */
    private $additional_quota;

    /**
     * Conjunto de reservas que ha realizado esta aavv
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aavv_reservation_group;

    /**
     * Arreglo que contiene los datos  de perzonalización de colores
     * Se asigna los valores por defecto
     * @var array
     */
    private $customize;

    /**
     * Logo de la AAVV
     *
     * @var \Navicu\Core\Domain\Model\Entity\Document
     */
    private $logo;

    /*
     * Fecha en la que el establecimiento fue dado de alta
     *
     * @var \DateTime
     */
    private $join_date;

    /**
     * Esta propiedad es usada para interactuar con el conjunto de movimiento financiero
     * asignadas a la AAVV.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $financial_transactions;

    /**
     * Esta propiedad es usada para interactuar con el credito inicial asignado a la AAVV.
     *
     * @var float
     */
    private $credit_initial;

    /**
     * Esta propiedad es usada para interactuar con el credito disponible asignado a la AAVV.
     *
     * @var float
     */
    private $credit_available;

    /**
     * Esta propiedad es usada para almacenar la ganancia que le queda a NAVICU por la afiliciacion de la AAVV
     *
     * @var float
     */
    private $navicu_gain;

    /**
     * Esta propiedad es usada para almacenar cuando el correo ya fue enviado tras un primer mensaje de credito
     * insuficiente en el proceso de reserva
     *
     * @var boolean
     */
    private $sent_email_for_insufficient_credit = false;

    /**
     * indica si ya se envió un correo por disminución del credito por debajo un porcentaje del total
     * 
     * @var boolean
     */
    private $sent_email_for_credit_less_than = false;

    /**
     * Esta propiedad es usada para indicar la razon por la cual una agencia ha sido desactivada
     *
     * 0 no desactivada
     * 1 por razones comerciales
     * 2 facturas sin cancelar
     * 3 credit_available menor a 0
     * @var integer
     */
    private $deactivate_reason = 0;

    /**
     * Esta propiedad es usada para interactuar con los depositos realizados por la Agencia de
     *viaje.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bank_deposit;

    /**
     * fecha de registro o de alta de la aavv
     *
     * @var \DateTime
     */
    private $registration_date;

    /**
     * estatus en el que se encuentra la agencia de viajes para navicu
     *
     * 0 = en proceso de registro
     * 1 = registro finalizado
     * 2 = dado de alta / activo
     * 3 = inactivo por admin
     *
     * @var integer
     */
    private $status_agency = 0;

    /**
     * Representa el subdominio creado a la aavv
     *
     * @var \Navicu\InfrastructureBundle\Entity\Subdomain
     */
    private $subdomain;

    /**
     * Esta propiedad es para interactuar con las busquedas mas utilizadas por la agencia
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $top_destination;

    /**
     * indica si la agencia de viaje decidió operar con o sin credito
     * 
     * @var boolean
     */
    private $have_credit_zero = false;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->additional_quota = new ArrayCollection();
        $this->financial_transactions = new ArrayCollection();
        $this->bank_deposit = new ArrayCollection();
        $this->registration_date = new \DateTime('now');
        $this->joined_date = new \DateTime();
        $this->customize = json_encode([
            'activeButton' => '#62259d',
            'buttonPrimary' => '#b42371',
            'footer' => '#2e174b',
            'icon' => '#391860',
            'navbarMenu'=>'#391860',
            'navbarPrimary' => '#783cbd',
            'text' => '#808080',
            'title' => '#391860',
        ]);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return AAVVProfile
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
     * Set slug
     *
     * @param string $slug
     * @return Property
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set rif
     *
     * @param string $rif
     * @return AAVVProfile
     */
    public function setRif($rif)
    {
        $this->rif = $rif;

        return $this;
    }

    /**
     * Get rif
     *
     * @return string2
     */
    public function getRif()
    {
        return $this->rif;
    }

    /**
     * Set company_email
     *
     * @param string $companyEmail
     * @return AAVVProfile
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
     * Set merchant_id
     *
     * @param string $merchantId
     * @return AAVV
     */
    public function setMerchantId($merchantId)
    {
        $this->merchant_id = $merchantId;

        return $this;
    }

    /**
     * Get merchant_id
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return AAVV
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
     * Set commercial_name
     *
     * @param string $commercialName
     * @return AAVV
     */
    public function setCommercialName($commercialName)
    {
        $this->commercial_name = $commercialName;

        return $this;
    }

    /**
     * Get commercial_name
     *
     * @return string
     */
    public function getCommercialName()
    {
        return $this->commercial_name;
    }

    /**
     * Set opening_year
     *
     * @param integer $openingYear
     * @return AAVV
     */
    public function setOpeningYear($openingYear)
    {
        $this->opening_year = $openingYear;

        return $this;
    }

    /**
     * Get opening_year
     *
     * @return integer
     */
    public function getOpeningYear()
    {
        return $this->opening_year;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return AAVV
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVV
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
     * @return AAVV
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
     * @return AAVV
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
     * @return AAVV
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
     * Add pictures
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVImage $pictures
     * @return AAVV
     */
    public function addPicture(\Navicu\Core\Domain\Model\Entity\AAVVImage $pictures)
    {
        $this->pictures[] = $pictures;

        return $this;
    }

    /**
     * Remove pictures
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVImage $pictures
     */
    public function removePicture(\Navicu\Core\Domain\Model\Entity\AAVVImage $pictures)
    {
        $this->pictures->removeElement($pictures);
    }

    /**
     * Add documents
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVDocument $documents
     * @return AAVV
     */
    public function addDocument(\Navicu\Core\Domain\Model\Entity\AAVVDocument $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVDocument $documents
     */
    public function removeDocument(\Navicu\Core\Domain\Model\Entity\AAVVDocument $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add aavv_address
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAddress $aavvAddress
     * @return AAVV
     */
    public function addAavvAddress(\Navicu\Core\Domain\Model\Entity\AAVVAddress $aavvAddress)
    {
        $this->aavv_address[] = $aavvAddress;

        return $this;
    }

    /**
     * Remove aavv_address
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAddress $aavvAddress
     */
    public function removeAavvAddress(\Navicu\Core\Domain\Model\Entity\AAVVAddress $aavvAddress)
    {
        $this->aavv_address->removeElement($aavvAddress);
    }

    public function setCoordinates(Coordinate $coordinates)
    {
        $this->coordinates = $coordinates->toArray();

        return $this;
    }

    /**
     * Get coordinates
     *
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Get aavv_address
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAavvAddress()
    {
        return $this->aavv_address;
    }

    /**
     * Add aavv_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile
     * @return AAVV
     */
    public function addAavvProfile(\Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile)
    {
        $this->aavv_profile[] = $aavvProfile;

        return $this;
    }

    /**
     * Remove aavv_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile
     */
    public function removeAavvProfile(\Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile)
    {
        $this->aavv_profile->removeElement($aavvProfile);
    }

    /**
     * Get aavv_profile
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAavvProfile()
    {
        return $this->aavv_profile;
    }

    /**
     * Set social_reason
     *
     * @param string $socialReason
     * @return AAVV
     */
    public function setSocialReason($socialReason)
    {
        $this->social_reason = $socialReason;

        return $this;
    }

    /**
     * Get social_reason
     *
     * @return string
     */
    public function getSocialReason()
    {
        return $this->social_reason;
    }

    /**
     * Set agreement
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAgreement $agreement
     * @return AAVV
     */
    public function setAgreement(\Navicu\Core\Domain\Model\Entity\AAVVAgreement $agreement = null)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVAgreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Metodo para actualizar los parametros requeridos
     *
     * @param $data
     * @version 13/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function updateObject($data)
    {
        if (isset($data['social_reason']))
            $this->social_reason = $data['social_reason'];
        if (isset($data['slug']))
            $this->slug = $data['slug'];
        if (isset($data['rif']))
            $this->rif = $data['rif'];
        if (isset($data['company_email']))
            $this->company_email = $data['company_email'];
        if (isset($data['phone']))
            $this->phone = $data['phone'];
        if (isset($data['merchant_id']))
            $this->merchant_id = $data['merchant_id'];
        if (isset($data['commercial_name']))
             $this->commercial_name = $data['commercial_name'];
        if (isset($data['status']))
            $this->status = $data['status'];

    }

    /**
     * Metodo para obtener los parametros de la clase
     *
     * @version 13/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function toArray()
    {

        return [
            "id" => $this->id,
            "social_reason" => $this->social_reason,
            "slug" => $this->slug,
            "rif" => $this->rif,
            "company_email" => $this->company_email,
            "phone" => $this->phone,
            "merchant_id" => $this->merchant_id,
            "commercial_name" => $this->commercial_name,
            "status" => $this->status,
            "opening_year" => $this->opening_year,

        ];
    }

    public function isEmpty()
    {


        $attributes = [

            "social_reason" => $this->social_reason,
            "slug" => $this->slug,
            "rif" => $this->rif,
            "company_email" => $this->company_email,
            "phone" => $this->phone,
            "merchant_id" => $this->merchant_id,
            "commercial_name" => $this->commercial_name,
            "status" => $this->status,
            "opening_year" => $this->opening_year,

        ];



        foreach($attributes as $key => $attribute) {

            if($attribute != null)
                return false;
        }

        if($this->aavv_address->count() > 0)
            return false;

        if($this->documents->count() > 0)
            return false;

        if($this->coordinates != null)
            return false;

        return true;
    }

    /**
     * Add roles
     *
     * @param \Navicu\InfrastructureBundle\Entity\Role $roles
     * @return AAVV
     */
    public function addRole(\Navicu\InfrastructureBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
        $roles->setAavv($this);

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Navicu\InfrastructureBundle\Entity\Role $roles
     */
    public function removeRole(\Navicu\InfrastructureBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getRoleNames()
    {
        $roles = $this->roles;

        $names = array();

        foreach($roles as $role) {

            $names[] = $role->getName();
        }

        return $names;
    }

    /**
     * Set personalized_mail
     *
     * @param boolean $personalizedMail
     * @return AAVV
     */
    public function setPersonalizedMail($personalizedMail)
    {
        $this->personalized_mail = $personalizedMail;

        return $this;
    }

    /**
     * Get personalized_mail
     *
     * @return boolean
     */
    public function getPersonalizedMail()
    {
        return $this->personalized_mail;
    }

    /**
     * Add invoices
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoices
     * @return AAVV
     */
    public function addInvoice(\Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoices)
    {
        $this->invoices[] = $invoices;

        return $this;
    }

    /**
     * Remove invoices
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoices
     */
    public function removeInvoice(\Navicu\Core\Domain\Model\Entity\AAVVInvoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }


    /**
     * Add additional_quota
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAdditionalQuota $additionalQuota
     * @return AAVV
     */
    public function addAdditionalQuotum(\Navicu\Core\Domain\Model\Entity\AAVVAdditionalQuota $additionalQuota)
    {
        $this->additional_quota[] = $additionalQuota;

        return $this;
    }

    /**
     * Remove additional_quota
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVAdditionalQuota $additionalQuota
     */
    public function removeAdditionalQuotum(\Navicu\Core\Domain\Model\Entity\AAVVAdditionalQuota $additionalQuota)
    {
        $this->additional_quota->removeElement($additionalQuota);
    }

    /**
     * Get additional_quota
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdditionalQuota()
    {
        return $this->additional_quota;
    }

    /**
     * Add aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     * @return AAVV
     */
    public function addAavvReservationGroup(\Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup)
    {
        $this->aavv_reservation_group[] = $aavvReservationGroup;

        return $this;
    }

    /**
     * Remove aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     */
    public function removeAavvReservationGroup(\Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup)
    {
        $this->aavv_reservation_group->removeElement($aavvReservationGroup);
    }

    /**
     * Get aavv_reservation_group
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAavvReservationGroup()
    {
        return $this->aavv_reservation_group;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set customize
     *
     * @param array $customize
     * @return AAVV
     */
    public function setCustomize($customize)
    {
        $this->customize = json_encode($customize);
    }

    /**
     * Get customize
     *
     * @return array
     */
    public function getCustomize()
    {
        return json_decode($this->customize, true);
    }

    /**
     * Set logo
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $logo
     * @return AAVV
     */
    public function setLogo(\Navicu\Core\Domain\Model\Entity\Document $logo = null)
    {
        $this->logo = $logo;

    }

    /**
     * Get logo
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /*
     * Set join_date
     *
     * @param \DateTime $joinDate
     * @return AAVV
     */
    public function setJoinDate($joinDate)
    {
        $this->join_date = $joinDate;
    }

    /**
     * Add financial_transactions
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions $financialTransactions
     * @return AAVV
     */
    public function addFinancialTransaction(\Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions $financialTransactions)
    {
        $this->financial_transactions[] = $financialTransactions;
        return $this;
    }

    /**
     * Remove financial_transactions
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions $financialTransactions
     */
    public function removeFinancialTransaction(\Navicu\Core\Domain\Model\Entity\AAVVFinancialTransactions $financialTransactions)
    {
        $this->financial_transactions->removeElement($financialTransactions);
    }

    /**
     * Get financial_transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFinancialTransactions()
    {
        return $this->financial_transactions;

    }

    /**
     * Set credit_initial
     *
     * @param float $creditInitial
     * @return AAVV
     */
    public function setCreditInitial($creditInitial)
    {
        $this->credit_initial = $creditInitial;

        return $this;
    }

    /**
     * Get credit_initial
     *
     * @return float
     */
    public function getCreditInitial()
    {
        return $this->credit_initial;
    }

    /**
     * Set credit_available
     *
     * @param float $creditAvailable
     * @return AAVV
     */
    public function setCreditAvailable($creditAvailable)
    {
        $this->credit_available = $creditAvailable;
        return $this;
    }

    /*
     * Get credit_available
     *
     * @return float
     */
    public function getCreditAvailable()
    {
        return $this->credit_available;
    }

    /**
     * Add bank_deposit
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVBankPayments $bankDeposit
     * @return AAVV
     */
    public function addBankDeposit(\Navicu\Core\Domain\Model\Entity\AAVVBankPayments $bankDeposit)
    {
        $this->bank_deposit[] = $bankDeposit;
    }

    /**
     * Remove bank_deposit
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVBankPayments $bankDeposit
     */
    public function removeBankDeposit(\Navicu\Core\Domain\Model\Entity\AAVVBankPayments $bankDeposit)
    {
        $this->bank_deposit->removeElement($bankDeposit);
    }

    /**
     * Get bank_deposit
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankDeposit()
    {
        return $this->bank_deposit;
    }

    /**
     * Get registration_end_date
     *
     * @return \DateTime
     */
    public function getRegistrationEndDate()
    {
        return $this->registration_end_date;
    }
    /**
     * @var boolean
     */
    private $personalized_interface;


    /**
     * Set personalized_interface
     *
     * @param boolean $personalizedInterface
     * @return AAVV
     */
    public function setPersonalizedInterface($personalizedInterface)
    {
        $this->personalized_interface = $personalizedInterface;

        return $this;
    }

    /**
     * Get personalized_interface
     *
     * @return boolean
     */
    public function getPersonalizedInterface()
    {
        return $this->personalized_interface;
    }


    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    public function getdocumentByType($type)
    {
        $documents = $this->documents;

        $tempDocuments = array();

        foreach($documents as $document) {
            if($document->getType() == $type)
                $tempDocuments[] = $document;
        }

        $document = null;

        if(!empty($tempDocuments)) {
            $document = $tempDocuments[0];
        }
        return  $document;
    }


    public function getPictures()
    {
        $documents = $this->documents;

        $tempDocuments = array();

        foreach($documents as $document) {
            if($document->getType() == 'IMAGE')
                $tempDocuments[] = $document;
        }

        $pictures = array();

        foreach($tempDocuments as $picture) {
            $currentPicture = array();

            $currentPicture['id'] = $picture->getId();
            $currentPicture['path'] = $picture->getDocument()->getFilename();

            $pictures[] = $currentPicture;
        }

        return  $pictures;
    }

    /**
     * Set public_id
     *
     * @param string $publicId
     * @return AAVV
     */
    public function setPublicId($publicId)
    {
        return $this;
    }

    /**
     * Get public_id
     *
     * @return string
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * Add top_destination
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVTopDestination $topDestination
     * @return AAVV
     */
    public function addTopDestination(\Navicu\Core\Domain\Model\Entity\AAVVTopDestination $topDestination)
    {
        $this->top_destination[] = $topDestination;

        return $this;
    }


    /**
     * Set registration_date
     *
     * @param \DateTime $registrationDate
     * @return AAVV
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registration_date = $registrationDate;
    }

    /**
     * Get top_destination
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopDestination()
    {
        return $this->top_destination;
    }

    /**
     * Get registration_date
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    /**
     * Set status_agency
     *
     * @param integer $statusAgency
     * @return AAVV
     */
    public function setStatusAgency($statusAgency)
    {
        $this->status_agency = $statusAgency;

        return $this;
    }

    /**
     * Get status_agency
     *
     * @return integer
     */
    public function getStatusAgency()
    {
        return $this->status_agency;
    }

    /**
     * Remove top_destination
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVTopDestination $topDestination
     */
    public function removeTopDestination(\Navicu\Core\Domain\Model\Entity\AAVVTopDestination $topDestination)
    {
        $this->top_destination->removeElement($topDestination);
    }

    /**
     * Retorna true si el establecimiento tiene los datos
     * de personalización por defecto
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return bool
     */
    public function isCustomize()
    {

        return strtolower($this->customize) != strtolower(json_encode([
            'navbarPrimary' => '#783CBD',
            'navbarMenu'=>'#391860',
            'buttonPrimary' => '#783CBD',
            'activeButton' => '#391860',
            'title' => '#391860',
            'text' => '#dadada',
            'icon' => '#391860',
            'footer' => '#dadada',
        ]));
    }

    public function generatePublicId()
    {
        if (empty($this->public_id)) {
            $this->public_id = "NAV" . str_pad($this->getId(), 5, "0", STR_PAD_LEFT);
        }
    }

    /**
     * Set navicu_gain
     *
     * @param float $navicuGain
     * @return AAVV
     */
    public function setNavicuGain($navicuGain)
    {
        $this->navicu_gain = $navicuGain;

        return $this;
    }

    /**
     * Get navicu_gain
     *
     * @return float 
     */
    public function getNavicuGain()
    {
        return $this->navicu_gain;
    }

    /**
     * Set sent_email_for_insufficient_credit
     *
     * @param boolean $sentEmailForInsufficientCredit
     * @return AAVV
     */
    public function setSentEmailForInsufficientCredit($sentEmailForInsufficientCredit)
    {
        $this->sent_email_for_insufficient_credit = $sentEmailForInsufficientCredit;

        return $this;
    }

    /**
     * Get sent_email_for_insufficient_credit
     *
     * @return boolean 
     */
    public function getSentEmailForInsufficientCredit()
    {
        return $this->sent_email_for_insufficient_credit;
    }

    /**
     * Set deactivate_reason
     *
     * @param integer $deactivateReason
     * @return AAVV
     */
    public function setDeactivateReason($deactivateReason)
    {
        $this->deactivate_reason = $deactivateReason;

        return $this;
    }

    /**
     * Get deactivate_reason
     *
     * @return integer 
     */
    public function getDeactivateReason()
    {
        return $this->deactivate_reason;
    }

    /**
     * Set subdomain
     *
     * @param \Navicu\Core\Domain\Model\Entity\Subdomain $subdomain
     * @return AAVV
     */
    public function setSubdomain(\Navicu\InfrastructureBundle\Entity\Subdomain $subdomain = null)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain
     *
     * @return \Navicu\Core\Domain\Model\Entity\Subdomain 
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    public function getMonthlyServiceInvoices($month)
    {
        $invoices = array();

        foreach ($this->invoices as $invoice) {
            if($invoice->getLines()->count() > 0 && ($invoice->getDate()->format('m') == $month->format('m')))
                $invoices[] = $invoice;
        }

        return $invoices;
    }

    /**
     * Set sent_email_for_credit_less_than
     *
     * @param boolean $sentEmailForCreditLessThan
     * @return AAVV
     */
    public function setSentEmailForCreditLessThan($sentEmailForCreditLessThan)
    {
        $this->sent_email_for_credit_less_than = $sentEmailForCreditLessThan;

        return $this;
    }

    /**
     * Get sent_email_for_credit_less_than
     *
     * @return boolean 
     */
    public function getSentEmailForCreditLessThan()
    {
        return $this->sent_email_for_credit_less_than;
    }

    /**
     * Set have_credit_zero
     *
     * @param boolean $haveCreditZero
     * @return AAVV
     */
    public function setHaveCreditZero($haveCreditZero)
    {
        $this->have_credit_zero = $haveCreditZero;

        return $this;
    }

    /**
     * Get have_credit_zero
     *
     * @return boolean 
     */
    public function getHaveCreditZero()
    {
        return $this->have_credit_zero;
    }
}
