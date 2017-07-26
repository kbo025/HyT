<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\EditTempImage;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* Comando 'Editar una imagen en espacio Temporal'
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 01/07/2015
*/

class EditTempImageCommand implements Command
{
    /**
    * Representa el path de la imagen a editar
    */
    protected $path;

    /**
    * Representa el nombre de imagen a editar
    */
    protected $name;

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
    * Representa el ID de la SubGalería a eliminar
    */
    protected $idSubGallery;

    /**
    *   constructor de la clase
    *   @param $image File
    */
    public function __construct(
        $path = null,
        $name = null, 
        $slug = null, 
        $gallery = null, 
        $subGallery = null,
        $idSubGallery = null)
    {
       $this->path = $path;
       $this->name = $name;
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
            'name' => $this->name,
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
    * La Función representa una método Get sobre el atributo Name
    */
    public function getName()
    {
        return $this->name;
    }
    
    /**
    * La Función representa una método Set sobre el atributo Name
    */
    public function setName($name)
    {
        $this->name = $name;
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