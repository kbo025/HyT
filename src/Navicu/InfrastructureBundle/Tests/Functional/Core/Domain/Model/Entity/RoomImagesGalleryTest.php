<?php

namespace Navicu\InfrastructureBundle\Tests\Functional\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales
 * de la entidad RoomImagesGallery (Galeria de images de la habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class RoomImagesGalleryTest extends KernelTestCase
{
	 /**
	 * La función se encarga declarar el entity manager
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     * Metodo comprueba que las galería de imagenes tenga imagen asociada
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullImage() 
    {
        echo "------------------------------\n";
        echo "* Clase: RoomImagesGallery\n";
        echo "* Prueba Funcional: Tiene una imagen asociada\n";
        
        $roomsGallery = $this->em->getRepository('NavicuDomain:RoomImagesGallery')->findAll();
        
        foreach ($roomsGallery as $currentGallery) {
            $countImage = count($currentGallery->getImage());

            $this->assertTrue($countImage == 1, 
                "\n- La Galería de la habitación con ID:".$currentGallery->getId()." no tiene imagen asociada\n");
        }
    }

    /**
     * Metodo comprueba que las galería de imagenes de la habitación tenga
     * una habitación asociada
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullRoom() 
    {

        echo "------------------------------\n";
        echo "* Clase: RoomImagesGallery\n";
        echo "* Prueba Funcional: Tiene una habitación asociada\n";
        
        $roomsGallery = $this->em->getRepository('NavicuDomain:RoomImagesGallery')->findAll();
        
        foreach ($roomsGallery as $currentGallery) {
            $room = count($currentGallery->getRoom());

            $this->assertTrue($room == 1, 
                "\n- La Galería de la habitación con ID:".$currentGallery->getId()." no tiene habitación asociada\n");
        }
    }
}
