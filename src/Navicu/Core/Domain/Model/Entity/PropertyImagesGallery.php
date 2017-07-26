<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;

/**
 * Clase PropertyImagesGallery
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * imagenes asociadas a una gallería de imagenes del establecimiento
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @author 26/05/2015
 */
class PropertyImagesGallery
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad representa el conjunto de imagenes asociadas
     * @var Document
     */
    protected $image;

    /**
     * Esta propiedad representa la gallería a la que pertenece la imagen
     * @var PropertyGallery
     */
    protected $property_gallery;

    /**
     * Orden de la imagen en la galería
     *
     * @var integer
     */
    private $order_gallery = 0;

    /**
     * constructor
     */
    public function __construct() {}

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
     * Set image
     *
     * @param Document $image
     * @return PropertyImagesGallery
     */
    public function setImage(Document $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Document
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set property_gallery
     *
     * @param PropertyGallery $propertyGallery
     * @return PropertyImagesGallery
     */
    public function setPropertyGallery(PropertyGallery $propertyGallery = null)
    {
        $this->property_gallery = $propertyGallery;

        return $this;
    }

    /**
     * Get property_gallery
     *
     * @return PropertyGallery
     */
    public function getPropertyGallery()
    {
        return $this->property_gallery;
    }

    /**
     * Set order_gallery
     *
     * @param integer $orderGallery
     * @return PropertyImagesGallery
     */
    public function setOrderGallery($orderGallery)
    {
        $this->order_gallery = $orderGallery;

        return $this;
    }

    /**
     * Get order_gallery
     *
     * @return integer 
     */
    public function getOrderGallery()
    {
        return $this->order_gallery;
    }
}
