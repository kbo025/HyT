<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * room (Habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class RoomTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La variable representa la entidad base (Room)
    *
    * @var Object Room
    */
    private $baseEntity;

    /**
    * La variable representa imagen de la habitación (RoomImagesGallery)
    *
    * @var Object RoomImagesGallery
    */
    private $roomImagesGallery;

    /**
    * La variable representa imagen de perfil de la habitación (RoomImagesGallery)
    *
    * @var Object RoomImagesGallery
    */
    private $profileImage;

    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new Room();        
        $this->roomImagesGallery = new RoomImagesGallery();
        $this->profileImage = new RoomImagesGallery();
    }

    /**
    * La función realiza la prueba unitaria del metodos add/remove y get
    * del atributo $roomImagesGallery
    *
    * @version 01/06/2015
    */    
    public function testRoomImagesGallery()
    {
        echo "------------------------------\n";
        echo "* Clase: Room\n";
        echo "* Prueba Unitaria: add/remove/get RoomImagesGallery\n";

        $this->baseEntity->addImagesGallery($this->roomImagesGallery);
        
        $roomGallery = $this->baseEntity->getImagesGallery();
        
        foreach ($roomGallery as $currentGallery) {
            
            $this->assertTrue($currentGallery == $this->roomImagesGallery,
                "\n- Los métodos add/Get del atributo RoomImagesGallery no funciona\n");

            $this->baseEntity->removeImagesGallery($this->roomImagesGallery);
        }

        $roomGallery = $this->baseEntity->getImagesGallery();
        $this->assertTrue(count($roomGallery) == 0,
                "\n- Los métodos remove/Get del atributo imagesGallery no funciona\n");           
    }

    /** 
    * La función realiza la prueba unitaria del metodos get y set
    * del atributo $profileImage
    * @version 01/06/2015
    */
    public function testProfileImage()
    {
        echo "------------------------------\n";
        echo "* Clase: Room\n";
        echo "* Prueba Unitaria: Set\Get profileImage\n";

        $this->baseEntity->setProfileImage($this->profileImage);
         
        $this->assertTrue(
            $this->baseEntity->getProfileImage() == $this->profileImage,
            "\n- Los métodos Set\Get del atributo ProfileImage no funciona\n");
    }
}
