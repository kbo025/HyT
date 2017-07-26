<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Room;

/**
 * Clase RoomImagesGallery
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * la galleria de imagenes asociadas a una habitación.
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @author 26/05/2015
 */
class RoomImagesGallery
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad representa el conjunto de imagenes asociadas
     * 
     * @var Array Object Document
     */
    protected $image;

    /**
     * Esta propiedad representa la habitación de la galería
     * 
     * @var Object Room
     */
    protected $room;

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
     * @return RoomImagesGallery
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
     * Set room
     *
     * @param Room $room
     * @return RoomImagesGallery
     */
    public function setRoom(Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return Room 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set order_gallery
     *
     * @param integer $orderGallery
     * @return RoomImagesGallery
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
