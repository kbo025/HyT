<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\UploadImage;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de UploadImage
 *
 * Class UploadImageCommand
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\UploadImage
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/11/2015
 */
class UploadImageCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * @var id de la Galería
     */
    private $idGallery;

    /**
     * @var tipo de galería habitación o otherGalleries
     */
    private $typeGallery;

    /**
     * @var el archivo a subir
     */
    private $file;

    /**
     * @var La posición en el ordenamiento
     */
    private $orderGallery;

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'idGallery' => $this->idGallery,
                'typeGallery' => $this->typeGallery,
                'file' => $this->file,
                'orderGallery' => $this->orderGallery
            );
    }

    /**
     * @return Slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param Slug $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return id
     */
    public function getIdGallery()
    {
        return $this->idGallery;
    }

    /**
     * @param id $idGallery
     */
    public function setIdGallery($idGallery)
    {
        $this->idGallery = $idGallery;
    }

    /**
     * @return tipo
     */
    public function getTypeGallery()
    {
        return $this->typeGallery;
    }

    /**
     * @param tipo $typeGallery
     */
    public function setTypeGallery($typeGallery)
    {
        $this->typeGallery = $typeGallery;
    }

    /**
     * @return el
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param el $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Constructor de la clase
     * @param $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return La
     */
    public function getOrderGallery()
    {
        return $this->orderGallery;
    }

    /**
     * @param La $orderGallery
     */
    public function setOrderGallery($orderGallery)
    {
        $this->orderGallery = $orderGallery;
    }

}