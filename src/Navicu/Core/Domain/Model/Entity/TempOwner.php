<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\User;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;

/**
 * Clase Pool "Doctrine ORM".
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del
 * usuario fantasma (usuario hotelero en proceso de registro).
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 06/05/2015
 */
class TempOwner
{

    /**
     * @var integer   $id   Identificador del objeto tempowner
     */
    protected $id;

    /**
     * @var Integer $lastsec       indica el ultimo paso del registro cumplido por el usuario
     */
    protected $lastsec;

    /**
     * @var Object $lastsec        indica la fecha en que expira el registro del usuario, despues de esta fecha se procede a eliminar al usuario
     */
    protected $expiredate;

    /**
     * @var array
     */
    protected $propertyForm;

    /**
     * @var array
     */
    protected $services_form;

    /**
     * @var array
     */
    protected $rooms_form;

    /**
     * @var array
     */
    protected $payment_info_form;

    /**
     * @var array
     */
    protected $terms_and_conditions_info;

    /**
     * @var array
     */
    protected $gallery_form;

    /**
     * @var array
     */
    protected $progress;

    /**
     * @var integer
     */
    protected $slug;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    protected $user_id;

    /**
     * @var array
     */
    private $validations;

    /**
     * Representa el comercial asociado al establecimiento
     *
     * @var \Navicu\Core\Domain\Model\Entity\CommercialProfile
     */
    private $commercial_profile;

    /**
     * Captador (persona que hizo el primer contacto con el establecimiento)
     * @var \Navicu\Core\Domain\Model\Entity\NvcProfile
     */
    private $recruit;

    /**
     * Metodo Constructor de la clase
     *
     * @param   String  $username      cadena de caracteres que representa un nombre de usuario
     * @param 	String 	$email 		cadena de caracteres que representa una direccion de correo electroncio
     * @param 	String 	$password 	cadena de caracteres que representa un password
     */
    public function __construct($username,$email,$password)
    {
        $this->user_id = new User($username,$email,$password);
        $this->progress = array(0,0,0,0,0,0,0,0);
        $this->lastsec = 0;
        $this->expiredate = new \DateTime(date('d-m-Y'));
        $this->expiredate->modify('+30 day');
        $this->array_services = new ArrayCollection();
        $this->rooms_form = array();
        $this->terms_and_conditions_info = array(
            'discount_rate'=>0.3,
            'accepted'=>false
        );
    }

    public function evaluateGalleriesServices ()
    {
        foreach ($this->array_services as $service) {
            $this->evaluateGalleryService( $service->getType() );
        }
    }

    protected function evaluateGalleryService (ServiceType $service)
    {
        $exist = false;
        if ( $service->getGallery() ) {
            if (isset($this->gallery_form['otherGalleries'])) {
                foreach ($this->gallery_form['otherGalleries'] as $gallery) {
                    if ($gallery['idGallery'] == $service->getId() ) {
                        $exist = true;
                    }
                }
                if (!$exist) {
                    $this->progress[3]=0;
                }
            }
        } else {
            $parent = $service->getParent();
            if (isset($parent)) {
                $this->evaluateGalleryService ($parent);
            }
        }
    }

    /**
     * Metodo que se ejecuta antes de ser almacenado el tempowner para convertir sus atributos en objetos almacenables
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 08/05/2015
     */
    public function prePersist()
    {
        $array_services = array();
        if( $this->propertyForm instanceof Property) {
          $this->propertyForm=$this->propertyForm->toArray();
        }
        if( !$this->array_services->isEmpty()) {
            foreach($this->array_services as $service)
            {
                array_push($array_services,$service->toArray());
            }
            $this->services_form=$array_services;
        }
        $rooms = $this->rooms_form;
        if (!empty($rooms)) {
            $this->rooms_form = [];
            foreach ($rooms as $room)
                if(isset($room) && is_array($room))
                    $this->rooms_form[] = $room;
        }
    }

    /**
     * Metodo que se ejecuta luego de ser leido un tmpowner
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 08/05/2015
     */
    public function postLoad()
    {
        if (!isset($this->rooms_form)) {
            $this->rooms_form = array();
        }
        $this->array_services = new ArrayCollection();
    }

    /**
     *   Metodo de genracion de slug
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 01/06/2015
     */
    public function generateSlug()
    {
        $this->slug=$this->id;
    }

    /**
     *   este metodo evalua el progreso del usuario en el formulario
     *   @return Integer
     */
    public function evaluateProgress()
    {
        $sum = 0;
        foreach ($this->progress as $progress) {
            if ($progress) {
                $sum = $sum + 17;
            }
        }
        if ($sum > 100) {
            $sum = 100;
        }
        return $sum;
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
     * Set lastsec
     *
     * @param integer $lastsec
     * @return TempOwner
     */
    public function setLastsec($lastsec)
    {
        $this->lastsec = $lastsec;

        return $this;
    }

    /**
     * Get lastsec
     *
     * @return integer 
     */
    public function getLastsec()
    {
        return $this->lastsec;
    }

    /**
     * Set expiredate
     *
     * @param \DateTime $expiredate
     * @return TempOwner
     */
    public function setExpiredate($expiredate)
    {
        $this->expiredate = $expiredate;

        return $this;
    }

    /**
     * Get expiredate
     *
     * @return \DateTime 
     */
    public function getExpiredate()
    {
        return $this->expiredate;
    }

    /**
     * Set propertyForm
     *
     * @param array $propertyForm
     * @return TempOwner
     */
    public function setPropertyForm(Property $propertyForm)
    {
        $this->propertyForm = $propertyForm;

        return $this;
    }

    /**
     * Get propertyForm
     *
     * @return array 
     */
    public function getPropertyForm($att = null)
    {
        if(!isset($att)) {
            return $this->propertyForm;
        } else {
            if (isset($this->propertyForm[$att])) {
                return $this->propertyForm[$att];
            } else {
                return null;
            }
        }
    }

    /**
     * Set services_form
     *
     * @param array $servicesForm
     * @return TempOwner
     */
    public function setServicesForm($servicesForm)
    {
        $this->services_form = $servicesForm;

        return $this;
    }

    /**
     * Get services_form
     *
     * @return array 
     */
    public function getServicesForm()
    {
        return $this->services_form;
    }

    /**
     * Set rooms_form
     *
     * @param array $roomsForm
     * @return TempOwner
     */
    public function setRoomsForm($roomsForm)
    {
        $this->rooms_form = $roomsForm;

        return $this;
    }

    /**
     * Get array_services
     *
     * @return array Collection
     */
    public function getServices()
    {
        return $this->array_services;
    }

    /**
     * Get rooms_form
     *
     * @return array 
     */
    public function getRoomsForm()
    {
        return $this->rooms_form;
    }

    /**
     * Set payment_info_form
     *
     * @param array $paymentInfoForm
     * @return TempOwner
     */
    public function setPaymentInfoForm($paymentInfoForm)
    {
        $this->payment_info_form = $paymentInfoForm;

        return $this;
    }

    /**
     * Get payment_info_form
     *
     * @return array 
     */
    public function getPaymentInfoForm()
    {
        return $this->payment_info_form;
    }

    /**
     * Set terms_and_conditions_info
     *
     * @param array $termsAndConditionsInfo
     * @return TempOwner
     */
    public function setTermsAndConditionsInfo($termsAndConditionsInfo)
    {
        $this->terms_and_conditions_info = $termsAndConditionsInfo;

        return $this;
    }

    /**
     * Get terms_and_conditions_info
     *
     * @return array 
     */
    public function getTermsAndConditionsInfo()
    {
        return $this->terms_and_conditions_info;
    }

    /**
     * Set gallery_form
     *
     * @param array $galleryForm
     * @return TempOwner
     */
    public function setGalleryForm($galleryForm)
    {
        $this->gallery_form = $galleryForm;

        return $this;
    }

    /**
     * Get gallery_form
     *
     * @return array 
     */
    public function getGalleryForm()
    {
        return $this->gallery_form;
    }

    public function addRoom( Room $room , $index = null )
    {
        if ( !isset($index) ) {
            array_push($this->rooms_form,$room->toArray());
        }else{
            $this->rooms_form[$index] = $room->toArray();
        }
    }

    public function unsetRoom($index)
    {
        if(isset($this->rooms_form[$index])){
            $this->rooms_form[$index] = null;
        }
    }

    public function removeRoom($index)
    {
        unset($this->rooms_form[$index]);
        $resp = array();
        foreach($this->rooms_form as $room){
            array_push($resp, $room);
        }
        $this->rooms_form = $resp;
    }

    public function selectRoom($index)
    {
        if ( isset($this->rooms_form[$index]) ) {
            return $this->rooms_form[$index];
        } else {
            return null;
        }
    }

    public function existsRoom($newroom)
    {
        $val = false;
        foreach ( $this->rooms_form as $room ) {
            //$val = $val || ( $newroom->getType()->getId() == $room['type'] );
            if ( $newroom->getType()->getId() == $room['type'] ) {
                if ( $newroom->getMaxPeople() == $room['max_people'] ) {
                    $val = true;
                } else {
                    $newroom->setUniqueType(false);
                }
            }
        }
        return $val;
    }

    public function getIndexRoom($newroom)
    {
        $val = null;
        foreach( $this->rooms_form as $id => $room ){
            if ( $newroom->getType()->getId() == $room['type'] ){
                $val = $id;
            }
        }
        return $val;
    }

    public function getRoom($index)
    {
        $val = null;
        if (isset($this->rooms_form[$index])) {
            $val = $this->rooms_form[$index];
        }
        return $val;
    }

    /**
     * Get progress
     *
     * @return array 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set slug
     *
     * @param integer $slug
     * @return TempOwner
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return integer 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set user_id
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $userId
     * @return TempOwner
     */
    public function setUserId($userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Set progress
     *
     * @param array $progress
     * @return TempOwner
     */
    public function setProgress($index,$val)
    {
        if (isset($this->progress[$index])) {
            $this->progress[$index] = $val;
        }

        return $this;
    }

    /**
     * Get user_id
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Calcula  la cantidad de habitaciones registradas hasta el momento
     *
     *  @return Integer
     */
    public function getAmountRoomsAdd()
    {
        $suma = 0;
        foreach ($this->rooms_form as $room) {
            $suma = $suma + $room['amount_rooms'];
        }
        return $suma;
    }

    /**
     * Calcula  la cuota de habitaciones cubiertas hasta el momento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 08-10-2015
     * @return Integer
     */
    public function getBasicQuotasRoomsAdd()
    {
        $suma = 0;
        foreach ($this->rooms_form as $room) {
            $base = isset($room['base_availability']) ? $room['base_availability'] : 0;
            $suma = $suma + $base;
        }
        return $suma;
    }

    /**
     * Set validations
     *
     * @param array $validations
     * @return TempOwner
     */
    public function setValidations($validations)
    {
        $this->validations = $validations;

        return $this;
    }

    /**
     * Get validations
     *
     * @return array 
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * indica si la entidad se encuentra en un estado valido
     */
    public function validate()
    {
        $validate = true;
        foreach ($this->validations as $val) {
            $validate = $validate && (is_string($val) && $val=='OK');
        }
        return $validate;
    }

    /**
     * Set commercial_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\CommercialProfile $commercialProfile
     * @return TempOwner
     */
    public function setCommercialProfile(\Navicu\Core\Domain\Model\Entity\CommercialProfile $commercialProfile = null)
    {
        $this->commercial_profile = $commercialProfile;

        return $this;
    }

    /**
     * Get commercial_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\CommercialProfile 
     */
    public function getCommercialProfile()
    {
        return $this->commercial_profile;
    }
    /**
     * @var \Navicu\Core\Domain\Model\Entity\NvcProfile
     */
    private $nvc_profile;


    /**
     * Set nvc_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile
     * @return TempOwner
     */
    public function setNvcProfile(\Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile = null)
    {
        $this->nvc_profile = $nvcProfile;

        return $this;
    }

    /**
     * Get nvc_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\NvcProfile 
     */
    public function getNvcProfile()
    {
        return $this->nvc_profile;
    }

    /**
     * Set recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfile $recruit
     * @return TempOwner
     */
    public function setRecruit(\Navicu\Core\Domain\Model\Entity\NvcProfile $recruit = null)
    {
        $this->recruit = $recruit;

        return $this;
    }

    /**
     * Get recruit
     *
     * @return \Navicu\Core\Domain\Model\Entity\NvcProfile 
     */
    public function getRecruit()
    {
        return $this->recruit;
    }
}
