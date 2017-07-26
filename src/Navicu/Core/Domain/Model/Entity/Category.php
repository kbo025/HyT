<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use \Navicu\Core\Domain\Model\Entity\CancellationPolicy;

/**
 * Clase Category.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado por Arboles.
 * 
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Category
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el nombre o el titulo del nodo en el arbol
     *
     * @var String
     */
    protected $title;

    /**
     * Esta propiedad es usada para interactuar con el nombre o el titulo del nodo en el arbol
     *
     * @var String
     */
    protected $code;

    /**
     * Esta propiedad es usada para interactuar con el Id del nodo izquierdo
     *
     * @var Integer
     */
    protected $lft;

    /**
     * Esta propiedad es usada para interactuar con el nivel en el que se encuentra el nodo
     *
     * @var Integer
     */
    protected $lvl;

    /**
     * Esta propiedad es usada para interactuar con el Id del nodo izquierdo
     *
     * @var Integer
     */
    protected $rgt;

    /**
     * Esta propiedad es usada para interactuar con la raiz del nodo
     *
     * @var Integer
     */
    protected $root;

    /**
     * Esta propiedad es usada para interactuar con el nodo hijo de la lista.
     *
     * @var ArrayCollection
     */
    protected $children;

    /**
     * Esta propiedad es usada para interactuar con el nodo padre de la lista.
     *
     * @var Category
     */
    protected $parent;

    /**
     * Esta propiedad es usada para interactuar con el perfil del propietario
     * cuyo valor asignado coincida con el valor de la lista Cargo de Oficina.
     *
     * @var ArrayCollection
     */
    protected $offices_owners_profiles;

    /**
     * Esta propiedad es usada para interactuar con el servicio de la habitación
     * cuyo valor asignado coincida con el valor de tipos de servicio.
     *
     * @var Pack Type Object
     */
    protected $types_pack;

    /**
     * @var ArrayCollection
     */
    private $types_cancellation_policy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->offices_owners_profiles = new ArrayCollection();
        $this->types_pack = new ArrayCollection();
        $this->types_property_gallery = new ArrayCollection();
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
     * @return Category
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
     *
     * @return Category
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
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Category
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
     * Set rgt
     *
     * @param integer $rgt
     *
     * @return Category
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
     *
     * @return Category
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
     * Add child
     *
     * @param Category $child
     *
     * @return Category
     */
    public function addChild(Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param Category $child
     */
    public function removeChild(Category $child)
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
     * Add officesOwnersProfile
     *
     * @param OwnerProfile $officesOwnersProfile
     *
     * @return Category
     */
    public function addOfficesOwnersProfile(OwnerProfile $officesOwnersProfile)
    {
        $this->offices_owners_profiles[] = $officesOwnersProfile;

        return $this;
    }

    /**
     * Remove officesOwnersProfile
     *
     * @param OwnerProfile $officesOwnersProfile
     */
    public function removeOfficesOwnersProfile(OwnerProfile $officesOwnersProfile)
    {
        $this->offices_owners_profiles->removeElement($officesOwnersProfile);
    }

    /**
     * Get offices_owners_profiles
     *
     * @return ArrayCollection
     */
    public function getOfficesOwnersProfiles()
    {
        return $this->offices_owners_profiles;
    }

    /**
     * Add types_pack
     *
     * @param Pack $typesPack
     *
     * @return Category
     */
    public function addTypesPack(Pack $typesPack)
    {
        $this->types_pack[] = $typesPack;

        return $this;
    }

    /**
     * Remove types_pack
     *
     * @param Pack $typesPack
     */
    public function removeTypesPack(Pack $typesPack)
    {
        $this->types_pack->removeElement($typesPack);
    }

    /**
     * Get types_pack
     *
     * @return ArrayCollection
     */
    public function getTypesPack()
    {
        return $this->types_pack;
    }

    /**
     * Set parent
     *
     * @param Category $parent
     *
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add types_cancellation_policy
     *
     * @param CancellationPolicy $typesCancellationPolicy
     * @return Category
     */
    public function addTypesCancellationPolicy(CancellationPolicy $typesCancellationPolicy)
    {
        $this->types_cancellation_policy[] = $typesCancellationPolicy;

        return $this;
    }

    /**
     * Remove types_cancellation_policy
     *
     * @param CancellationPolicy $typesCancellationPolicy
     */
    public function removeTypesCancellationPolicy(CancellationPolicy $typesCancellationPolicy)
    {
        $this->types_cancellation_policy->removeElement($typesCancellationPolicy);
    }

    /**
     * Get types_cancellation_policy
     *
     * @return ArrayCollection
     */
    public function getTypesCancellationPolicy()
    {
        return $this->types_cancellation_policy;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Category
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
}
