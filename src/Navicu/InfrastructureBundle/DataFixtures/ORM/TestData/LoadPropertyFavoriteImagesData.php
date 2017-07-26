<?php

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 * Clase LoadPropertyFavoriteImagesData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema para las imagenes favoritas del establecimiento
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 * @version 27/05/2015
 */

class LoadPropertyFavoriteImagesData extends AbstractFixture implements OrderedFixtureInterface
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

        $j = 1;
        
        foreach ($properties as $currentProperty) {
            
            for ($i= 0; $i < 8; $i++) { 
                $favoriteImage = new PropertyFavoriteImages();
                
                $image = $manager->getRepository("NavicuDomain:Document")
                    ->find($j+1);

                $favoriteImage->setProperty($currentProperty);
                $favoriteImage->setImage($image);
                $manager->persist($favoriteImage);
                $manager->flush();    
                $j++;
            }

            $currentProperty->setProfileImage($favoriteImage);
            $manager->persist($currentProperty);
            $manager->flush();
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