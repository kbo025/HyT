<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\RoomFeatureType;

/**
 * Clase RoomType
 * 
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado de
 * los tipos de habitaciones usando Arboles.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class RoomType
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
     * Esta propiedad es usada para interactuar con el Id del nodo izquierdo
     * 
     * @var Integer
     */
    protected $lft;

    /**
     * @var integer
     */
    protected $rgt;

    /**
     * @var integer
     */
    protected $root;

    /**
     * @var integer
     */
    protected $lvl;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var ArrayCollection
     */
    protected $children;

    /**
     * @var ArrayCollection
     */
    protected $parent;

    /**
     * @var ArrayCollection
     */
    protected $features;

    /**
     * Es la clasificación de la estructura de la habitación
     * (ejemplo: Tiene o no salones, posee o no baños extras, etc)
     * @var integer
     */
    protected $category = 0;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->features = new ArrayCollection();
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
     * @return RoomType
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
     * Set lft
     *
     * @param integer $lft
     * @return RoomType
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return RoomType
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return RoomType
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
     * Set lvl
     *
     * @param integer $lvl
     * @return RoomType
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
     * Set Code
     *
     * @param string $code
     * @return RoomType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add children
     *
     * @param RoomType $children
     * @return RoomType
     */
    public function addChild(RoomType $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param RoomType $children
     */
    public function removeChild(RoomType $children)
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
     * @param RoomType $parent
     * @return RoomType
     */
    public function setParent(RoomType $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return RoomType
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add features
     *
     * @param  $features
     * @return RoomType
     */
    public function addFeature( $features)
    {
        $this->features[] = $features;

        return $this;
    }

    /**
     * Remove features
     *
     * @param  $features
     */
    public function removeFeature( $features)
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
     * Set id
     *
     * @param integer $id
     * @return RoomType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set category
     *
     * @param integer $category
     * @return RoomType
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
