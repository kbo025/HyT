<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\DeleteTempImage;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* Comando 'Eliminar una imagen en espacio Temporal'
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 30/06/2015
*/

class DeleteTempImageCommand extends CommandBase implements Command
{
    /**
    * Representa el path de la imagen a eliminar
    */
    protected $path;

    /**
    * La variable representa el slug del establecimiento temporal
    */
    protected $slug;

    /**
    * Representa la galería donde se va almacenar (e.g: Property, User, etc)
    */
    protected $gallery;

    /*
    * Representa la sub Galería a la cual representa la imagen (e.g: Profile, Room, etc)
    */
    protected $subGallery;

    /*
    * Representa el id de la subGalleria de la cual se eliminará la imagen
    */
    protected $idSubGallery;

    /**
    *   constructor de la clase
    *   @param $image File
    */
    public function __construct(
        $path = null, 
        $slug = null, 
        $gallery = null, 
        $subGallery = null,
        $idSubGallery = null)
    {
       $this->path = $path;
       $this->slug = $slug;
       $this->gallery = $gallery;
       $this->subGallery = $subGallery;
       $this->idSubGallery = $idSubGallery;
    }

    /**
    * La función retorna todos los atributos de la clase en un arreglo
    * 
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @return Array
    */
    public function getRequest()
    {
        return array(
            'path' => $this->path,
            'slug' => $this->slug,
            'gallery' => $this->gallery,
            'subGallery' => $this->subGallery,
            'idSubGallery' => $this->idSubGallery);    
    }
    
    /**
    * La Función representa una método Get sobre el atributo Path
    */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
    * La Función representa una método Set sobre el atributo Path
    */
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    /**
    * La Función representa una método Get sobre el atributo Slug
    */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
    * La Función representa una método Set sobre el atributo Slug
    */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
    
    /**
    * La Función representa una método Get sobre el atributo Gallery
    */
    public function getGallery()
    {
        return $this->gallery;
    }
    
    /**
    * La Función representa una método Set sobre el atributo Gallery
    */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery; 
    }
    
    /**
    * La Función representa una método Get sobre el atributo SubGallery
    */
    public function getSubGallery()
    {
        return $this->subGallery;
    }

    /**
    * La Función representa una método Set sobre el atributo SubGallery
    */    public function setSubGallery($subGallery)
    {
        $this->subGallery = $subGallery;
    }

        /**
    * La Función representa una método Get sobre el atributo idSubGallery
    */
    public function getIdSubGallery()
    {
        return $this->idSubGallery;
    }

    /**
    * La Función representa una método Set sobre el atributo idSubGallery
    */    public function setIdSubGallery($idSubGallery)
    {
        $this->idSubGallery = $idSubGallery;
    }
}