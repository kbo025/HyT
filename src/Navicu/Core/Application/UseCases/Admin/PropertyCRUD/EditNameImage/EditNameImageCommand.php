<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\EditNameImage;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de EditNameImage
 *
 * Class EditNameImageCommand
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\EditNameImage
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 02/12/2015
 */
class EditNameImageCommand implements Command
{
    /**
     * @var id de la imagen en la entidad Document
     */
    private $idImage;

    /**
     * @var nombre de la imagen
     */
    private $name;

    /**
     * Slug del hotel
     * @var $slug
     */
    private $slug;

    public function getRequest()
    {
        return
            array(
                'idImage' => $this->idImage,
                'name' => $this->name,
                'slug' => $this->slug
            );
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
     * @return nombre
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param nombre $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $slug Slug del hotel
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed Slug del hotel
     */
    public function getSlug()
    {
        return $this->slug;
    }
}