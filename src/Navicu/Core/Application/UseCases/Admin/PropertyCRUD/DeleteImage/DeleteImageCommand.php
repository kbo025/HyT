<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteImage;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de DeleteImage
 *
 * Class DeleteImageCommand
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\UploadImage
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 02/12/2015
 */
class DeleteImageCommand implements Command
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
     * @var id de la imagen en la entidad Document
     */
    private $idImage;

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'idGallery' => $this->idGallery,
                'typeGallery' => $this->typeGallery,
                'idImage' => $this->idImage
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
     * @return id
     */
    public function getIdImage()
    {
        return $this->idImage;
    }

    /**
     * @param id $idImage
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;
    }
}