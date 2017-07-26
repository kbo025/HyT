<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\DeleteTempFavoriteImage;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* Comando 'Eliminar una imagen favorita en espacio Temporal'
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 08/07/2015
*/

class DeleteTempFavoriteImageCommand extends CommandBase implements Command
{

    /*
    * La variable del slug del establecimiento
    */
    protected $slug;

    /*
    * Representa el path de la imagen favorita a eliminar
    */
    protected $path;

    /*
    * Representa la sub Galería de la imagen favorita a eliminar
    */
    protected $subGallery;

   
    public function __construct(
        $slug = null,
        $path = null,
        $subGallery = null)
    {  
       $this->slug = $slug;
       $this->path = $path;
       $this->subGallery = $subGallery;
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
    * La Función representa una método Get sobre el atributo SubGallery
    */
    public function getSubGallery()
    {
        return $this->subGallery;
    }
    
    /**
    * La Función representa una método Set sobre el atributo SubGallery
    */
    public function setSubGallery($subGallery)
    {
        $this->subGallery = $subGallery;
    }
}