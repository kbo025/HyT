<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Document;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * RoomImagesGallery (Images de la habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class RoomImagesGalleryTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La variable representa la entidad base (RoomImagesGallery)
    *
    * @var Object RoomImagesGallery
    */
    private $baseEntity;

    /**
    * La variable representa la habitación (Room)
    *
    * @var Object Room
    */
    private $room;

    /**
    * La variable representa la imagen (Room)
    *
    * @var Object Document
    */
    private $image;
    
    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new RoomImagesGallery();
        $this->room = new room();
        $this->image = new Document();        
    }

    /**
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $room
    *
    * @version 01/06/2015
    */
    public function testroom()
    {
        echo "------------------------------\n";
        echo "* Clase: RoomImagesGallery\n";
        echo "* Prueba Unitaria: Set\Get room\n";

        $this->baseEntity->setRoom($this->room);
         
        $this->assertTrue(
            $this->baseEntity->getroom() == $this->room,
            "\n- Los métodos Set\Get del atributo room no funciona\n");
    }

    /** 
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $image
    * @version 01/06/2015
    */
    public function testImage()
    {
        echo "------------------------------\n";
        echo "* Clase: RoomImagesGallery\n";
        echo "* Prueba Unitaria: Set\Get Image\n";

        $this->baseEntity->setImage($this->image);
         
        $this->assertTrue(
            $this->baseEntity->getImage() == $this->image,
            "\n- Los métodos Set\Get del atributo Image no funciona\n");
    }
}