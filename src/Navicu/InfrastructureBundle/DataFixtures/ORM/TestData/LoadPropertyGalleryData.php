<?php

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;

/**
 * Clase LoadRoomImagesGalleryData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema 
 * para las gallerias de imagenes del establecimiento
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 * @version 28/05/2015
 */

class LoadPropertyGalleryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        $properties = $manager->getRepository("NavicuDomain:Property")
            ->findAll();

        foreach ($properties as $currentProperty) {
            for ($i=1; $i <3 ; $i++) { 
                //Se obtiene el tipo de galería
                if ($i == 1)
                    $typeGallery = $manager->getRepository("NavicuDomain:ServiceType")
                        ->findOneByTitle('Piscina');
                else 
                    $typeGallery = $manager->getRepository("NavicuDomain:ServiceType")
                        ->findOneByTitle('Bar');

                $propertyGallery = new PropertyGallery();
                $propertyGallery->setProperty($currentProperty);
                $propertyGallery->setType($typeGallery);

                
                $manager->persist($propertyGallery);
                $manager->flush();  
            }            
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 22;
    }
}