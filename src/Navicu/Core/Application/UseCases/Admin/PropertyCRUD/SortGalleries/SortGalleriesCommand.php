<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SortGalleries;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de SortGalleries
 *
 * Class SortGalleries
 *
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\SortGalleries
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 14/01/2016
 */
class SortGalleriesCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * @var Información del ordenamiento de las imagenes favoritas
     */
    private $favoritesData;

    /**
     * @var Información del ordenamiento de las imagenes (rooms / otherGalleries)
     */
    private $galleriesData;

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return array(
            'slug' => $this->slug,
            'favoritesData' => $this->favoritesData,
            'galleriesData' => $this->galleriesData
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
     * @return Información
     */
    public function getFavoritesData()
    {
        return $this->favoritesData;
    }

    /**
     * @param Información $favoritesData
     */
    public function setFavoritesData($favoritesData)
    {
        $this->favoritesData = $favoritesData;
    }

    /**
     * @return Información
     */
    public function getGalleriesData()
    {
        return $this->galleriesData;
    }

    /**
     * @param Información $galleriesData
     */
    public function setGalleriesData($galleriesData)
    {
        $this->galleriesData = $galleriesData;
    }
}