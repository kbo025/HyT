<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Clase ServiceType
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado de
 * los tipos de servicios de un establecimiento por medio de estructura de arboles.
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class ServiceType
{

    protected $test1;
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
     * tipo indica la naturaleza y comportamiento del servicio
     * 
     * @var integer
     */
    protected $type;

    /**
     * Esta propiedad es usada para interactuar con el Id del nodo izquierdo
     * @var integer
     */
    protected $rgt;

    /**
     * Esta propiedad es usada para interactuar con el nivel en el que se encuentra el nodo
     * @var integer
     */
    protected $root;

    /**
     * Esta propiedad es usada para interactuar con el Id del nodo izquierdo
     * @var boolean
     */
    protected $required;

    /**
     * Esta propiedad es usada para interactuar con la raiz del nodo
     * @var boolean
     */
    protected $gallery;

    /**
     * Esta propiedad es usada para interactuar con el nodo padre de la lista.
     * @var integer
     */
    protected $lvl;


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    /**
     * @var ServiceType
     */
    protected $parent;

    /**
     * @var boolean
     */
    private $priority;

    /**
     * @var string
     */
    private $url_icon;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 0;
        $this->required = false;
        $this->gallery = false;
        $this->children = new ArrayCollection();
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
     * @return ServiceType
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
     * @return ServiceType
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
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return ServiceType
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     * @return boolean 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set gallery
     *
     * @param boolean $gallery
     * @return ServiceType
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
        return $this;
    }

    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return ServiceType
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Add children
     *
     * @param ServiceType $children
     * @return ServiceType
     */
    public function addChild(ServiceType $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param ServiceType $children
     */
    public function removeChild(ServiceType $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param ServiceType $parent
     * @return ServiceType
     */
    public function setParent(ServiceType $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Set root
     *
     * @param ServiceType $root
     * @return ServiceType
     */
    public function setRoot(ServiceType $root = null)
    {
        $this->root = $root;
    }

    /**
     * Get parent
     *
     * @return ServiceType 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /*
     * Get root
     *
     * @return ServiceType 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return ServiceType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set priority
     *
     * @param boolean $priority
     * @return ServiceType
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return boolean 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set url_icon
     *
     * @param string $urlIcon
     * @return ServiceType
     */
    public function setUrlIcon($urlIcon)
    {
        $this->url_icon = $urlIcon;

        return $this;
    }

    /**
     * Get url_icon
     *
     * @return string 
     */
    public function getUrlIcon()
    {
        return $this->url_icon;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $property_gallery_types;


    /**
     * Add property_gallery_types
     *
     * @param \Navicu\Core\Domain\Model\Entity\PropertyGallery $propertyGalleryTypes
     * @return ServiceType
     */
    public function addPropertyGalleryType(\Navicu\Core\Domain\Model\Entity\PropertyGallery $propertyGalleryTypes)
    {
        $this->property_gallery_types[] = $propertyGalleryTypes;

        return $this;
    }

    /**
     * Remove property_gallery_types
     *
     * @param \Navicu\Core\Domain\Model\Entity\PropertyGallery $propertyGalleryTypes
     */
    public function removePropertyGalleryType(\Navicu\Core\Domain\Model\Entity\PropertyGallery $propertyGalleryTypes)
    {
        $this->property_gallery_types->removeElement($propertyGalleryTypes);
    }

    /**
     * Get property_gallery_types
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPropertyGalleryTypes()
    {
        return $this->property_gallery_types;
    }

    /**
     *  indica si el servicio es de tipo salon
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho
     * @version 21-12-15
     *
     * @return Boolean
     */
    public function isSalon()
    {
        return $this->id == 50;
    }

    /**
     *  indica si el servicio es de tipo Bar
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho
     * @version 21-12-15
     *
     * @return Boolean
     */
    public function isBar()
    {
        return $this->id == 24;
    }

    /**
     *  indica si el servicio es de tipo Restaurnat
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho
     * @version 21-12-15
     *
     * @return Boolean
     */
    public function isRestaurant()
    {
        return $this->id == 23;
    }
}
