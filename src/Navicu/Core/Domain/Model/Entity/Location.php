<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\Language;
use Navicu\Core\Domain\Model\Entity\CurrencyType;

/**
 * Clase Location
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado de
 * localidades: Pais, Estado, Ciudad y Parroquias usando Arboles.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Location
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el nombre o el titulo del nodo en el arbol
     * 
     * @var String
     */
    protected $title;

    /**
     * Esta propiedad es usada para interactuar con el nivel en el que se encuentra el nodo
     * 
     * @var Integer
     */
    protected $lvl;

    /**
     * Esta propiedad es usada para interactuar con la raiz del nodo
     * 
     * @var Integer
     */
    protected $root;

    /**
     * Esta propiedad es usada para interactuar con el nodo padre de la lista.
     * 
     * @var Location Type Object
     */
    protected $parent;

    /**
     * Constructor
     */
    protected $children;

    /**
    *   codigo alfabetico de 2 digitos que identifica el pais ISO 3166-1
    *   @var string
    */
    protected $alfa2;

    /**
    *   codigo alfabetico de 3 digitos que identifica el pais ISO 3166-1
    *   @var string
    */
    protected $alfa3;

    /**
    *   codigo numerico que identifica el pais ISO 3166-1
    *   @var integer
    */
    protected $number;

    /**
     * Esta propiedad es usada para interactuar con la ciudad a la que pertenece
     * el municipio o parroquia.
     * 
     * @var Location Type Object
     */
    protected $city_id;

    /**
     * Esta propiedad es usada para interactuar con los municipios o parroquias
     * que forman parte de una ciudad.
     * 
     * @var Location Type Object
     */
    protected $integrations_city;

    /**
     * url del icono de la bandera de la localidad (usado para mostrarse en los formularios con numeros de telefono)
     * @var string
     */
    private $url_flag_icon;

    /**
     * prefijo de numero de telefono usado en la localidad (usado en los formularios que contienen telefonos)
     * @var string
     */
    private $phone_prefix;

    /**
     * lenguaje nativo de la localidad
     * @var Language
     */
    private $native_language;

    /**
     * moneda oficial de la localidad
     * @var CurrencyType
     */
    private $official_currency;

    /**
     * lista de lenguajes mas usados en la localidad
     * @var ArrayCollection
     */
    private $languages_used;

    /**
     * lista de otras monedas usadas en la localidad
     * @var ArrayCollection
     */
    private $currencies_used;

    /**
     * Representa el slug de la localidad
     *
     * @var string
     */
    private $slug;

    /**
     * indica que clase de locacion:
     *  1: isla
     *  0: cualquier otra cosa
     *
     * @var integer
     */
    private $type = 0;

    /**
     * Representa el tipo de localidad
     *
     * @var \Navicu\Core\Domain\Model\Entity\LocationType
     */
    private $location_type;

    /**
     * Las localidades dependientes de la localidad actual (dependientes de mi)
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $dependentOnMe;

    /**
     * Las localidades que la localidad actual dependen (de quien soy dependiente)
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $dependency;

    /**
     * La tipologia de destinos a la que puede pertenecer una localidad.
     *
     * @var DestinationsType
     */
    private $destination_type;
    /**
     * Las IP's pertenecientes a una localidad.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ip_collections;

    /**
     * indica si la localidad será visible en los listados o no
     *
     * @var boolean
     */
    private $visible = true;

    /**
     * aereopuertos relacionado con la localidad
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $airports;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->integrations_city = new ArrayCollection();
        $this->dependentOnMe = new ArrayCollection();
        $this->dependency = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Location
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Location
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set alfa2
     *
     * @param string $alfa2
     *
     * @return Location
     */
    public function setAlfa2($alfa2)
    {
        $this->alfa2 = $alfa2;

        return $this;
    }

    /**
     * Get alfa2
     *
     * @return string
     */
    public function getAlfa2()
    {
        return $this->alfa2;
    }

    /**
     * Set alfa3
     *
     * @param string $alfa3
     *
     * @return Location
     */
    public function setAlfa3($alfa3)
    {
        $this->alfa3 = $alfa3;

        return $this;
    }

    /**
     * Get alfa3
     *
     * @return string
     */
    public function getAlfa3()
    {
        return $this->alfa3;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Location
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set url_flag_icon
     *
     * @param string $urlFlagIcon
     *
     * @return Location
     */
    public function setUrlFlagIcon($urlFlagIcon)
    {
        $this->url_flag_icon = $urlFlagIcon;

        return $this;
    }

    /**
     * Get url_flag_icon
     *
     * @return string
     */
    public function getUrlFlagIcon()
    {
        return $this->url_flag_icon;
    }

    /**
     * Add child
     *
     * @param Location $child
     *
     * @return Location
     */
    public function addChild(Location $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param Location $child
     */
    public function removeChild(Location $child)
    {
        $this->children->removeElement($child);
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
     * Set phone_prefix
     *
     * @param string $phonePrefix
     *
     * @return Location
     */
    public function setPhonePrefix($phonePrefix)
    {
        $this->phone_prefix = $phonePrefix;

        return $this;
    }

    /**
     * Get phone_prefix
     *
     * @return string
     */
    public function getPhonePrefix()
    {
        return $this->phone_prefix;
    }

    /**
     * Set parent
     *
     * @param Location $parent
     *
     * @return Location
     */
    public function setParent(Location $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Location
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set native_language
     *
     * @param Language $nativeLanguage
     *
     * @return Location
     */
    public function setNativeLanguage(Language $nativeLanguage = null)
    {
        $this->native_language = $nativeLanguage;

        return $this;
    }

    /**
     * Get native_language
     *
     * @return Language
     */
    public function getNativeLanguage()
    {
        return $this->native_language;
    }

    /**
     * Set official_currency
     *
     * @param CurrencyType $officialCurrency
     *
     * @return Location
     */
    public function setOfficialCurrency(CurrencyType $officialCurrency = null)
    {
        $this->official_currency = $officialCurrency;

        return $this;
    }

    /**
     * Get official_currency
     *
     * @return CurrencyType
     */
    public function getOfficialCurrency()
    {
        return $this->official_currency;
    }

    /**
     * Set root
     *
     * @param integer $root
     *
     * @return Location
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Add languages_used
     *
     * @param Language $languagesUsed
     * @return Location
     */
    public function addLanguagesUsed(Language $languagesUsed)
    {
        $this->languages_used[] = $languagesUsed;

        return $this;
    }

    /**
     * Remove languages_used
     *
     * @param Language $languagesUsed
     */
    public function removeLanguagesUsed(Language $languagesUsed)
    {
        $this->languages_used->removeElement($languagesUsed);
    }

    /**
     * Get languages_used
     *
     * @return ArrayCollection
     */
    public function getLanguagesUsed()
    {
        return $this->languages_used;
    }

    /**
     * Add currencies_used
     *
     * @param CurrencyType $currenciesUsed
     *
     * @return Location
     */
    public function addCurrenciesUsed(CurrencyType $currenciesUsed)
    {
        $this->currencies_used[] = $currenciesUsed;

        return $this;
    }

    /**
     * Remove currencies_used
     *
     * @param CurrencyType $currenciesUsed
     */
    public function removeCurrenciesUsed(CurrencyType $currenciesUsed)
    {
        $this->currencies_used->removeElement($currenciesUsed);
    }

    /**
     * Get currencies_used
     *
     * @return ArrayCollection
     */
    public function getCurrenciesUsed()
    {
        return $this->currencies_used;
    }

    /**
     * Add integrations_city
     *
     * @param Location $integrationsCity
     * @return Location
     */
    public function addIntegrationsCity(Location $integrationsCity)
    {
        $this->integrations_city[] = $integrationsCity;

        return $this;
    }

    /**
     * Remove integrations_city
     *
     * @param Location $integrationsCity
     */
    public function removeIntegrationsCity(Location $integrationsCity)
    {
        $this->integrations_city->removeElement($integrationsCity);
    }

    /**
     * Get integrations_city
     *
     * @return ArrayCollection
     */
    public function getIntegrationsCity()
    {
        return $this->integrations_city;
    }

    /**
     * Set city_id
     *
     * @param Location $cityId
     *
     * @return Location
     */
    public function setCityId(Location $cityId = null)
    {
        $this->city_id = $cityId;

        return $this;
    }

    /**
     * Get city_id
     *
     * @return Location
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Location
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Location
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
     * La siguiente función se encarga retorna
     * el tipo de localidad del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $lvl
     * @return null|string
     * @version 27/01/2016
     */
    public function checkType()
    {
        return CoreTranslator::getTranslator($this->getLocationType()->getCode(),'location');
    }

    /**
     * Set location_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\LocationType $locationType
     * @return Location
     */
    public function setLocationType(\Navicu\Core\Domain\Model\Entity\LocationType $locationType = null)
    {
        $this->location_type = $locationType;

        return $this;
    }

    /**
     * Get location_type
     *
     * @return \Navicu\Core\Domain\Model\Entity\LocationType 
     */
    public function getLocationType()
    {
        return $this->location_type;
    }

    /**
     * Add dependentOnMe
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $dependentOnMe
     * @return Location
     */
    public function addDependentOnMe(\Navicu\Core\Domain\Model\Entity\Location $dependentOnMe)
    {
        $this->dependentOnMe[] = $dependentOnMe;

        return $this;
    }

    /**
     * Remove dependentOnMe
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $dependentOnMe
     */
    public function removeDependentOnMe(\Navicu\Core\Domain\Model\Entity\Location $dependentOnMe)
    {
        $this->dependentOnMe->removeElement($dependentOnMe);
    }

    /**
     * Get dependentOnMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDependentOnMe()
    {
        return $this->dependentOnMe;
    }

    /**
     * Add dependency
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $dependency
     * @return Location
     */
    public function addDependency(\Navicu\Core\Domain\Model\Entity\Location $dependency)
    {
        $this->dependency[] = $dependency;

        return $this;
    }

    /**
     * Remove dependency
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $dependency
     */
    public function removeDependency(\Navicu\Core\Domain\Model\Entity\Location $dependency)
    {
        $this->dependency->removeElement($dependency);
    }

    /**
     * Get dependency
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * Set destination_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\Location $destinationType
     * @return Location
     */
    public function setDestinationType(\Navicu\Core\Domain\Model\Entity\Location $destinationType = null)
    {
        $this->destination_type = $destinationType;

        return $this;
    }

    /**
     * Get destination_type
     *
     * @return \Navicu\Core\Domain\Model\Entity\Location 
     */
    public function getDestinationType()
    {
        return $this->destination_type;
    }

    /**
     * Add ip_collections
     *
     * @param \Navicu\Core\Domain\Model\Entity\IpCollection $ipCollections
     * @return Location
     */
    public function addIpCollection(\Navicu\Core\Domain\Model\Entity\IpCollection $ipCollections)
    {
        $this->ip_collections[] = $ipCollections;

        return $this;
    }

    /**
     * Remove ip_collections
     *
     * @param \Navicu\Core\Domain\Model\Entity\IpCollection $ipCollections
     */
    public function removeIpCollection(\Navicu\Core\Domain\Model\Entity\IpCollection $ipCollections)
    {
        $this->ip_collections->removeElement($ipCollections);
    }

    /**
     * Get ip_collections
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIpCollections()
    {
        return $this->ip_collections;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Location
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Add airports
     *
     * @param \Navicu\Core\Domain\Model\Entity\Airport $airports
     * @return Location
     */
    public function addAirport(\Navicu\Core\Domain\Model\Entity\Airport $airports)
    {
        $this->airports[] = $airports;

        return $this;
    }

    /**
     * Remove airports
     *
     * @param \Navicu\Core\Domain\Model\Entity\Airport $airports
     */
    public function removeAirport(\Navicu\Core\Domain\Model\Entity\Airport $airports)
    {
        $this->airports->removeElement($airports);
    }

    /**
     * Get airports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAirports()
    {
        return $this->airports;
    }
}
