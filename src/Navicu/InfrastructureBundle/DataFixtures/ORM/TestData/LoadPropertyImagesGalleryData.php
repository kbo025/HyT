<?php

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;

/**
 * Clase LoadRoomImagesGalleryData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema 
 * para las imagenes perteneciente a la galería de imagenes del establecimiento
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 * @version 28/05/2015
 */

class LoadPropertyImagesGalleryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        $galleries = $manager->getRepository("NavicuDomain:PropertyGallery")
            ->findAll();

        $j = 1;
        
        foreach ($galleries as $currentGallery) {
            

            for ($i=0; $i < 2 ; $i++) { 
                
                $propertyImage = new PropertyImagesGallery();

                $image = $manager->getRepository("NavicuDomain:Document")
                    ->find($j);

                $propertyImage->setPropertyGallery($currentGallery);
                $propertyImage->setImage($image);
                
                $manager->persist($propertyImage);
                $manager->flush();
                
                $j++;
            }
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 23;
    }
}