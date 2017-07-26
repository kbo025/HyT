<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\Entity\Bedroom;
use Navicu\Core\Domain\Model\Entity\Livingroom;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Model\Entity\RoomFeature;
use Navicu\Core\Domain\Model\Entity\RoomLinkage;
use Navicu\Core\Domain\Model\Entity\RoomPackLinkage;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Domain\Model\Entity\RateByPeople;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Model\Entity\PackLinkage;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\RoomType;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Symfony\Component\Finder\SplFileInfo;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
 * Clase Room.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las habitaciones de un establecimiento.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Room
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * @var float
     */
    protected $low_rate;

    /**
     * Esta propiedad es usada para interactuar con la disponibilidad base por Habitación.
     * 
     * @var Integer
     */
    protected $base_availability;

    /**
     * Esta propiedad es usada para interactuar con el minimo de personas por Habitación.
     * 
     * @var Integer
     */
    protected $min_people;

    /**
     * Esta propiedad es usada para interactuar con el maximo de personas por Habitación.
     * 
     * @var Integer
     */
    protected $max_people;

    /**
     * Esta propiedad es usada para interactuar con el tipo de monto por persona de una Habitación.
     * 
     * @var Integer
     */
    protected $variation_type_people;

    /**
     * Esta propiedad es usada para interactuar con el tipo de monto por niño de una Habitación.
     * 
     * @var Integer
     */
    protected $variation_type_kids;

    /**
     * @var integer
     */
    protected $amount_rooms;

    /**
     * @var boolean
     */
    protected $smoking_policy;

    /**
     * @var float
     */
    protected $max_price_person;

    /**
     * @var float
     */
    protected $min_price_person;

    /**
     * @var array
     */
    protected $bedrooms;

    /**
     * @var array
     */
    protected $livingrooms;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $size;

    /**
     * @var RoomImagesGallery
     */
    protected $profile_image;

    /**
     * @var ArrayCollection
     */
    protected $features;

    /**
     * @var ArrayCollection
     */
    protected $i_am_child;

    /**
     * @var ArrayCollection
     */
    protected $i_am_parent;

    /**
     * @var ArrayCollection
     */
    protected $parent_room_pack;

    /**
     * @var ArrayCollection
     */
    protected $daily_rooms;

    /**
     * @var ArrayCollection
     */
    protected $rates_by_peoples;

    /**
     * @var ArrayCollection
     */
    protected $rates_by_kids;

    /**
     * @var ArrayCollection
     */
    protected $packages;

    /**
     * @var ArrayCollection
     */
    protected $packages_linkage;

    /**
     * @var ArrayCollection
     */
    protected $images_gallery;

    /**
     * @var Property
     */
    protected $property;

    /**
     * @var RoomType
     */
    protected $type;

    /**
    * @var boolean
    */
    protected $unique_type;

    /**
    * @var boolean
    */
    protected $is_active;

    /**
    * esta variable es usada para determinar si el usuario usa un incremento por cantidad de personas (no se persiste)
    * @var boolean
    */
    protected $increment;

    /**
     * Existe incremento de tarifas por niños
     * @var boolean
     */
    protected $increment_kid = false;

    /**
     * indica si las politicas de aumneto por niño son iguales a las politicas de aumento por adultos
     *
     * @var boolean
     */
    private $kid_pay_as_adult = true;

    /**
     * indica si será el mismo incremento para todas las cantidades de personas permitidas en la habitacion
     *
     * @var boolean
     */
    private $same_increment_adult = true;

    /**
     * indica si será el mismo incremento para todas las cantidades de niñor permitidos en la habitación
     *
     * @var boolean
     */
    private $same_increment_kid = true;

    /**
     * @var string
     */
    private $slug;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->increment = false;
        $this->unique_type = true;
        $this->is_active = true;
        $this->livingrooms = new ArrayCollection();
        $this->bedrooms = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->i_am_child = new ArrayCollection();
        $this->i_am_parent = new ArrayCollection();
        $this->parent_room_pack = new ArrayCollection();
        $this->daily_rooms = new ArrayCollection();
        $this->rates_by_peoples = new ArrayCollection();
        $this->rates_by_kids = new ArrayCollection();
        $this->packages = new ArrayCollection();
        $this->packages_linkage = new ArrayCollection();
        $this->images_gallery = new ArrayCollection();
        $this->variation_type_kids = [];
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
     * Set low_rate
     *
     * @param float $lowRate
     * @return Room
     */
    public function setLowRate($lowRate)
    {
        $this->low_rate = $lowRate;

        return $this;
    }

    /**
     * Get low_rate
     *
     * @return float
     */
    public function getLowRate()
    {
        return $this->low_rate;
    }

    /**
     * Set base_availability
     *
     * @param integer $baseAvailability
     * @return Room
     */
    public function setBaseAvailability($baseAvailability)
    {
        $this->base_availability = $baseAvailability;

        return $this;
    }

    /**
     * Get base_availability
     *
     * @return integer
     */
    public function getBaseAvailability()
    {
        return $this->base_availability;
    }

    /**
     * Set min_people
     *
     * @param integer $minPeople
     * @return Room
     */
    public function setMinPeople($minPeople)
    {
        $this->min_people = $minPeople;

        return $this;
    }

    /**
     * Get min_people
     *
     * @return integer
     */
    public function getMinPeople()
    {
        return $this->min_people;
    }

    /**
     * Set max_people
     *
     * @param integer $maxPeople
     * @return Room
     */
    public function setMaxPeople($maxPeople)
    {
        $this->max_people = $maxPeople;

        return $this;
    }

    /**
     * Get max_people
     *
     * @return integer
     */
    public function getMaxPeople()
    {
        return $this->max_people;
    }

    /**
     * Set variation_type_people
     *
     * @param integer $variationTypePeople
     * @return Room
     */
    public function setVariationTypePeople($variationTypePeople)
    {
        $this->variation_type_people = $variationTypePeople;

        return $this;
    }

    /**
     * Get variation_type_people
     *
     * @return integer
     */
    public function getVariationTypePeople()
    {
        return $this->variation_type_people;
    }

    /**
     * Set amount_rooms
     *
     * @param integer $amountRooms
     * @return Room
     */
    public function setAmountRooms($amountRooms)
    {
        $this->amount_rooms = $amountRooms;

        return $this;
    }

    /**
     * Get amount_rooms
     *
     * @return integer
     */
    public function getAmountRooms()
    {
        return $this->amount_rooms;
    }

    /**
     * Set smoking_policy
     *
     * @param boolean $smokingPolicy
     * @return Room
     */
    public function setSmokingPolicy($smokingPolicy)
    {
        $this->smoking_policy = $smokingPolicy;

        return $this;
    }

    /**
     * Get smoking_policy
     *
     * @return boolean
     */
    public function getSmokingPolicy()
    {
        return $this->smoking_policy;
    }

    /**
     * Set max_price_person
     *
     * @param float $maxPricePerson
     * @return Room
     */
    public function setMaxPricePerson($maxPricePerson)
    {
        $this->max_price_person = $maxPricePerson;

        return $this;
    }

    /**
     * Get max_price_person
     *
     * @return float
     */
    public function getMaxPricePerson()
    {
        return $this->max_price_person;
    }

    /**
     * Set min_price_person
     *
     * @param float $minPricePerson
     * @return Room
     */
    public function setMinPricePerson($minPricePerson)
    {
        $this->min_price_person = $minPricePerson;

        return $this;
    }

    /**
     * Get min_price_person
     *
     * @return float
     */
    public function getMinPricePerson()
    {
        return $this->min_price_person;
    }

    /**
     * Get bedrooms
     *
     * @return array
     */
    public function getBedrooms()
    {
        return $this->bedrooms;
    }

    /**
     * Set livingrooms
     *
     * @param array $livingrooms
     * @return Room
     */
    public function setLivingrooms($livingrooms)
    {
        $this->livingrooms = $livingrooms;

        return $this;
    }

    /**
     * Get livingrooms
     *
     * @return array
     */
    public function getLivingrooms()
    {
        return $this->livingrooms;
    }

    /**
     * Get livingrooms
     *
     * @return array
     */
    public function addLivingroom(Livingroom $livingroom)
    {
        $this->livingrooms->add($livingroom);
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Room
     */
    public function setName($name)
    {
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
     * Set size
     *
     * @param float $size
     * @return Room
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**setName
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set profile_image
     *
     * @param RoomImagesGallery $profileImage
     * @return Room
     */
    public function setProfileImage(RoomImagesGallery $profileImage = null)
    {
        $this->profile_image = $profileImage;

        return $this;
    }



    /**
     * Get profile_image
     *
     * @return RoomImagesGallery
     */
    public function getProfileImage()
    {
        return $this->profile_image;
    }

    /**
     * Add features
     *
     * @param RoomFeature $features
     * @return Room
     */
    public function addFeature(RoomFeature $features)
    {
        $this->features[] = $features;

        return $this;
    }

    /**
     * Remove features
     *
     * @param RoomFeature $features
     */
    public function removeFeature(RoomFeature $features)
    {
        $this->features->removeElement($features);
    }

    /**
     * Get features
     *
     * @return ArrayCollection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Add i_am_child
     *
     * @param RoomLinkage $iAmChild
     * @return Room
     */
    public function addIAmChild(RoomLinkage $iAmChild)
    {
        $this->i_am_child[] = $iAmChild;

        return $this;
    }

    /**
     * Remove i_am_child
     *
     * @param RoomLinkage $iAmChild
     */
    public function removeIAmChild(RoomLinkage $iAmChild)
    {
        $this->i_am_child->removeElement($iAmChild);
    }

    /**
     * Get i_am_child
     *
     * @return ArrayCollection
     */
    public function getIAmChild()
    {
        return $this->i_am_child;
    }

    /**
     * Add i_am_parent
     *
     * @param RoomLinkage $iAmParent
     * @return Room
     */
    public function addIAmParent(RoomLinkage $iAmParent)
    {
        $this->i_am_parent[] = $iAmParent;

        return $this;
    }

    /**
     * Remove i_am_parent
     *
     * @param RoomLinkage $iAmParent
     */
    public function removeIAmParent(RoomLinkage $iAmParent)
    {
        $this->i_am_parent->removeElement($iAmParent);
    }

    /**
     * Get i_am_parent
     *
     * @return ArrayCollection
     */
    public function getIAmParent()
    {
        return $this->i_am_parent;
    }

    /**
     * Add parent_room_pack
     *
     * @param RoomPackLinkage $parentRoomPack
     * @return Room
     */
    public function addParentRoomPack(RoomPackLinkage $parentRoomPack)
    {
        $this->parent_room_pack[] = $parentRoomPack;

        return $this;
    }

    /**
     * Remove parent_room_pack
     *
     * @param RoomPackLinkage $parentRoomPack
     */
    public function removeParentRoomPack(RoomPackLinkage $parentRoomPack)
    {
        $this->parent_room_pack->removeElement($parentRoomPack);
    }

    /**
     * Get parent_room_pack
     *
     * @return ArrayCollection
     */
    public function getParentRoomPack()
    {
        return $this->parent_room_pack;
    }

    /**
     * Add daily_rooms
     *
     * @param DailyRoom $dailyRooms
     * @return Room
     */
    public function addDailyRoom(DailyRoom $dailyRooms)
    {
        $this->daily_rooms[] = $dailyRooms;

        return $this;
    }

    /**
     * Remove daily_rooms
     *
     * @param DailyRoom $dailyRooms
     */
    public function removeDailyRoom(DailyRoom $dailyRooms)
    {
        $this->daily_rooms->removeElement($dailyRooms);
    }

    /**
     * Get daily_rooms
     *
     * @return ArrayCollection
     */
    public function getDailyRooms()
    {
        return $this->daily_rooms;
    }

    /**
     * Add rates_by_peoples
     *
     * @param RateByPeople $ratesByPeoples
     * @return Room
     */
    public function addRatesByPeople(RateByPeople $ratesByPeoples)
    {
        $this->rates_by_peoples[] = $ratesByPeoples;

        return $this;
    }

    /**
     * Remove rates_by_peoples
     *
     * @param RateByPeople $ratesByPeoples
     */
    public function removeRatesByPeople(RateByPeople $ratesByPeoples)
    {
        $this->rates_by_peoples->removeElement($ratesByPeoples);
    }

    /**
     * Remove all RateByPeoples
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     */
    public function removeAllRatesByPeople()
    {
        $this->rates_by_peoples = new ArrayCollection();
    }

    /**
     * Get rates_by_peoples
     *
     * @return ArrayCollection
     */
    public function getRatesByPeoples()
    {
        return $this->rates_by_peoples;
    }

    /**
     * Set variation_type_kids
     *
     * @param array $variationTypeKids
     * @return Room
     */
    public function setVariationTypeKids($variationTypeKids)
    {
        $this->variation_type_kids = $variationTypeKids;

        return $this;
    }

    /**
     * Get variation_type_kids
     *
     * @return array
     */
    public function getVariationTypeKids()
    {
        return $this->variation_type_kids;
    }

    /**
     * Add rates_by_kids
     *
     * @param \Navicu\Core\Domain\Model\Entity\RateByKid $ratesByKids
     * @return Room
     */
    public function addRatesByKid(\Navicu\Core\Domain\Model\Entity\RateByKid $ratesByKids)
    {
        $this->rates_by_kids[] = $ratesByKids;

        return $this;
    }


    /**
     * Remove rates_by_kids
     *
     * @param \Navicu\Core\Domain\Model\Entity\RateByKid $ratesByKids
     */
    public function removeRatesByKid(\Navicu\Core\Domain\Model\Entity\RateByKid $ratesByKids)
    {
        $this->rates_by_kids->removeElement($ratesByKids);
    }

    /**
     * Remove all RateByKids
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     */
    public function removeAllRatesByKid()
    {
        $this->rates_by_kids = new ArrayCollection();
    }

    /**
     * Get rates_by_kids
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatesByKids()
    {
        return $this->rates_by_kids;
    }

    /**
     * Add packages
     *
     * @param Pack $packages
     * @return Room
     */
    public function addPackage(Pack $packages)
    {
        $this->packages[] = $packages;

        return $this;
    }

    /**
     * Remove packages
     *
     * @param Pack $packages
     */
    public function removePackage(Pack $packages)
    {
        $this->packages->removeElement($packages);
    }

    /**
     * Get packages
     *
     * @return ArrayCollection
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Add packages_linkage
     *
     * @param PackLinkage $packagesLinkage
     * @return Room
     */
    public function addPackagesLinkage(PackLinkage $packagesLinkage)
    {
        $this->packages_linkage[] = $packagesLinkage;

        return $this;
    }

    /**
     * Remove packages_linkage
     *
     * @param PackLinkage $packagesLinkage
     */
    public function removePackagesLinkage(PackLinkage $packagesLinkage)
    {
        $this->packages_linkage->removeElement($packagesLinkage);
    }

    /**
     * Get packages_linkage
     *
     * @return ArrayCollection
     */
    public function getPackagesLinkage()
    {
        return $this->packages_linkage;
    }

    /**
     * Add images_gallery
     *
     * @param RoomImagesGallery $imagesGallery
     * @return Room
     */
    public function addImagesGallery(RoomImagesGallery $imagesGallery)
    {
        $this->images_gallery[] = $imagesGallery;

        return $this;
    }

    /**
     * Remove images_gallery
     *
     * @param RoomImagesGallery $imagesGallery
     */
    public function removeImagesGallery(RoomImagesGallery $imagesGallery)
    {
        $this->images_gallery->removeElement($imagesGallery);
    }

    /**
     * Get images_gallery
     *
     * @return ArrayCollection
     */
    public function getImagesGallery()
    {
        return $this->images_gallery;
    }

    /**
     * Set property
     *
     * @param Property $property
     * @return Room
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set type
     *
     * @param RoomType $type
     * @return Room
     */
    public function setType(RoomType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return RoomType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set bedrooms
     *
     * @param array $bedrooms
     * @return Room
     */
    public function setBeds($bedrooms)
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    /**
     * Get bedrooms
     *
     * @return array
     */
    public function getBeds()
    {
        return $this->bedrooms;
    }

    public function prePersist()
    {
        if (empty($this->name)) {
            $this->generateName();
        }
    }

    /**
     * Add bed in the current bedrooms
     *
     * @param Bed
     * @return Room
     */
    public function addBedroom(Bedroom $bedroom)
    {
        $this->bedrooms->add($bedroom);
        return $this;
    }

    /**
     * Set increment
     *
     * @param boolean $increment
     * @return Room
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;

        return $this;
    }

    /**
     * Get increment
     *
     * @return boolean
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Room
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    public function generateName()
    {
        if ( $this->type->getLvl() == 1 ) {
            $this->name = 'Habitación '.$this->type->getParent()->getTitle()
            .' - '
            .$this->type->getTitle()
            .(!$this->unique_type ?
            ' ('.$this->max_people
            .($this->max_people==1 ? ' Persona)' : ' Personas)') : '' );
        } else {
            $this->name = 'Habitación '.$this->type->getTitle()
            .(!$this->unique_type ?
            ' ('.$this->max_people
            .($this->max_people==1 ? ' Persona)' : ' Personas)') : '' );
        }
        return $this->name;
    }

    /**
     * Remove livingrooms
     *
     * @param Livingroom $livingrooms
     */
    public function removeLivingroom(Livingroom $livingrooms)
    {
        $this->livingrooms->removeElement($livingrooms);
    }

    /**
     * Remove bedrooms
     *
     * @param Beedroom $bedrooms
     */
    public function removeBedroom(Bedroom $bedrooms)
    {
        $this->bedrooms->removeElement($bedrooms);
    }

    /**
     *   indica que el objeto se encuentra en un estado valido
     *   @return boolean
     */
    public function validate()
    {
        $this->errors = array();
        $this->errors['type'] = array();
        $this->errors['amount_rooms'] = array();
        $this->errors['size'] = array();
        $this->errors['max_people'] = array();
        $this->errors['min_price_person'] = array();
        $this->errors['max_price_person'] = array();
        $this->errors['variation_type_people'] = array();
        $this->errors['features'] = array();
        $this->errors['bedrooms'] = array();
        $this->errors['livingrooms'] = array();

        if(empty($this->type)){
            array_push(
                $this->errors['type'],
                array('message'=>'type should not be empty')
            );
        }
        if (empty($this->amount_rooms)) {
            array_push(
                $this->errors['amount_rooms'],
                array('message'=>'amount_rooms should not be empty')
            );
        } else {
            if(!is_integer($this->amount_rooms)){
                array_push(
                    $this->errors['amount_rooms'],
                    array('message'=>'amount_rooms amount is not an integer')
                );
            } else {
                if ($this->amount_rooms<1) {
                    array_push(
                        $this->errors['amount_rooms'],
                        array('message'=>'amount_rooms  must be greater than 1')
                    );
                }
            }
        }
        if (!empty($this->size)) {
            if (!is_numeric($this->size)) {
                array_push(
                    $this->errors['size'],
                    array('message'=>'size  must be a number')
                );
            }
        }
        if (empty($this->max_people)) {
            array_push(
                $this->errors['max_people'],
                array('message'=>'max_people should not be empty')
            );
        } else {
            if(!is_integer($this->max_people)){
                array_push(
                    $this->errors['max_people'],
                    array('message'=>'max_people amount is not an integer')
                );
            } else {
                if ($this->max_people<1) {
                    array_push(
                        $this->errors['max_people'],
                        array('message'=>'max_people must be greater than 1')
                    );
                }
            }
        }
        if (!empty($this->min_price_person)) {
            if(!is_numeric($this->min_price_person)){
                array_push(
                    $this->errors['min_price_person'],
                    array('message'=>'min_price_person amount is not an integer')
                );
            } else {
                if ($this->min_price_person <= 0) {
                    array_push(
                        $this->errors['min_price_person'],
                        array('message'=>'min_price_person must be greater than 0')
                    );
                }
            }
        }

        if (!empty($this->max_price_person)) {
            if(!is_numeric($this->max_price_person)){
                array_push(
                    $this->errors['max_price_person'],
                    array('message'=>'max_price_person amount is not an integer')
                );
            } else {
                if ($this->max_price_person <= 0) {
                    array_push(
                        $this->errors['max_price_person'],
                        array('message'=>'max_price_person must be greater than 0')
                    );
                } else {
                    if ( empty($this->errors['min_price_person']) && $this->min_price_person > $this->max_price_person ) {
                        array_push(
                            $this->errors['max_price_person'],
                            array('message'=>'max_price_person must be greater than min_price_person')
                        );
                    }
                }
            }
        }

        if ( !empty($this->variation_type_people)) {
            if(
                !is_integer($this->variation_type_people) ||
                $this->variation_type_people<0 ||
                $this->variation_type_people>1
            ){
                array_push(
                    $this->errors['variation_type_people'],
                    array('message'=>'variation_type_people is a invalid choice')
                );
            }
        }

        foreach($this->features as $feature){
            if($feature->validate()){
                if($feature->getFeature()==0){
                    if (!$feature->getFeature()->validateFeatureType($this->type)) {
                        $this->errors['features'][$feature->getFeature()->getId()] = array('message'=>'prohibited action');
                    }
                }
            } else {
                $this->errors['features'][$feature->getFeature()->getId()] = $feature->getErrors();
            }
        }

        foreach($this->livingrooms as $id => $livingroom){
            if (!$livingroom->validate()) {
                $this->errors['livingrooms'][$id] = $livingroom->getErrors();
            }
        }

        foreach($this->bedrooms as $id => $bedroom){
            if (!$bedroom->validate()) {
                $this->errors['bedrooms'][$id] = $bedroom->getErrors();
            }
        }

        foreach ($this->errors as $errors) {
            if(empty($errors)){
                unset($errors);
            }
        }
        return empty($this->errors);
    }

    /**
     *   retorna un array de errores del objeto
     *   @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *  marca el objeto como unico en su tipo dentro del establecimiento
     *
     * @authos Gabriel Camacho <kbo025@gmail.com>
     * @param Boolean $unique
     */
    public function setUniqueType($unique)
    {
        $this->unique_type = $unique;
    }

    /**
     *  devuelve un array que representa el objeto
     *
     * @authos Gabriel Camacho <kbo025@gmail.com>
     * @param bool $cc
     * @return array
     */
    public function toArray($cc = false)
    {
        $response = [];
        $this->generateName();

        //atributos
        foreach ($this as $name => $value) {

            $index = $cc ? self::formaterCamelCase( $name ) : $name;
            if(!is_object($value))
                $response[$index] = $value;
        }

        //tipo de habitacion
        $response['type'] = !empty($this->type) ? $this->type->getId() : null;

        //caracteristicas de la habitacion
        $response['features']= [];
        foreach($this->features as $feature)
            $response['features'][] = $feature->toArray();

        //aumento por persona
        $index = 'rates_by_people';
        if($cc)
            $index = self::formaterCamelCase( $index );
        $response[$index]=[];
        foreach($this->rates_by_peoples as $rate)
            $response[$index][] = $rate->toArray();

        //aumento por niños
        $index = $cc ? self::formaterCamelCase( 'rates_by_kids' ) : 'rates_by_kids';
        $response[$index] = [];
        foreach($this->rates_by_kids as $rate)
            $response[$index][]=$rate->toArray();

        //Combinaciones de camas
        $response['bedrooms'] = [];
        foreach ($this->bedrooms as $bedroom)
            $response['bedrooms'][] = $bedroom->toArray();

        $response['livingrooms'] = [];
        foreach($this->livingrooms as $livingroom)
            $response['livingrooms'][] =  $livingroom->toArray();

        return $response;
    }

    private static function formaterCamelCase($value)
    {
        $response = \ucwords($value,'_');
        $response = str_replace( '_', '', $response);
        return \ucfirst ($response);
    }

    /**
     * Función para la creación de los dailyPack de una habitación
     * cuando su dailyRoom esta siendo creado por primera vez.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Object $dailyRoom
     * @return Array
     */
    public function createDailyByPack($dailyRoom)
    {
        $response = array();
        $packages = $this->packages;
		for ($p = 0; $p < count($packages); $p++) {
            $dailyPack = new DailyPack;
            $dailyPack->setDate($dailyRoom->getDate());
            $dailyPack->setPack($packages[$p]);
            array_push($response, $dailyPack);
        }
        return $response;
    }

    /**
     * Función para la creación de una habitación a partir de un
     * arreglo con información basica.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return Object
     */
    public function updateObject($data, $rf = null)
    {
        $this->setMaxPeople($data["numPeople"]);
        $this->setAmountRooms($data["numRooms"]);
        $this->setBaseAvailability($data["baseAvailability"]);
        $this->setIncrement($data['prices']['increment']);
        $this->setVariationTypePeople($data['prices']['variationTypePeople']);
        $this->setVariationTypeKids($data['prices']['variationTypeKid']);
        $this->setSize($data['sizeRoom']);
        $this->setSmokingPolicy($data['smoking']);
        if (isset($data["minPeople"]))
            $this->setMinPeople($data["minPeople"]);

        if(count($this->packages) < 1){

            $policyCancellation = $this->getProperty()->getPropertyCancellationPolicies()[0];

            if ($this->property->getAllIncluded()) {
                $typeAllIncluded =$rf->get("Category")->find(88); // BreakFast Included

                $packAI = new Pack;
                $packAI->setType($typeAllIncluded);
                $packAI->setRoom($this);
                $packAI->addPackCancellationPolicy($policyCancellation);
                $policyCancellation->addPackage($packAI);
                $this->addPackage($packAI);
            } else {
                $typeRoomOnly = $rf->get("Category")->find(14); // Room Only
                $typeBreakFastIncluded =$rf->get("Category")->find(16); // BreakFast Included

                $packRO = new Pack;
                $packRO->setType($typeRoomOnly);
                $packRO->setRoom($this);
                $packRO->addPackCancellationPolicy($policyCancellation);
                $policyCancellation->addPackage($packRO);
                $this->addPackage($packRO);

                $packBFI = new Pack;
                $packBFI->setType($typeBreakFastIncluded);
                $packBFI->addPackCancellationPolicy($policyCancellation);
                $policyCancellation->addPackage($packBFI);
                $packBFI->setRoom($this);
                $this->addPackage($packBFI);
            }
        }

        if ($rf) {
            if (is_null($data['subRoom'])) {
                $roomType = $rf->get("RoomType")->findOneByArray(array("id"=>$data['typeRoom']));
            } else {
                $roomType = $rf->get("RoomType")->findOneByArray(array("id"=>$data['subRoom']));
            }
            $this->setType($roomType);
        }

        $this->unique_type = true;

        $rooms = $this->property->getRooms();
        foreach ($rooms as $room) {
            if ($room->getType() == $this->getType() and $room->getId() != $this->getId()) {
                $this->unique_type = false;
            }
        }

        return $this;
    }

    /**
     * Función es usada para validar si el establecimiento cuenta con el numero
     * de habitaciones valido para estar activo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return void
     */
    public function validAmountRooms($room) {
        //Se comento este codigo porque no es necesario realizar esta validación
        // comentario realizado por Freddy Contreras

        /*$roomsProperty = $this->property->getRooms()->unwrap()->toArray();
        $amountRoom = $room->getId() ? 0 : $room->getAmountRooms();

        for ($r = 0; $r < count($roomsProperty); $r++) {
            $amountRoom = $amountRoom + $roomsProperty[$r]->getAmountRooms();
        }

        if ($amountRoom == $this->property->getAmountRoom()) {
            $this->property->setActive(true);
        } else {
            $this->property->setActive(false);
        }*/
        return $this;
    }

    /**
     * Función es usada para cambiar el nombre de la habitación en los path de galleria,
     * y a su vez cambiar el nombre de los direcorios.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return void
     */
    public function changeNameGallery($rf, $name = '')
    {

        $property = $this->getProperty();
		$gallery = $this->getImagesGallery();
        $newName = Slug::generateSlug(str_replace('-',' ',$this->getName()));

        if (!$this->getProfileImage())
            return $this;

        $oldName = explode("/",$this->getProfileImage()->getImage()->getFileName());
        array_pop($oldName);
        $oldName = array_pop($oldName);

        $oldDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_md/property/".$property->getSlug()."/rooms/".$oldName;
        $newDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_md/property/".$property->getSlug()."/rooms/".$newName;
        $isDir =  is_dir($oldDir);
		if ($isDir)
            rename($oldDir, $newDir);

        $oldDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_xs/property/".$property->getSlug()."/rooms/".$oldName;
        $newDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_xs/property/".$property->getSlug()."/rooms/".$newName;
        $isDir =  is_dir($oldDir);
		if ($isDir)
            rename($oldDir, $newDir);

        $oldDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_original/property/".$property->getSlug()."/rooms/".$oldName;
        $newDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_original/property/".$property->getSlug()."/rooms/".$newName;
        $isDir =  is_dir($oldDir);
		if ($isDir)
            rename($oldDir, $newDir);

        $oldDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_sm/property/".$property->getSlug()."/rooms/".$oldName;
        $newDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_sm/property/".$property->getSlug()."/rooms/".$newName;
        $isDir =  is_dir($oldDir);
		if ($isDir)
            rename($oldDir, $newDir);

        $oldDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_email/property/".$property->getSlug()."/rooms/".$oldName;
        $newDir = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_email/property/".$property->getSlug()."/rooms/".$newName;
        $isDir =  is_dir($oldDir);
		if ($isDir)
            rename($oldDir, $newDir);

		for ($g = 0; $g < count($gallery); $g++) {
			$file = new SplFileInfo($gallery[$g]->getImage()->getFileName(), null, null);
			$gallery[$g]->getImage()->setFileName("property/".$property->getSlug()."/rooms/".$newName."/".$file->getFilename());
			$rf->get("Document")->save($gallery[$g]->getImage());
        }
        return $this;
    }

    /**
     * Set increment_kid
     *
     * @param boolean $incrementKid
     * @return Room
     */
    public function setIncrementKid($incrementKid)
    {
        $this->increment_kid = $incrementKid;

        return $this;
    }

    /**
     * Get increment_kid
     *
     * @return boolean
     */
    public function getIncrementKid()
    {
        return $this->increment_kid;
    }

    /**
     * Set kid_pay_as_adult
     *
     * @param boolean $kidPayAsAdult
     * @return Room
     */
    public function setKidPayAsAdult($kidPayAsAdult)
    {
        $this->kid_pay_as_adult = $kidPayAsAdult;

        return $this;
    }

    /**
     * Get kid_pay_as_adult
     *
     * @return boolean 
     */
    public function getKidPayAsAdult()
    {
        return $this->kid_pay_as_adult;
    }

    /**
     * Set same_increment_adult
     *
     * @param boolean $sameIncrementAdult
     * @return Room
     */
    public function setSameIncrementAdult($sameIncrementAdult)
    {
        $this->same_increment_adult = $sameIncrementAdult;

        return $this;
    }

    /**
     * Get same_increment_adult
     *
     * @return boolean 
     */
    public function getSameIncrementAdult()
    {
        return $this->same_increment_adult;
    }

    /**
     * Set same_increment_kid
     *
     * @param boolean $sameIncrementKid
     * @return Room
     */
    public function setSameIncrementKid($sameIncrementKid)
    {
        $this->same_increment_kid = $sameIncrementKid;

        return $this;
    }

    /**
     * Get same_increment_kid
     *
     * @return boolean 
     */
    public function getSameIncrementKid()
    {
        return $this->same_increment_kid;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Room
     */
    public function setSlug()
    {
        $this->generateSlug();
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

        $this->slug = $this->getProperty()->getSlug().'-'.$slug;
    }
}
