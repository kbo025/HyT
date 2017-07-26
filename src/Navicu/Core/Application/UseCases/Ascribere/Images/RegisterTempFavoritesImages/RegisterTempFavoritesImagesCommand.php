<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\RegisterTempFavoritesImages;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* Comando 'Registrar las imagenes favoritas en espacio Temporal'
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 07/07/2015
*/

class RegisterTempFavoritesImagesCommand implements Command
{

    /*
    * Es la variable la direccion del archivo de la imagen
    */
    protected $favoritesImages;

    /*
    * La variable del slug del establecimiento
    */
    protected $slug;

   
    public function __construct(
        $slug = null,
        $favoritesImages = null)
    {  
       $this->slug = $slug;
       $this->favoritesImages = $favoritesImages;
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
            'slug' => $this->slug,
            'favoritesImages' => $this->favoritesImages);            
    }
    
    /**
    * La Función representa una método Get sobre el atributo favoritesImages
    */
    public function getFavoritesImages()
    {
        return $this->favoritesImages;
    }
    
    /**
    * La Función representa una método Set sobre el atributo favoritesImages
    */
    public function setFavoritesImages($favoritesImages)
    {
        $this->favoritesImages = $favoritesImages;
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
}