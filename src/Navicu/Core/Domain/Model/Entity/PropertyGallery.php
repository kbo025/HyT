<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\Category;

/**
 * Clase PropertyGallery
 *
 * Esta clase representa la gallería de imagenes asociadas a un establecimiento
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @author 26/05/2015
 */
class PropertyGallery
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad representa las imagenes correspondiente a la gallería
     * de imagenes del establecimiento
     */
    protected $images_gallery;

    /**
     * Esta propiedad representa el establecimiento de la gallería
     * @var Property
     */
    protected $property;

    /**
     * Esta propiedad representa el tipo de gallería (areas comunes, princiales, piscina, etc)
     * @var Category
     */
    protected $type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images_gallery = new ArrayCollection();
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
     * Add images_gallery
     *
     * @param PropertyImagesGallery $imagesGallery
     * @return PropertyGallery
     */
    public function addImagesGallery(PropertyImagesGallery $imagesGallery)
    {
        $this->images_gallery[] = $imagesGallery;

        return $this;
    }

    /**
     * Remove images_gallery
     *
     * @param PropertyImagesGallery $imagesGallery
     */
    public function removeImagesGallery(PropertyImagesGallery $imagesGallery)
    {
        $this->images_gallery->removeElement($imagesGallery);
    }

    /**
     * Get images_gallery
     *
     * @return ArracyCollection
     */
    public function getImagesGallery()
    {
        return $this->images_gallery;
    }

    /**
     * Set property
     *
     * @param Property $property
     * @return PropertyGallery
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
     * @param ServiceType $type
     * @return PropertyGallery
     */
    public function setType(ServiceType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Category
     */
    public function getType()
    {
        return $this->type;
    }
}
