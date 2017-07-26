<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * property (Establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{   
    /**
    * La variable representa la entidad base (Property)
    *
    * @var Object Property
    */
    private $baseEntity;

    /**
    * La variable representa la Galeria de imagenes del establecimiento (PropertyGallery)
    *
    * @var Object PropertyGallery
    */
    private $propertyGallery;

    /**
    * La variable representa las imagenes favoritas del establecimiento (PropertyFavoriteImages)
    *
    * @var Object PropertyFavoriteImages
    */
    private $propertyFavoriteImages;

    /**
    * La variable representa la imagen de perfil del establecimiento (PropertyFavoriteImages)
    *
    * @var Object PropertyFavoriteImages
    */
    private $profileImage;

    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new Property();
        $this->propertyGallery = new PropertyGallery();
        $this->propertyFavoriteImages = new PropertyFavoriteImages();        
        $this->profileImage = new PropertyFavoriteImages();
    }

    /**
    * La función realiza la prueba unitaria del metodos add/remove y get
    * del atributo $propertyGallery
    *
    * @version 01/06/2015
    */    
    public function testPropertyGallery()
    {       
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: add/remove/get PropertyGallery\n";

        $this->baseEntity->addPropertyGallery($this->propertyGallery);
        
        $propertyGallery = $this->baseEntity->getPropertyGallery();
        
        foreach ($propertyGallery as $currentGallery) {
            
            $this->assertTrue($currentGallery == $this->propertyGallery,
                "\n- Los métodos add/Get del atributo PropertyGallery no funciona\n");

            $this->baseEntity->removePropertyGallery($this->propertyGallery);
        }

        $propertyGallery = $this->baseEntity->getPropertyGallery();
        $this->assertTrue(count($propertyGallery) == 0,
                "\n- Los métodos remove/Get del atributo propertyGallery no funciona\n");           
    }

    /**
    * Realizando prueba de generar slug valido
    *
    * @version 06/07/2015
    */    
    public function testGeneratePropertySlug()
    {       
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Unitaria: Generar slug\n";

        $this->baseEntity->setName ( 'PRUEBA DE MAYUSCULAS' );
        $this->baseEntity->generateSlug();
        $this->assertEquals ( $this->baseEntity->getSlug() ,
            'prueba-de-mayusculas',
            "\n- método generarSlug() no funciona \n" );

        $this->baseEntity->setName ( 'Prúébá cón cáráctérés éspécíálés 1' );
        $this->baseEntity->generateSlug();
        $this->assertEquals ( $this->baseEntity->getSlug() ,
            'prueba-con-caracteres-especiales-1',
            "\n- método generarSlug() no funciona \n" );

        $this->baseEntity->setName ( 'prueba[???]con///Caracteres&&%&%esṕpeciales2' );
        $this->baseEntity->generateSlug();
        $this->assertEquals ( $this->baseEntity->getSlug() ,
            'pruebaconcaracteresespeciales2',
            "\n- método generarSlug() no funciona \n" );
    }


    /**
    * La función realiza la prueba unitaria del metodos add/remove y get
    * del atributo $propertyFavoriteImages
    *
    * @version 01/06/2015
    */    
    public function testPropertyFavoriteImages()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyFavoriteImages\n";
        echo "* Prueba Unitaria: add/remove/get PropertyFavoriteImages\n";

        $this->baseEntity->addPropertyFavoriteImage($this->propertyFavoriteImages);
        
        $propertyFavoriteGallery = $this->baseEntity->getPropertyFavoriteImages();
        
        foreach ($propertyFavoriteGallery as $currentFavorite) {
            
            $this->assertTrue($currentFavorite == $this->propertyFavoriteImages,
                "\n- Los métodos add/Get del atributo PropertyFavoriteImages no funciona\n");

            $this->baseEntity->removePropertyFavoriteImage($this->propertyFavoriteImages);
        }

        $propertyFavoriteImages = $this->baseEntity->getPropertyFavoriteImages();
        $this->assertTrue(count($propertyFavoriteImages) == 0,
                "\n- Los métodos remove/Get del atributo propertyGallery no funciona\n");           
    }

    /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $profileImage
    *
    * @version 01/06/2015
    */
    public function testProfileImage()
    {
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Unitaria: Set\Get profileImage\n";

        $this->baseEntity->setProfileImage($this->profileImage);
         
        $this->assertTrue(
            $this->baseEntity->getProfileImage() == $this->profileImage,
            "\n- Los métodos Set\Get del atributo ProfileImage no funciona\n");
    }
}
