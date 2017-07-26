<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * PropertyFavoritresImages (Images favoritas del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyFavoriteImagesTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La variable representa la entidad base (PropertyFavoriteImages)
    *
    * @var Object PropertyFavoriteImages
    */
    private $baseEntity;

    /**
    * La variable representa la imagen (Document)
    *
    * @var Object Document
    */
    private $image;

    /**
    * La variable representa el estableciminto (Property)
    *
    * @var Object Property
    */
    private $property;

    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new PropertyFavoriteImages();
        $this->image = new Document();
        $this->property = new Property();
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
        echo "* Clase: PropertyFavoriteImages\n";
        echo "* Prueba Unitaria: Set\Get Image\n";

        $this->baseEntity->setImage($this->image);
         
        $this->assertTrue(
            $this->baseEntity->getImage() == $this->image,
            "\n- Los métodos Set\Get del atributo Image no funciona\n");
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
        echo "* Clase: PropertyFavoriteImages\n";
        echo "* Prueba Unitaria: Set\Get Property\n";
        
        $this->baseEntity->setProperty($this->property);
         
        $this->assertTrue(
            $this->baseEntity->getProperty() == $this->property,
            "\n- Los métodos Set\Get del atributo Property no funciona\n");
    }    
}
