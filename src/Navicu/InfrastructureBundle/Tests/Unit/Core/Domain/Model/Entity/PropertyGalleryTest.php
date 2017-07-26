<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * PropertyGallery (Galería de images del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class ProperGalleryTest extends \PHPUnit_Framework_TestCase
{   
    /**
    * La variable representa la entidad base (PropertyGallery)
    *
    * @var Object PropertyGallery
    */
    private $baseEntity;

    /**
    * La variable representa el establecimiento (Property)
    *
    * @var Object Property
    */
    private $property;

    /**
    * La variable representa el tipo de galeria (Category)
    *
    * @var Object Categoria
    */
    private $type;

    /**
    * La variable representa las imagenes de la galeria (PropertyImagesGallery)
    *
    * @var Object PropertyImagesGalerry
    */
    private $propertyImagesGallery;

    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new PropertyGallery();
        $this->property = new Property();
        $this->type = new Category();
        $this->propertyImagesGallery = new PropertyImagesGallery();        
    }

    /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $property
    *
    * @version 01/06/2015
    */
    public function testProperty()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: Set\Get Property\n";

        $this->baseEntity->setProperty($this->property);
         
        $this->assertTrue(
            $this->baseEntity->getProperty() == $this->property,
            "\n- Los métodos Set\Get del atributo Property no funciona\n");
    }

     /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $type
    *
    * @version 01/06/2015
    */
    public function testType()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: Set\Get Type (Category)\n";

        $this->baseEntity->setType($this->type);
         
        $this->assertTrue(
            $this->baseEntity->getType() == $this->type,
            "\n- Los métodos Set\Get del atributo Type no funciona\n");
    }

     /**
    * La función realiza la prueba unitaria del metodos add/remove y get
    * del atributo $propertyImagesGallery
    *
    * @version 01/06/2015
    */
    public function testPropertyImagesGallery()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: add/remove/get PropertyImagesGallery\n";

        $this->baseEntity->addImagesGallery($this->propertyImagesGallery);
        
        $imagesGallery = $this->baseEntity->getImagesGallery();
        
        foreach ($imagesGallery as $currentGallery) {
            
            $this->assertTrue($currentGallery == $this->propertyImagesGallery,
                "\n- Los métodos add/Get del atributo ImagesGallery no funciona\n");

            $this->baseEntity->removeImagesGallery($this->propertyImagesGallery);
        }

        $imagesGallery = $this->baseEntity->getImagesGallery();
        $this->assertTrue(count($imagesGallery) == 0,
                "\n- Los métodos remove/Get del atributo ImagesGallery no funciona\n");                    
    }
}
