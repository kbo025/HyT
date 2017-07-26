<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * Clase AAVVProfile.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del perfil del usuario AAVV.
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 */
class AAVVProfile
{

    # 0: inactivo en proceso de registro
    # 1: activo
    # 2: inactivo por mora
    # 3: inactivado por la agencia de viajes (borrado logico)
    const STATUS_REGISTRATION_PROCESS = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE_DEBT_REASON = 2;
    const STATUS_INACTIVE_COMMERCIAL_REASON = 3;
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Nombre y Apellido del usuario
     * @var string
     */
    private $fullname;

    /**
     * @var string
     */
    private $email;

    /**
     * Cargo del usuario
     * @var string
     */
    private $position;

    /**
     * Ubicacion del usuario
     * @var Location
     */
    private $location;

    /**
     * Esta propiedad es usada para interactuar con la cuenta de usuario asignada por el sistema.
     *
     * @var User Type Object
     **/
    protected $user;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * Telefono celular del usuario
     *
     * @var String
     */
    protected $phone;

    /**
     * Orden del usuario en la agencia de viajes
     *
     * @var String
     */
    protected $profileorder;

    /**
     * @var string
     */
    private $document_id;

    /**
     * @var boolean
     */
    private $newsemailreceiver;

    /**
     * @var boolean
     */
    private $confirmationemailreceiver;

    /**
     * @var boolean
     */
    private $cancellationemailreceiver;

    /**
     * @var integer
     */
    private $status = 0;

    /**
     * @var \DateTime
     */
    private $last_activation;

    /**
     * @var \DateTime
     */
    private $deleted_at;

    /**
     * Relacion a las reservaciones realizadas por el aavvProfile
     *
     * @var ArrayCollection
     */
    private $aavv_reservation_group;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aavv_reservation_group = new ArrayCollection();
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
     * Set fullname
     *
     * @param string $fullname
     * @return AAVVProfile
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    public function setProfileOrder($order)
    {
        $this->profileorder = $order;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getProfileOrder()
    {
        return $this->profileorder;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AAVVProfile
     */
    public function setEmail($email)
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
     * Set position
     *
     * @param string $position
     * @return AAVVProfile
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
     * Set phone
     *
     * @param string $phone
     * @return AAVVProfile
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
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return AAVVProfile
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
     * Set location
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $location
     * @return AAVVProfile
     */
    public function setLocation(\Navicu\Core\Domain\Model\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Navicu\Core\Domain\Model\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVProfile
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

    /**
     * Set document_id
     *
     * @param string $documentId
     * @return AAVVProfile
     */
    public function setDocumentId($documentId)
    {
        $this->document_id = $documentId;

        return $this;
    }

    /**
     * Get document_id
     *
     * @return string
     */
    public function getDocumentId()
    {
        return $this->document_id;
    }

    /**
     * Set confirmationemailreceiver
     *
     * @param boolean $confirmationemailreceiver
     * @return AAVVProfile
     */
    public function setConfirmationemailreceiver($confirmationemailreceiver)
    {
        $this->confirmationemailreceiver = $confirmationemailreceiver;

        return $this;
    }

    /**
     * Get confirmationemailreceiver
     *
     * @return boolean
     */
    public function getConfirmationemailreceiver()
    {
        return $this->confirmationemailreceiver;
    }

    /**
     * Set cancellationemailreceiver
     *
     * @param boolean $cancellationemailreceiver
     * @return AAVVProfile
     */
    public function setCancellationemailreceiver($cancellationemailreceiver)
    {
        $this->cancellationemailreceiver = $cancellationemailreceiver;

        return $this;
    }

    /**
     * Get cancellationemailreceiver
     *
     * @return boolean
     */
    public function getCancellationemailreceiver()
    {
        return $this->cancellationemailreceiver;
    }

    /**
     * Set newsEmailReceiver
     *
     * @param boolean $newsEmailReceiver
     * @return AAVVProfile
     */
    public function setNewsEmailReceiver($newsEmailReceiver)
    {
        $this->newsemailreceiver = $newsEmailReceiver;

        return $this;
    }

    /**
     * Get newsEmailReceiver
     *
     * @return boolean
     */
    public function getNewsEmailReceiver()
    {
        return $this->newsemailreceiver;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AAVVProfile
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set last_activation
     *
     * @param \DateTime $lastActivation
     * @return AAVVProfile
     */
    public function setLastActivation($lastActivation)
    {
        $this->last_activation = $lastActivation;

        return $this;
    }

    /**
     * Get last_activation
     *
     * @return \DateTime
     */
    public function getLastActivation()
    {
        return $this->last_activation;
    }

    /**
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return AAVVProfile
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Add aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     * @return AAVVProfile
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
     * @return ArrayCollection
     */
    public function getAavvReservationGroup()
    {
        return $this->aavv_reservation_group;
    }
}
