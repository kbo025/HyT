<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Pack;

/**
 * Clase LoadPackData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para todos los servicios o Pack de las habitaciones.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadPackData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {   
        //Se obtiene todas las habitaciones (rooms)     
        $rooms = $manager->getRepository("NavicuDomain:Room")
            ->findAll();

        $category = $manager->getRepository("NavicuDomain:Category");
        
        //Tipos de servicios existentes
        $roomOnly = $category->findOneByTitle("Room Only");
        $breakFast = $category->findOneByTitle("BreakFast Included");
        $nonRedundable = $category->findOneByTitle("Non Redundable");
        $egenciaPreferred = $category->findOneByTitle("Egencia Preferred");
        
        
        foreach ($rooms as $currentRoom) {

            $basePolicy = $currentRoom->getProperty()->getBasePolicy();
            $propertyCancellationPolicy = $manager->getRepository("NavicuDomain:PropertyCancellationPolicy")
            ->findByProperty($currentRoom->getProperty()->getId());

            //Room Only
            $pack = new Pack();
            $pack->setType($roomOnly);
            $pack->setRoom($currentRoom);
            $pack->addPackCancellationPolicy($basePolicy);

            $manager->persist($pack);
            $manager->flush();

            //BreakFast Included
            $pack1 = new Pack();
            $pack1->setType($breakFast);
            $pack1->setRoom($currentRoom);
            $pack1->addPackCancellationPolicy($basePolicy);

            $manager->persist($pack1);
            $manager->flush();

            //Non Redundable
            $pack2 = new Pack();
            $pack2->setType($nonRedundable);
            $pack2->setRoom($currentRoom);
            $pack2->addPackCancellationPolicy($basePolicy);

            $manager->persist($pack2);
            $manager->flush();

            //Egencia Preferred
            $pack3 = new Pack();
            $pack3->setType($egenciaPreferred);
            $pack3->setRoom($currentRoom);

            foreach ($propertyCancellationPolicy as $policy) {
                $pack3->addPackCancellationPolicy($policy);
            }

            $manager->persist($pack3);
            $manager->flush();
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 70;
    }
}