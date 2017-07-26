<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 * Clase PropertyFavoriteImages
 *
 * Esta clase representa la gallería de imagenes favoritas de un establecimiento
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @author 26/05/2015
 */
class PropertyFavoriteImages
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * @var Document
     */
    protected $image;

    /**
     * Esta propiedad representa el establecimiento de la gallería
     * @var Property
     */
    protected $property;

    /**
     * Orden de la imagen en la galería
     *
     * @var integer
     */
    private $order_gallery = 0;

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
     * @return PropertyFavoriteImages
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
     * Set property
     *
     * @param Property $property
     * @return PropertyFavoriteImages
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
     * Set order_gallery
     *
     * @param integer $orderGallery
     * @return PropertyFavoriteImages
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
