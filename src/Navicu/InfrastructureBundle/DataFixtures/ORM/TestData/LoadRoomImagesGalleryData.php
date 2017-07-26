<?php

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;

/**
 * Clase LoadRoomImagesGalleryData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema para las imagenes de una habitación
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 * @version 28/05/2015
 */

class LoadRoomImagesGalleryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        $rooms = $manager->getRepository("NavicuDomain:Room")
            ->findAll();

        $j = 1;
        
        foreach ($rooms as $currentRoom) {
            
            for ($i= 0; $i < 3; $i++) { 
                $imageGallery = new RoomImagesGallery();

                $image = $manager->getRepository("NavicuDomain:Document")
                    ->find($j);

                $imageGallery->setRoom($currentRoom);
                $imageGallery->setImage($image);
                $manager->persist($imageGallery);
                $manager->flush();
                $j++;
            }
            $currentRoom->setProfileImage($imageGallery);
            $manager->persist($currentRoom);
            $manager->flush();
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 62;
    }
}