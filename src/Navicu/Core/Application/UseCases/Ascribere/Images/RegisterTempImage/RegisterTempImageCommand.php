<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\RegisterTempImage;

use Navicu\Core\Application\Contract\Command;

/**
* Comando 'Registrar una imagen en espacio Temporal'
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 25/06/2015
*/

class RegisterTempImageCommand implements Command
{

    /*
    * Es la variable la direccion del archivo de la imagen
    */
    protected $pathImage;

    /**
    * Es una la imagen a procesar en el caso de uso de las imagen
    */
    protected $image;

    /**
    * Es la propiedad que representa el Id de la galería
    */
    protected $idSubGallery;        

    /**
    * La variable representa el slug del establecimiento temporal
    */
    protected $slug;

    /**
    * El nombre de la imagen a cargar
    */
    protected $name;

    /**
    * Representa la galería donde se va almacenar (e.g: Property, User, etc)
    */
    protected $gallery;

    /*
    * Representa la sub Galería a la cual representa la imagen (e.g: Profile, Room, etc)
    */
    protected $subGallery;

    /**
    *   constructor de la clase
    *   @param $image File
    */
    public function __construct(
        $pathImage = null,
        $image = null, 
        $idSubGallery = null,
        $slug = null,
        $name = null, 
        $gallery = null, 
        $subGallery = null)
    {  
       $this->pathImage = $pathImage;
       $this->image = $image;
       $this->idSubGallery = $idSubGallery;
       $this->slug = $slug;
       $this->name = $name;
       $this->gallery = $gallery;
       $this->subGallery = $subGallery;
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
            'pathImage' => $this->pathImage,
            'image' => $this->image,
            'idSubGallery' => $this->idSubGallery,
            'slug' => $this->slug,
            'name' => $this->name,
            'gallery' => $this->gallery,
            'subGallery' => $this->subGallery);    
    }

    /**
    * La Función representa una método Get sobre el atributo pathImage
    */
    public function getPathImage()
    {
        return $this->pathImage;
    }
    
    /**
    * La Función representa una método Set sobre el atributo pathImage
    */
    public function setPathImage($pathImage)
    {
        $this->pathImage = $pathImage;
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
    */
    public function setIdSubGallery($idSubGallery)
    {
        $this->idSubGallery = $idSubGallery;
    }
    
    /**
    * La Función representa una método Get sobre el atributo Image
    */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
    * La Función representa una método Set sobre el atributo Image
    */
    public function setImage($image)
    {
        $this->image = $image;
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
}