<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\RoomType;

/**
 * Clase RoomFeatureType
 * 
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado de
 * tipos de caracteristicas de una habitacion usando Arboles.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class RoomFeatureType
{
    const SPACE = 0;
    const SERVICE = 1;

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
     * Esta propiedad indica si la caracteristica es un espacio (0) como baño, cocina, comedor, salon o es un servicio (1)
     * como secador de pelo, jacuzzi, tv, wifi
     * @var Integer
     */
    protected $type;

    /**
     * Esta propiedad indica si la naturaleza de la caracteristica es que sea un booleano como wifi, cocina, habitacion para no
     * fumadores. o es una cantidad como dormitorios, baños, armarios
     * @var boolean
     */
    protected $type_val;

    /**
     * Esta propiedad relaciona la caracteristica con el conjunto de tipos de habitaciones las cuales tienen permitido tenerla
     * 
     * @var ArrayCollection
     */
    protected $room_types;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var ArrayCollection
     */
    private $children;

    /**
     * @var RoomFeatureType
     */
    private $parent;

    /**
     * @var string
     */
    private $url_icon;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->room_types = new ArrayCollection();
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
     * @return RoomFeatureType
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
     * Set type
     *
     * @param integer $type
     * @return RoomFeatureType
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
     * Set type_val
     *
     * @param integer $typeVal
     * @return RoomFeatureType
     */
    public function setTypeVal($typeVal)
    {
        $this->type_val = $typeVal;

        return $this;
    }

    /**
     * Get type_val
     *
     * @return integer 
     */
    public function getTypeVal()
    {
        return $this->type_val;
    }

    /**
     * Add room_types
     *
     * @param RoomType $roomTypes
     * @return RoomFeatureType
     */
    public function addRoomType(RoomType $roomTypes)
    {
        $this->room_types[] = $roomTypes;

        return $this;
    }

    /**
     * Remove room_types
     *
     * @param RoomType $roomTypes
     */
    public function removeRoomType(RoomType $roomTypes)
    {
        $this->room_types->removeElement($roomTypes);
    }

    /**
     * Get room_types
     *
     * @return ArrayCollection
     */
    public function getRoomTypes()
    {
        return $this->room_types;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return RoomFeatureType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return RoomFeatureType
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Add children
     *
     * @param RoomFeatureType $children
     * @return RoomFeatureType
     */
    public function addChild(RoomFeatureType $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param RoomFeatureType $children
     */
    public function removeChild(RoomFeatureType $children)
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
     * Set parent
     *
     * @param RoomFeatureType $parent
     * @return RoomFeatureType
     */
    public function setParent(RoomFeatureType $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return RoomFeatureType
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set url_icon
     *
     * @param string $urlIcon
     * @return RoomFeatureType
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
}
