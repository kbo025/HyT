<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SetFavoriteImage;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de SetFavoriteImage
 *
 * Class SetFavoriteCommand
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\GetDataGalleries
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 23/11/2015
 */
class SetFavoriteImageCommand implements Command
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
     * @var id de la imagen
     */
    private $idImage;

    /**
     * @var Posición de ordenamiento en la galería de favoritos
     */
    private $orderGallery;

    /**
     *  Método getRequest devuelve un array con los parametros del command
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 23/11/2015
     * @return  Array
     */
    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'idGallery' => $this->idGallery,
                'typeGallery' => $this->typeGallery,
                'idImage' => $this->idImage,
                'orderGallery' => $this->orderGallery
        );
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getIdGallery()
    {
        return $this->idGallery;
    }

    /**
     * @param mixed $idGallery
     */
    public function setIdGallery($idGallery)
    {
        $this->idGallery = $idGallery;
    }

    /**
     * @return mixed
     */
    public function getTypeGallery()
    {
        return $this->typeGallery;
    }

    /**
     * @param mixed $typeGallery
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

    /**
     * @return Posición
     */
    public function getOrderGallery()
    {
        return $this->orderGallery;
    }

    /**
     * @param Posición $orderGallery
     */
    public function setOrderGallery($orderGallery)
    {
        $this->orderGallery = $orderGallery;
    }
}