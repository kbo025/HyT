<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\ValueObject\PublicId;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\Entity\Location;
use Navicu\Core\Domain\Model\Entity\Language;
use Navicu\Core\Domain\Model\Entity\Accommodation;
use Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\Entity\CurrencyType;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\ContactPerson;
use Navicu\Core\Domain\Model\Entity\LogsOwner;
use Navicu\Core\Domain\Model\Entity\LogsUser;
use Navicu\Core\Domain\Model\Entity\Agreement;
use Navicu\Core\Domain\Model\Entity\PaymentInfoProperty;

/**
 * Clase Property.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los establecimiento de los propietarios.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Property
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * slug generado automaticamente para el establecimiento
     * @var string
     */
    protected $slug;

    /**
     * id publica manejada por el cliente
     * @var string
     */
    protected $public_id;

    /**
     * lista de contactos del establecimiento
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contacts;

    /**
     * nombre del establecimiento
     * @var string
     */
    protected $name;

    /**
     * indica si el establecimiento esta activo dentro de navicu o no
     * @var boolean
     */
    protected $active;

    /**
     * direccion del establecimiento
     * @var string
     */
    protected $address;

    /**
     * categoria o nivel en estrellas del hotel
     * @var integer
     */
    protected $star;

    /**
     * direccion web del establecimiento
     * @var string
     */
    protected $url_web;

    /**
     * cantidad de habitaciones ofrecidas por el hotel
     * @var integer
     */
    protected $amount_room;

    /**
     * numero de pisos del establecimiento
     * @var integer
     */
    protected $number_floor;

    /**
     * hora de checkin
     * @var \DateTime
     */
    protected $check_in;

    /**
     * hora de checkout
     * @var \DateTime
     */
    protected $check_out;

    /**
     * descripcion del hotel
     * @var string
     */
    protected $description;

    /**
     * OV numero de telefono del establecimiento
     * @var array
     */
    protected $phones;

    /**
     * OV numero de fax del establecimiento
     * @var string
     */
    protected $fax;

    /**
     * email de contacto
     * @var string
     */
    protected $emails;

    /**
     * @var float
     */
    protected $rating;

    /**
     * @var float
     */
    protected $discount_rate;

    /**
     * año de apertura
     * @var integer
     */
    protected $opening_year;

    /**
     * año de renovacion
     * @var integer
     */
    protected $renewal_year;

    /**
     * año de renovacion de areas publicas
     * @var integer
     */
    protected $public_areas_renewal_year;

    /**
     * edad requerida para hacer checkin
     * @var integer
     */
    protected $check_in_age;

    /**
     * porcentaje de impuesto cobrado sobre los precios
     * @var float
     */
    protected $tax;

    /**
     * @var float
     */
    protected $tax_rate;

    /**
     * informacion adicional
     * @var string
     */
    protected $additional_info;

    /**
     * Esta propiedad es un objeto valor coordenadas y representa la ubicacion geografica del
     * establecieminto
     *
     * @var Coordinate
     */
    protected $coordinates;

    /**
     * indica si el establecimiento presta camas adicionales
     * @var boolean
     */
    protected $beds;

    /**
     * indica si el establecimiento cobra por prestar camas adicionales
     * @var boolean
     */
    protected $beds_additional_cost;

    /**
     * indica si el establecimiento tiene politicas con los niños
     * @var boolean
     */
    protected $child;

    /**
     * indica si hay un costo adicional por prestar cunas
     * @var boolean
     */
    protected $cribs_additional_cost;

    /**
     * indica la cantidad maxima de cunas que presta
     * @var integer
     */
    protected $cribs_max;

    /**
     * indica si presta cunas
     * @var boolean
     */
    protected $cribs;

    /**
     * indica si admite mascotas
     * @var boolean
     */
    protected $pets;

    /**
     * indica si hay un costo adicional por admitir mascotas
     * @var boolean
     */
    protected $pets_additional_cost;

    /**
     * indica si se puede pagar en efectivo
     * @var boolean
     */
    protected $cash;

    /**
     * indica la maxima cantidad que recibe el establecimiento en efectivo
     * @var float
     */
    protected $max_cash;

    /**
     * porcentaje cobrado por impuesto de la ciudad
     * @var float
     */

    protected $city_tax;

    /**
     * indica el tipo de impuesto de la ciudad que se cobra
     * 1: por noche
     * 2: por Persona
     * 3: por noche y persona
     * @var integer
     */
    protected $city_tax_type;

    /**
     * cantidad maxima de noches en las que se cobra el impuesto de la ciudad
     * @var integer
     */
    protected $city_tax_max_nights;

    /**
     * indica si acepta tarjetas de credito
     * @var boolean
     */
    protected $credit_card;

    /**
     * nombre de la cadena hotelera
     * @var string
     */
    private $hotel_chain_name;

    /**
     *  indica si el establecimiento acepta la TC American Express
     * @var boolean
     */
    protected $credit_card_amex;

    /**
     * indica si acepta TC MasterCard
     * @var boolean
     */
    protected $credit_card_mc;

    /**
     * indica si acepta TC Visa
     * @var boolean
     */
    protected $credit_card_visa;

    /**
     * @var PropertyCancellationPolicy
     */
    protected $base_policy;

    /**
     * imagen de perfil
     * @var PropertyFavoriteImages
     */
    protected $profile_image;

    /**
     * conjunto de servicios generales que presta el establecimiento
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $services;

    /**
     * conjunto de tipos de habitaciones que ofrece
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $rooms;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $property_cancellation_policies;

    /**
     * galerias de imagenes del establecimiento
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $property_gallery;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $property_favorite_images;

    /**
     * @var Location
     */
    protected $location;

    /**
     * @var Accommodation
     */
    protected $accommodation;

    /**
     * @var CurrencyType
     */
    protected $currency;

    /**
     * @var Category
     */
    protected $city_tax_currency;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $owners_profiles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $languages;

    /**
     * @var boolean
     */
    private $all_included;

    /**
     * @var boolean
     */
    private $debit;

    /**
     * @var integer
     */
    private $comercial_rooms;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Agreement
     */
    private $agreement;

    /**
     * @var boolean
     */
    private $prominent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $logs_owners;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $logs_users;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\PaymentInfoProperty
     */
    private $payment_info;

    /**
     * indica si el establecimiento carga sus precios en base la tarifa neta ($rate_type = 1)
     * o base a la tarifa de venta ($rate_type = 2)
     * @var integer
     */
    private $rate_type;

    /**
     * indica si el servicio de camas adicionales se presta con previo aviso
     *
     * @var boolean
     */
    private $beds_prior_notice;

    /**
     * indica si el servicio de cunas se presta con previo aviso
     *
     * @var boolean
     */
    private $cribs_prior_notice;

    /**
     * indica el la cuota basica de habitaciones ofrecidas a navicu por el establecimiento
     *
     * @var integer
     */
    private $basic_quota = 1;

    /**
     * La variable contiene las reservaciones realizadas en el establecimiento
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reservations;

    /**
     * El atributo representa la fecha en que se dio de alta el establecimiento
     *
     * @var \DateTime
     */
    private $join_date;

    /**
     * El atributo representa la fecha del inicio de registro del establecimiento (ascribere)
     * @var \DateTime
     */
    private $registration_date;

    /**
     * El atributo representa el diseño de la ficha del establecimiento
     * (1 : Alta resolución, 2 : Baja resolución)
     * @var integer
     */
    private $design_view_property;

    /**
     * Representa el comercial asociado al establecimiento
     *
     * @var \Navicu\Core\Domain\Model\Entity\CommercialProfile
     */
    private $commercial_profile;

    /**
     * las politicas de edades que definen quien es un bebe, niño y adulto en un establecimiento
     *
     * @var array
     */
    private $age_policy;

    /**
     * Representa el usuario admin (comercial) encargado
     * de gestionar al establecimiento
     *
     * @var \Navicu\Core\Domain\Model\Entity\NvcProfile
     */
    private $nvc_profile;

    /**
     * Captador (persona que hizo el primer contacto con el establecimiento)
     * @var \Navicu\Core\Domain\Model\Entity\NvcProfile
     */
    private $recruit;

	/**
	 * @var \DateTime
	 */
	private $unpublished_date;

    /**
     * @var string
     */
    private $featured_location;

    /**
     * @var boolean
     */
    private $featured_home = 'false';

    /**
     * @var boolean
     */
    private $promo_home = 'false';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->all_included = false;
        $this->debit = false;
        $this->services = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->property_cancellation_policies = new ArrayCollection();
        $this->property_gallery = new ArrayCollection();
        $this->property_favorite_images = new ArrayCollection();
        $this->owners_profiles = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->discount_rate = 0.3;
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
        /*$this->slug = $slug;*/

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
     * Set public_id
     *
     * @param string $publicId
     * @return Property
     */
    public function setPublicId($last_id)
    {
        $loc = $this->location;
        while ( $loc->getLvl() != 0 )
            $loc = $loc->getParent();
        $this->public_id = $loc->getId()."-".str_pad($last_id, 4, "0", STR_PAD_LEFT);

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
     * Set name
     *
     * @param string $name
     * @return Property
     */
    public function setName($name)
    {
        /*if(empty($name))
            throw new EntityValidationException('name',\get_class($this),'is_null');
        else*/
            $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Property
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
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

    /**
     * Set address
     *
     * @param string $address
     * @return Property
     */
    public function setAddress($address)
    {
        /*if(empty($address))
            throw new EntityValidationException('address',\get_class($this),'is_null');
        else*/
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

    /**
     * Set star
     *
     * @param integer $star
     * @return Property
     */
    public function setStar($star)
    {
        /*if(empty($star))
            throw new EntityValidationException('star',\get_class($this),'is_null');
        elseif (!is_numeric($star))
            throw new EntityValidationException('star',\get_class($this),'ilegal_value');
        elseif ($star<1 or $star>5)
            throw new EntityValidationException('star',\get_class($this),'ilegal_value');
        else*/
            $this->star = $star;

        return $this;
    }

    /**
     * Get star
     *
     * @return integer
     */
    public function getStar()
    {
        return $this->star;
    }

    /**
     * Set url_web
     *
     * @param string $urlWeb
     * @return Property
     */
    public function setUrlWeb(Url $urlWeb = null)
    {
        $this->url_web = $urlWeb;

        return $this;
    }

    /**
     * Get url_web
     *
     * @return string
     */
    public function getUrlWeb()
    {
        return $this->url_web;
    }

    /**
     * Set amount_room
     *
     * @param integer $amountRoom
     * @return Property
     */
    public function setAmountRoom($amountRoom)
    {
        /*if(empty($amountRoom))
            throw new EntityValidationException('amountRoom',\get_class($this),'is_null');
        elseif (!is_numeric($amountRoom))
            throw new EntityValidationException('amountRoom',\get_class($this),'ilegal_value');
        elseif ($amountRoom<1)
            throw new EntityValidationException('amountRoom',\get_class($this),'ilegal_value');
        else*/
            $this->amount_room = $amountRoom;

        return $this;
    }

    /**
     * Get amount_room
     *
     * @return integer
     */
    public function getAmountRoom()
    {
        return $this->amount_room;
    }

    /**
     * Set number_floor
     *
     * @param integer $numberFloor
     * @return Property
     */
    public function setNumberFloor($numberFloor)
    {
        /*if(empty($numberFloor))
            throw new EntityValidationException('numberFloor',\get_class($this),'is_null');
        elseif (!is_numeric($numberFloor))
            throw new EntityValidationException('numberFloor',\get_class($this),'ilegal_value');
        elseif ($numberFloor<1)
            throw new EntityValidationException('numberFloor',\get_class($this),'ilegal_value');
        else*/
            $this->number_floor = $numberFloor;

        return $this;
    }

    /**
     * Get number_floor
     *
     * @return integer
     */
    public function getNumberFloor()
    {
        return $this->number_floor;
    }

    /**
     * Set check_in
     *
     * @param \DateTime $checkIn
     * @return Property
     */
    public function setCheckIn(\DateTime $checkIn = null)
    {
        $this->check_in = $checkIn;
        return $this;
    }

    /**
     * Get check_in
     *
     * @return \DateTime
     */
    public function getCheckIn()
    {
        return $this->check_in;
    }

    /**
     * Set check_out
     *
     * @param \DateTime $checkOut
     * @return Property
     */
    public function setCheckOut(\DateTime  $checkOut = null)
    {
        $this->check_out = $checkOut;

        return $this;
    }

    /**
     * Get check_out
     *
     * @return \DateTime
     */
    public function getCheckOut()
    {
        return $this->check_out;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Property
     */
    public function setDescription($description)
    {
        /*if(empty($description))
            throw new EntityValidationException('description',\get_class($this),'is_null');
        else*/
            $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set phones
     *
     * @param array $phones
     * @return Property
     */
    public function setPhones(Phone $phones)
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
     * @return Property
     */
    public function setFax(Phone $fax = null)
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
     * Set emails
     *
     * @param string $emails
     * @return Property
     */
    public function setEmails(EmailAddress $emails = null)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return string
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Property
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set discount_rate
     *
     * @param float $discountRate
     * @return Property
     */
    public function setDiscountRate($discountRate)
    {
        /*if(empty($discountRate))
            throw new EntityValidationException('discountRate',\get_class($this),'is_null');
        elseif (!is_numeric($discountRate))
            throw new EntityValidationException('discountRate',\get_class($this),'ilegal_value');
        elseif ($discountRate<0 or $discountRate>1)
            throw new EntityValidationException('discountRate',\get_class($this),'ilegal_value');
        else*/
            $this->discount_rate = $discountRate;

        return $this;
    }

    /**
     * Get discount_rate
     *
     * @return float
     */
    public function getDiscountRate()
    {
        return $this->discount_rate;
    }

    /**
     * Set opening_year
     *
     * @param integer $openingYear
     * @return Property
     */
    public function setOpeningYear($openingYear)
    {
        /*if(empty($openingYear))
            throw new EntityValidationException('openingYear',\get_class($this),'is_null');
        elseif (!is_numeric($openingYear) or !is_integer($openingYear))
            throw new EntityValidationException('openingYear',\get_class($this),'ilegal_value');
        else*/
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
     * Set renewal_year
     *
     * @param integer $renewalYear
     * @return Property
     */
    public function setRenewalYear($renewalYear)
    {
        $this->renewal_year = $renewalYear;

        return $this;
    }

    /**
     * Get renewal_year
     *
     * @return integer
     */
    public function getRenewalYear()
    {
        return $this->renewal_year;
    }

    /**
     * Set public_areas_renewal_year
     *
     * @param integer $publicAreasRenewalYear
     * @return Property
     */
    public function setPublicAreasRenewalYear($publicAreasRenewalYear)
    {
        $this->public_areas_renewal_year = $publicAreasRenewalYear;

        return $this;
    }

    /**
     * Get public_areas_renewal_year
     *
     * @return integer
     */
    public function getPublicAreasRenewalYear()
    {
        return $this->public_areas_renewal_year;
    }

    /**
     * Set check_in_age
     *
     * @param integer $checkInAge
     * @return Property
     */
    public function setCheckInAge($checkInAge)
    {
        /*if(empty($checkInAge))
            throw new EntityValidationException('checkInAge',\get_class($this),'is_null');
        elseif (!is_numeric($checkInAge))
            throw new EntityValidationException('checkInAge',\get_class($this),'ilegal_value');
        else*/
            $this->check_in_age = $checkInAge;

        return $this;
    }

    /**
     * Get check_in_age
     *
     * @return integer
     */
    public function getCheckInAge()
    {
        return $this->check_in_age;
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return Property
     */
    public function setTax($tax)
    {
        $this->tax = !empty($tax);

        return $this;
    }

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set tax_rate
     *
     * @param float $taxRate
     * @return Property
     */
    public function setTaxRate($taxRate)
    {
            $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return float
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set additional_info
     *
     * @param string $additionalInfo
     * @return Property
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additional_info = $additionalInfo;

        return $this;
    }

    /**
     * Get additional_info
     *
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additional_info;
    }

    /**
     * Set accounting_contact_name
     *
     * @param string $accountingContactName
     * @return Property
     */
    public function setAccountingContactName($accountingContactName)
    {
        $this->accounting_contact_name = $accountingContactName;

        return $this;
    }

    /**
     * Set coordinates
     *
     * @param array $coordinates
     * @return Property
     */
    public function setCoordinates(Coordinate $coordinates)
    {
        $this->coordinates = $coordinates;

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
     * Set profile_image
     *
     * @param PropertyFavoriteImages $profileImage
     * @return Property
     */
    public function setProfileImage(PropertyFavoriteImages $profileImage = null)
    {
        $this->profile_image = $profileImage;
        return $this;
    }

    /**
     * Get profile_image
     *
     * @return PropertyFavoriteImages
     */
    public function getProfileImage()
    {
        return $this->profile_image;
    }

    /**
     * Add property_favorite_images
     *
     * @param PropertyFavoriteImages $propertyFavoriteImages
     * @return Property
     */
    public function addPropertyFavoriteImage(PropertyFavoriteImages $propertyFavoriteImages)
    {
        $this->property_favorite_images[] = $propertyFavoriteImages;
        return $this;
    }

    /**
     * Remove property_favorite_images
     *
     * @param PropertyFavoriteImages $propertyFavoriteImages
     */
    public function removePropertyFavoriteImage(PropertyFavoriteImages $propertyFavoriteImages)
    {
        $this->property_favorite_images->removeElement($propertyFavoriteImages);
    }

    /**
     * Get property_favorite_images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyFavoriteImages()
    {
        return $this->property_favorite_images;
    }

    /**
     * Set beds
     *
     * @param boolean $beds
     * @return Property
     */
    public function setBeds($beds)
    {
        $this->beds = $beds;

        return $this;
    }

    /**
     * Get beds
     *
     * @return boolean
     */
    public function getBeds()
    {
        return $this->beds;
    }

    /**
     * Set beds_additional_cost
     *
     * @param boolean $bedsAdditionalCost
     * @return Property
     */
    public function setBedsAdditionalCost($bedsAdditionalCost)
    {
        $this->beds_additional_cost = $bedsAdditionalCost;

        return $this;
    }

    /**
     * Get beds_additional_cost
     *
     * @return boolean
     */
    public function getBedsAdditionalCost()
    {
        return $this->beds_additional_cost;
    }

    /**
     * Set child
     *
     * @param boolean $child
     * @return Property
     */
    public function setChild($child)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return boolean
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set cribs_additional_cost
     *
     * @param boolean $cribsAdditionalCost
     * @return Property
     */
    public function setCribsAdditionalCost($cribsAdditionalCost)
    {
        $this->cribs_additional_cost = $cribsAdditionalCost;

        return $this;
    }

    /**
     * Get cribs_additional_cost
     *
     * @return boolean
     */
    public function getCribsAdditionalCost()
    {
        return $this->cribs_additional_cost;
    }

    /**
     * Set cribs_max
     *
     * @param integer $cribsMax
     * @return Property
     */
    public function setCribsMax($cribsMax)
    {
        $this->cribs_max = $cribsMax;

        return $this;
    }

    /**
     * Get cribs_max
     *
     * @return integer
     */
    public function getCribsMax()
    {
        return $this->cribs_max;
    }

    /**
     * Set cribs
     *
     * @param boolean $cribs
     * @return Property
     */
    public function setCribs($cribs)
    {
        $this->cribs = $cribs;

        return $this;
    }

    /**
     * Get cribs
     *
     * @return boolean
     */
    public function getCribs()
    {
        return $this->cribs;
    }

    /**
     * Set pets
     *
     * @param boolean $pets
     * @return Property
     */
    public function setPets($pets)
    {
        $this->pets = $pets;
        return $this;
    }

    /**
     * Get pets
     *
     * @return boolean
     */
    public function getPets()
    {
        return $this->pets;
    }

    /**
     * Set pets_additional_cost
     *
     * @param boolean $petsAdditionalCost
     * @return Property
     */
    public function setPetsAdditionalCost($petsAdditionalCost)
    {
        $this->pets_additional_cost = $petsAdditionalCost;

        return $this;
    }

    /**
     * Get pets_additional_cost
     *
     * @return boolean
     */
    public function getPetsAdditionalCost()
    {
        return $this->pets_additional_cost;
    }

    /**
     * Set cash
     *
     * @param boolean $cash
     * @return Property
     */
    public function setCash($cash)
    {
        $this->cash = $cash;

        return $this;
    }

    /**
     * Get cash
     *
     * @return boolean
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * Set max_cash
     *
     * @param float $maxCash
     * @return Property
     */
    public function setMaxCash($maxCash)
    {
        $this->max_cash = $maxCash;

        return $this;
    }

    /**
     * Get max_cash
     *
     * @return float
     */
    public function getMaxCash()
    {
        return $this->max_cash;
    }

    /**
     * Set city_tax
     *
     * @param float $cityTax
     * @return Property
     */
    public function setCityTax($cityTax)
    {
        $this->city_tax = $cityTax;

        return $this;
    }

    /**
     * Get city_tax
     *
     * @return float
     */
    public function getCityTax()
    {
        return $this->city_tax;
    }

    /**
     * Set city_tax_type
     *
     * @param integer $cityTaxType
     * @return Property
     */
    public function setCityTaxType($cityTaxType)
    {
        $this->city_tax_type = $cityTaxType;

        return $this;
    }

    /**
     * Get city_tax_type
     *
     * @return integer
     */
    public function getCityTaxType()
    {
        return $this->city_tax_type;
    }

    /**
     * Set city_tax_max_nights
     *
     * @param integer $cityTaxMaxNights
     * @return Property
     */
    public function setCityTaxMaxNights($cityTaxMaxNights)
    {
        $this->city_tax_max_nights = $cityTaxMaxNights;

        return $this;
    }

    /**
     * Get city_tax_max_nights
     *
     * @return integer
     */
    public function getCityTaxMaxNights()
    {
        return $this->city_tax_max_nights;
    }

    /**
     * Set credit_card
     *
     * @param boolean $creditCard
     * @return Property
     */
    public function setCreditCard($creditCard)
    {
        $this->credit_card = $creditCard;

        return $this;
    }

    /**
     * Get credit_card
     *
     * @return boolean
     */
    public function getCreditCard()
    {
        return $this->credit_card;
    }

    /**
     * Set credit_card_amex
     *
     * @param boolean $creditCardAmex
     * @return Property
     */
    public function setCreditCardAmex($creditCardAmex)
    {
        $this->credit_card_amex = $creditCardAmex;

        return $this;
    }

    /**
     * Get credit_card_amex
     *
     * @return boolean
     */
    public function getCreditCardAmex()
    {
        return $this->credit_card_amex;
    }

    /**
     * Set credit_card_mc
     *
     * @param boolean $creditCardMc
     * @return Property
     */
    public function setCreditCardMc($creditCardMc)
    {
        $this->credit_card_mc = $creditCardMc;

        return $this;
    }

    /**
     * Get credit_card_mc
     *
     * @return boolean
     */
    public function getCreditCardMc()
    {
        return $this->credit_card_mc;
    }

    /**
     * Set credit_card_visa
     *
     * @param boolean $creditCardVisa
     * @return Property
     */
    public function setCreditCardVisa($creditCardVisa)
    {
        $this->credit_card_visa = $creditCardVisa;

        return $this;
    }

    /**
     * Get credit_card_visa
     *
     * @return boolean
     */
    public function getCreditCardVisa()
    {
        return $this->credit_card_visa;
    }

    /**
     * Set base_policy
     *
     * @param PropertyCancellationPolicy $basePolicy
     * @return Property
     */
    public function setBasePolicy(PropertyCancellationPolicy $basePolicy = null)
    {
        $this->base_policy = $basePolicy;

        return $this;
    }

    /**
     * Get base_policy
     *
     * @return PropertyCancellationPolicy
     */
    public function getBasePolicy()
    {
        return $this->base_policy;
    }

    /**
     * Add services
     *
     * @param PropertyService $services
     * @return Property
     */
    public function addService(PropertyService $services)
    {
        $this->services[] = $services;

        return $this;
    }

    /**
     * Remove services
     *
     * @param PropertyService $services
     */
    public function removeService(PropertyService $services)
    {
        $this->services->removeElement($services);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Add rooms
     *
     * @param Room $rooms
     * @return Property
     */
    public function addRoom(Room $rooms)
    {
        $this->rooms[] = $rooms;

        return $this;
    }

    /**
     * Remove rooms
     *
     * @param Room $rooms
     */
    public function removeRoom(Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Add property_cancellation_policies
     *
     * @param PropertyCancellationPolicy $propertyCancellationPolicies
     * @return Property
     */
    public function addPropertyCancellationPolicy(PropertyCancellationPolicy $propertyCancellationPolicies)
    {
        $this->property_cancellation_policies[] = $propertyCancellationPolicies;

        return $this;
    }

    /**
     * Remove property_cancellation_policies
     *
     * @param PropertyCancellationPolicy $propertyCancellationPolicies
     */
    public function removePropertyCancellationPolicy(PropertyCancellationPolicy $propertyCancellationPolicies)
    {
        $this->property_cancellation_policies->removeElement($propertyCancellationPolicies);
    }

    /**
     * Get property_cancellation_policies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyCancellationPolicies()
    {
        return $this->property_cancellation_policies;
    }

    /**
     * Add property_gallery
     *
     * @param PropertyGallery $propertyGallery
     * @return Property
     */
    public function addPropertyGallery(PropertyGallery $propertyGallery)
    {
        $this->property_gallery[] = $propertyGallery;

        return $this;
    }

    /**
     * Remove property_gallery
     *
     * @param PropertyGallery $propertyGallery
     */
    public function removePropertyGallery(PropertyGallery $propertyGallery)
    {
        $this->property_gallery->removeElement($propertyGallery);
    }
    /**
     * Get property_gallery
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyGallery()
    {
        return $this->property_gallery;
    }

    /**
     * Set location
     *
     * @param Location $location
     * @return Property
     */
    public function setLocation(Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set accommodation
     *
     * @param Accommodation $accommodation
     * @return Property
     */
    public function setAccommodation(Accommodation $accommodation = null)
    {
        $this->accommodation = $accommodation;

        return $this;
    }

    /**
     * Get accommodation
     *
     * @return Accommodation
     */
    public function getAccommodation()
    {
        return $this->accommodation;
    }

    /**
     * Set currency
     *
     * @param Category $currency
     * @return Property
     */
    public function setCurrency(CurrencyType $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return Category
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set city_tax_currency
     *
     * @param Category $cityTaxCurrency
     * @return Property
     */
    public function setCityTaxCurrency(CurrencyType $cityTaxCurrency = null)
    {
        $this->city_tax_currency = $cityTaxCurrency;

        return $this;
    }

    /**
     * Get city_tax_currency
     *
     * @return Category
     */
    public function getCityTaxCurrency()
    {
        return $this->city_tax_currency;
    }

    /**
     * Add owners_profiles
     *
     * @param OwnerProfile $ownersProfiles
     * @return Property
     */
    public function addOwnersProfile(OwnerProfile $ownersProfiles)
    {
        $this->owners_profiles[] = $ownersProfiles;

        return $this;
    }

    /**
     * Remove owners_profiles
     *
     * @param OwnerProfile $ownersProfiles
     */
    public function removeOwnersProfile(OwnerProfile $ownersProfiles)
    {
        $this->owners_profiles->removeElement($ownersProfiles);
    }

    /**
     * Get owners_profiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnersProfiles()
    {
        return $this->owners_profiles;
    }

    /**
     * Add languages
     *
     * @param Language $languages
     * @return Property
     */
    public function addLanguage(Language $languages)
    {
        $this->languages[] = $languages;

        return $this;
    }

    /**
     * Remove languages
     *
     * @param Language $languages
     */
    public function removeLanguage(Language $languages)
    {
        $this->languages->removeElement($languages);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add contacts
     *
     * @param ContactPerson $contacts
     * @return Property
     */
    public function addContact(ContactPerson $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param ContactPerson $contacts
     */
    public function removeContact(ContactPerson $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set hotel_chain_name
     *
     * @param string $hotelChainName
     * @return Property
     */
    public function setHotelChainName($hotelChainName)
    {
        $this->hotel_chain_name = $hotelChainName;

        return $this;
    }

    /**
     * Get hotel_chain_name
     *
     * @return string
     */
    public function getHotelChainName()
    {
        return $this->hotel_chain_name;
    }

    /**
     * Set all_included
     *
     * @param boolean $allIncluded
     * @return Property
     */
    public function setAllIncluded($allIncluded)
    {
        $this->all_included = $allIncluded;

        return $this;
    }

    /**
     * Get all_included
     *
     * @return boolean
     */
    public function getAllIncluded()
    {
        return $this->all_included;
    }

    /**
     * Set debit
     *
     * @param boolean $debit
     * @return Property
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return boolean
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set comercial_rooms
     *
     * @param integer $comercialRooms
     * @return Property
     */
    public function setComercialRooms($comercialRooms)
    {
        $this->comercial_rooms = $comercialRooms;

        return $this;
    }

    /**
     * Get comercial_rooms
     *
     * @return integer
     */
    public function getComercialRooms()
    {
        return $this->comercial_rooms;
    }
    /**
     * Set payment_info
     *
     * @param \Navicu\Core\Domain\Model\Entity\PaymentInfoProperty $paymentInfo
     * @return Property
     */
    public function setPaymentInfo(PaymentInfoProperty $paymentInfo = null)
    {
        $this->payment_info = $paymentInfo;

        return $this;
    }

    /**
     * Get payment_info
     *
     * @return \Navicu\Core\Domain\Model\Entity\PaymentInfoProperty
     */
    public function getPaymentInfo()
    {
        return $this->payment_info;
    }

    /**
     * Set agreement
     *
     * @param \Navicu\Core\Domain\Model\Entity\Agreement $agreement
     * @return Property
     */
    public function setAgreement(Agreement $agreement = null)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return \Navicu\Core\Domain\Model\Entity\Agreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set prominet
     *
     * @param boolean $prominet
     * @return Property
     */
    public function setProminent($prominet)
    {
        $this->prominent = $prominet;

        return $this;
    }

    /**
     * Get prominent
     *
     * @return boolean
     */
    public function getProminent()
    {
        return $this->prominent;
    }

    /**
     * Add logs_owners
     *
     * @param \Navicu\Core\Domain\Model\Entity\LogsOwner $logsOwners
     * @return Property
     */
    public function addLogsOwner(LogsOwner $logsOwners)
    {
        $this->logs_owners[] = $logsOwners;

        return $this;
    }

    /**
     * Remove logs_owners
     *
     * @param \Navicu\Core\Domain\Model\Entity\LogsOwner $logsOwners
     */
    public function removeLogsOwner(LogsOwner $logsOwners)
    {
        $this->logs_owners->removeElement($logsOwners);
    }

    /**
     * Get logs_owners
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogsOwners()
    {
        return $this->logs_owners;
    }

    public function validate()
    {
        return true;
    }

    /**
     *   formatea un string y devuelve un slug valido
     */
    public function generateSlug()
    {
        $slug = $this->name;

        //reemplazando caracteres especiales por simples
        $slug = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $slug
        );

        $slug = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $slug
        );

        $slug = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $slug
        );

        $slug = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô','Õ','Ø'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O','O','O'),
            $slug
        );

        $slug = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $slug
        );

        $slug = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç','Š','š','Ž','ž','Ý','ý','ÿ'),
            array('n', 'N', 'c', 'C','S','s','Z','z','Y','y','y'),
            $slug
        );

        //convirtiendo todo a minusculas
        $slug = strtolower($slug);

        //eliminando el resto de los caracteres especiales
        $slug = preg_replace('/[^a-zA-Z0-9\s]/i','', $slug);

        //limpiando espacios en blancos en los extremos
        $slug = trim($slug);

        // Rellenamos espacios con guiones
        $slug = preg_replace('/\s+/', '-', $slug);

        $this->slug = $slug;
    }

    public function generatePublicId()
    {
        $loc = $this->location;
        while ( $loc->getLvl() != 0 )
            $loc = $loc->getParent();
        $this->public_id = $loc->getId()."-".str_pad($this->getId(), 4, "0", STR_PAD_LEFT);

    }

    /**
     * Funcion que cambia los nombres de las carpetas que contienen las imagenes
     * que pertenecen al establecimiento
     * @author Alejandro Conde <adcs2008@gmail.com>
     * @return array
     */
    public function renameFolders($oldSlug)
    {
        $newSlug = $this->slug;
        $webPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
        $documentPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/documents/';

        system("mv ".$webPath.'images_original/property/'.$oldSlug.' '. $webPath.'images_original/property/'.$newSlug );

        system("mv ".$webPath.'images_md/property/'.$oldSlug.' '. $webPath.'images_md/property/'.$newSlug );

        system("mv ".$webPath.'images_sm/property/'.$oldSlug.' '. $webPath.'images_sm/property/'.$newSlug );

        system("mv ".$webPath.'images_xs/property/'.$oldSlug.' '. $webPath.'images_xs/property/'.$newSlug );

        system("mv ".$documentPath.$oldSlug.' '. $documentPath.$newSlug );
    }

    public function renameFoldersFromConsole($basePath,$oldSlug)
    {
        $newSlug = $this->slug;
        $webPath = $basePath.'/uploads/images/';
        $documentPath = $basePath.'/uploads/documents/';

        //system("mkdir ". $webPath.'images_original/property/'.$newSlug);
        system("mv ".$webPath.'images_original/property/'.$oldSlug.'/ '. $webPath.'images_original/property/'.$newSlug.'/' );

        //system("mkdir ". $webPath.'images_md/property/'.$newSlug);
        system("mv ".$webPath.'images_md/property/'.$oldSlug.'/ '. $webPath.'images_md/property/'.$newSlug.'/');

        //system("mkdir ". $webPath.'images_sm/property/'.$newSlug);
        system("mv ".$webPath.'images_sm/property/'.$oldSlug.'/ '. $webPath.'images_sm/property/'.$newSlug.'/' );

        //system("mkdir ". $webPath.'images_xs/property/'.$newSlug);
        system("mv ".$webPath.'images_xs/property/'.$oldSlug.'/ '. $webPath.'images_xs/property/'.$newSlug.'/' );

        //system("mkdir ". $documentPath.$newSlug);
        system("mv ".$documentPath.$oldSlug.'/ '. $documentPath.$newSlug.'/' );
    }

    /**
     * Devuelve una representacion del esteblecimiento en array
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     */
    public function toArray()
    {
        $res=array();
        $res['id']= $this->id;
        $res['slug']= $this->slug;
        $res['name'] = $this->name;
        $res['address'] =  $this->address;
        $res['star'] =  $this->star;
        $res['url_web'] =  !empty($this->url_web) ? $this->getUrlWeb()->toString() : null;
        $res['amount_room'] = $this->amount_room;
        $res['number_floor'] = $this->number_floor;
        $res['check_in'] = $this->check_in;
        $res['check_out'] = $this->check_out;
        $res['description'] = $this->description;
        $res['phones'] = !empty($this->phones) ? $this->getPhones()->toString() : null;
        $res['fax'] = !empty($this->fax) ? $this->getFax()->toString() : null;
        $res['emails'] = !empty($this->emails) ? $this->getEmails()->toString() : null;
        $res['rating'] = $this->rating;
        $res['discount_rate'] = $this->discount_rate;
        $res['opening_year'] = $this->opening_year;
        $res['renewal_year'] = $this->renewal_year;
        $res['public_areas_renewal_year'] = $this->public_areas_renewal_year;
        $res['check_in_age'] = $this->check_in_age;
        $res['tax'] = $this->tax;
        $res['tax_rate'] = $this->tax_rate;
        $res['additional_info'] = $this->additional_info;
        $res['description'] = $this->description;
        $res['all_included'] = $this->all_included;
        $res['debit'] = $this->debit;
        $res['contacts'] = array();
        if(!empty($this->contacts)) {
            foreach($this->contacts as $contact) {
                array_push(
                    $res['contacts'],
                    $contact->toArray()
                );
            }
        }
        $res['hotel_chain_name'] = $this->hotel_chain_name;
        $res['rate_type'] = $this->rate_type;
        $res['basic_quota'] = $this->basic_quota;
        $res['coordinates']= !empty($this->coordinates) ? $this->getCoordinates()->toArray() : null;
        $res['accommodation']= !empty($this->accommodation) ? $this->accommodation->getId() : null;
        $res['location'] = !empty($this->location) ? $this->location->getId() : null;
        $res['currency'] = !empty($this->currency) ? $this->currency->getId() : null;
        if(!empty($this->beds)){
            $res['beds'] = array(
                'beds_additional_cost'=>$this->beds_additional_cost,
                'beds_prior_notice'=>$this->beds_prior_notice,
            );
        }else{
            $res['beds']=false;
        }
        $res['child'] = $this->child;
        $res['agePolicy'] = $this->age_policy;
        if($this->cribs){
            $res['cribs'] = array(
                'cribs_additional_cost' => $this->cribs_additional_cost,
                'cribs_max' => $this->cribs_max,
                'cribs_prior_notice' => $this->cribs_prior_notice
            );
        }else{
            $res['cribs'] = false;
        }

        //if(!empty($this->pets)){
        $res['pets']=array('pets'=>$this->pets,'pets_additional_cost'=>$this->pets_additional_cost);
        //}else{
        //$res['pets']=false;
        //}
        //if(!empty($this->cash)){
        $res['cash']=array('cash'=>$this->cash,'max_cash'=>$this->max_cash);
        //}else{
        //$res['cash']=false;
        //}
        $res['city_tax'] = $this->city_tax;
        $res['city_tax_currency'] = !empty($this->city_tax_currency) ? $this->city_tax_currency->getId() : null;
        $res['city_tax_type'] = $this->city_tax_type;
        $res['city_tax_max_nights'] = $this->city_tax_max_nights;
        $res['tax'] = $this->tax;
        $res['tax_rate'] = $this->tax_rate;
        $res['comercial_rooms'] = $this->comercial_rooms;
        $res['design_view_property'] = $this->design_view_property;
        if(!empty($this->credit_card)){
            $res['credit_card'] = array(
                'credit_card_american' => $this->credit_card_amex,
                'credit_card_master' => $this->credit_card_mc,
                'credit_card_visa' => $this->credit_card_visa
            );
        }else{
            $res['credit_card'] = false;
        }

        $res['language'] = array();
        foreach($this->languages as $language)
            array_push($res['language'],$language->getId());
        return $res;
    }

    /**
     * Metodo usado para ajustar los valores del estableciemiento a una representacion almacenable
     * @author Gabriel Casmacho <kbo025@gmail.com>
     */
    public function prePersist()
    {

        if( isset($this->url_web) and  $this->url_web instanceof Url ) {
            $this->url_web=$this->url_web->toString();
        }

        if( isset($this->emails) and $this->emails instanceof EmailAddress ) {
            $this->emails=$this->emails->toString();
        }

        if( isset($this->phones) and $this->phones instanceof Phone ) {
            $this->phones=$this->phones->toString();
        }

        if( isset($this->fax) and  $this->fax instanceof Phone ) {
            $this->fax=$this->fax->toString();
        }

        if( isset($this->coordinates) and $this->coordinates instanceof Coordinate ) {
            $this->coordinates=$this->coordinates->toArray();
        }
        $this->generateSlug();
    }

    /**
     * Set rate_type
     *
     * @param integer $rateType
     * @return Property
     */
    public function setRateType($rateType)
    {
        $this->rate_type = $rateType;

        return $this;
    }

    /**
     * Get rate_type
     *
     * @return integer
     */
    public function getRateType()
    {
        return $this->rate_type;
    }

    /**
     * Set beds_prior_notice
     *
     * @param boolean $bedsPriorNotice
     * @return Property
     */
    public function setBedsPriorNotice($bedsPriorNotice)
    {
        $this->beds_prior_notice = $bedsPriorNotice;

        return $this;
    }

    /**
     * Get beds_prior_notice
     *
     * @return boolean
     */
    public function getBedsPriorNotice()
    {
        return $this->beds_prior_notice;
    }

    /**
     * Set cribs_prior_notice
     *
     * @param boolean $cribsPriorNotice
     * @return Property
     */
    public function setCribsPriorNotice($cribsPriorNotice)
    {
        $this->cribs_prior_notice = $cribsPriorNotice;

        return $this;
    }

    /**
     * Get cribs_prior_notice
     *
     * @return boolean
     */
    public function getCribsPriorNotice()
    {
        return $this->cribs_prior_notice;
    }

    /**
     * Set basic_quota
     *
     * @param integer $basicQuota
     * @return Property
     */
    public function setBasicQuota($basicQuota)
    {
        $this->basic_quota = $basicQuota;

        return $this;
    }

    /**
     * Get basic_quota
     *
     * @return integer
     */
    public function getBasicQuota()
    {
        return $this->basic_quota;
    }

    /**
     * Add reservations
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservations
     * @return Property
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Set join_date
     *
     * @param \DateTime $joinDate
     * @return Property
     */
    public function setJoinDate($joinDate)
    {
        $this->join_date = $joinDate;

        return $this;
    }

    /**
     * Get join_date
     *
     * @return \DateTime
     */
    public function getJoinDate()
    {
        return $this->join_date;
    }

    /**
     * Set registration_date
     *
     * @param \DateTime $registrationDate
     * @return Property
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registration_date = $registrationDate;

        return $this;
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
     * Set design_view_property
     *
     * @param integer $designViewProperty
     * @return Property
     */
    public function setDesignViewProperty($designViewProperty)
    {
        $this->design_view_property = $designViewProperty;

        return $this;
    }

    /**
     * Get design_view_property
     *
     * @return integer
     */
    public function getDesignViewProperty()
    {
        return $this->design_view_property;
    }

    /**
     * Set commercial_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\CommercialProfile $commercialProfile
     * @return Property
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
     * Set age_policy
     *
     * @param array $agePolicy
     * @return Property
     */
    public function setAgePolicy($adult=null,$child=null,$baby=null)
    {
        if (isset($adult)) {
            $this->age_policy = [
                'adult' => $adult,
                'child' => isset($child) ? $child : false,
                'baby' => isset($baby) ? $baby : false,
            ];
        } else {
            $this->age_policy = null;
        }
        return $this;
    }

    /**
     * Add logs_users
     *
     * @param \Navicu\Core\Domain\Model\Entity\LogsUser $logsUsers
     * @return Property
     */
    public function addLogsUser(LogsUser $logsUsers)
    {
        $this->logs_users[] = $logsUsers;

        return $this;
    }

    /**
     * Get age_policy
     *
     * @return array
     */
    public function getAgePolicy()
    {
        return $this->age_policy;
    }
    /**
     * Remove logs_users
     *
     * @param \Navicu\Core\Domain\Model\Entity\LogsUser $logsUsers
     */
    public function removeLogsUser(LogsUser $logsUsers)
    {
        $this->logs_users->removeElement($logsUsers);
    }

    /**
     * Get logs_users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogsUsers()
    {
        return $this->logs_users;
    }

    /**
     * Set nvc_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile
     * @return Property
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

    /////////////////////////esto se debe eliminar luego de la migracion de precios por niños en produccion


    /**
     * @var integer
     */
    private $child_max_age;

    /**
     * @var integer
     */
    private $adult_age;


    /**
     * Set child_max_age
     *
     * @param integer $childMaxAge
     * @return Property
     */
    public function setChildMaxAge($childMaxAge)
    {
        $this->child_max_age = $childMaxAge;

        return $this;
    }

    /**
     * Get child_max_age
     *
     * @return integer
     */
    public function getChildMaxAge()
    {
        return $this->child_max_age;
    }

    /**
     * Set adult_age
     *
     * @param integer $adultAge
     * @return Property
     */
    public function setAdultAge($adultAge)
    {
        $this->adult_age = $adultAge;

        return $this;
    }

    /**
     * Get adult_age
     *
     * @return integer
     */
    public function getAdultAge()
    {
        return $this->adult_age;
    }

    /**
     * Set recruit
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfile $recruit
     * @return Property
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

    /**
     * Set featured_home
     *
     * @param boolean $featuredHome
     * @return Property
     */
    public function setFeaturedHome($featuredHome)
    {
        $this->featured_home = $featuredHome;

    }

     /* Set unpublished_date
     *
     * @param \DateTime $unpublishedDate
     * @return Property
     */
    public function setUnpublishedDate($unpublishedDate)
    {
        $this->unpublished_date = $unpublishedDate;

        return $this;
    }

    /**
     * Get featured_home
     *
     * @return boolean
     */
    public function getFeaturedHome()
    {
        return $this->featured_home;
    }

    /**
     * Set promo_home
     *
     * @param boolean $promoHome
     * @return Property
     */
    public function setPromoHome($promoHome)
    {
        $this->promo_home = $promoHome;

        return $this;
    }

    /**
     * Get promo_home
     *
     * @return boolean
     */
    public function getPromoHome()
    {
        return $this->promo_home;
    }

    /**
     * Set featured_location
     *
     * @param string $featuredLocation
     * @return Property
     */
    public function setFeaturedLocation($featuredLocation)
    {
        $this->featured_location = $featuredLocation;

        return $this;
    }

    /**
     * Get featured_location
     *
     * @return string
     */
    public function getFeaturedLocation()
    {
        return $this->featured_location;
    }

    /* Get unpublished_date
     *
     * @return \DateTime
     */
    public function getUnpublishedDate()
    {
        return $this->unpublished_date;
    }
}
