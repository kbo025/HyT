<?php

namespace Navicu\InfrastructureBundle\Tests\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales
 * de la entidad Room (habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class RoomTest extends KernelTestCase
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
     * Método comprueba que una habitación tenga mínimo una imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
	public function testMinImagesGallery() 
    {
		echo "------------------------------\n";
		echo "* Clase: Room\n";
		echo "* Prueba Funcional: Mínimo una Imagen por habitación\n";
		
		$rooms = $this->em->getRepository('NavicuDomain:Room')->findAll();
		
		foreach ($rooms as $currentRoom) {
            
			$countImages = count($currentRoom->getImagesGallery());

			$this->assertTrue($countImages > 0, 
				"\n- La habitación con ID:".$currentRoom->getId()." no tiene imagen\n");
		}
	}


    /**
     * Método comprueba las habitaciones tenga una imagen de perfil
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullImageProfile() 
    {
        echo "------------------------------\n";
        echo "* Clase: Room\n";
        echo "* Prueba Funcional: Habitación con una imagen de perfil\n";
        
        $rooms = $this->em->getRepository('NavicuDomain:Room')->findAll();
        
        foreach ($rooms as $currentRoom) {
            $countProfileImage = count($currentRoom->getProfileImage());

            $this->assertTrue($countProfileImage == 1, 
                "\n- La habitación con ID:".$currentRoom->getId()." no tiene imagen de perfil\n");
        }
    }
}
