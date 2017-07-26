<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\Document;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * PropertyImagesGallery (Images de la galeria del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyImagesGalleryTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La variable representa la entidad base (PropertyImagesGallery)
    *
    * @var Object PropertyImagesGallery
    */
    private $baseEntity;

    /**
    * La variable representa la galeria de imagen del establecimiento (PropertyGallery)
    *
    * @var Object PropertyGallery
    */
    private $propertyGallery;

    /**
    * La variable representa la imagen (Document)
    *
    * @var Object Document
    */
    private $image;
    
    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new PropertyImagesGallery();
        $this->propertyGallery = new PropertyGallery();
        $this->image = new Document();        
    }

    /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $propertyGallery
    *
    * @version 01/06/2015
    */
    public function testPropertyGallery()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyImagesGallery\n";
        echo "* Prueba Unitaria: Set\Get propertyGallery\n";

        $this->baseEntity->setPropertyGallery($this->propertyGallery);
         
        $this->assertTrue(
            $this->baseEntity->getPropertyGallery() == $this->propertyGallery,
            "\n- Los métodos Set\Get del atributo propertyGallery no funciona\n");
    }

    /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $image
    *
    * @version 01/06/2015
    */
    public function testImage()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyImagesGallery\n";
        echo "* Prueba Unitaria: Set\Get Image\n";

        $this->baseEntity->setImage($this->image);
         
        $this->assertTrue(
            $this->baseEntity->getImage() == $this->image,
            "\n- Los métodos Set\Get del atributo image no funciona\n");
    }
}
