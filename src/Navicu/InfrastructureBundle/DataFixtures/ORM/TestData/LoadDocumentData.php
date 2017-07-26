<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Document;

/**
 * Clase LoadDocumentData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de los archivos del sistema.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadDocumentData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {   
        //Se cargan las imagenes del establecimiento
        for ($i = 1; $i <= 24; $i++) { 
            $currentDocument = new Document();
            $currentDocument->setName('image_profile_Property'.$i);
            $currentDocument->setFileName('c'.$i.'.jpg');

            $manager->persist($currentDocument);
            $manager->flush();
        }

        //Se cargan las imagenes del establecimiento
        for ($i = 1; $i <= 27; $i++) { 
            $currentDocument = new Document();
            $currentDocument->setName('image_profile_Room'.$i);
            $currentDocument->setFileName('r'.$i.'.jpg');

            $manager->persist($currentDocument);
            $manager->flush();
        }

        //Se cargan las imagenes de los destinos
        for ($i = 1; $i <= 4; $i++) { 
            $currentDocument = new Document();
            $currentDocument->setName('image_destiny'.$i);
            $currentDocument->setFileName('d'.$i.'.jpg');

            $manager->persist($currentDocument);
            $manager->flush();
        }

        //Se cargan las imagenes del historial
        for ($i = 1; $i <= 4; $i++) { 
            $currentDocument = new Document();
            $currentDocument->setName('image_history'.$i);
            $currentDocument->setFileName('h'.$i.'.jpg');

            $manager->persist($currentDocument);
            $manager->flush();
        }

        //Se cargan las imagenes del historial
        for ($i = 1; $i <= 3; $i++) { 
            $currentDocument = new Document();
            $currentDocument->setName('image_offer'.$i);
            $currentDocument->setFileName('l'.$i.'.jpg');

            $manager->persist($currentDocument);
            $manager->flush();
        }

    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 10;
    }
}