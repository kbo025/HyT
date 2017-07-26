<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\RoomPackLinkage;

/**
 * Clase LoadRoomPackLinkageData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema de las vinculaciones entre servicios y habitaciones.
 *
 * @author FreddyContreras <fcontreras@navicu.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRoomPackLinkageData extends AbstractFixture implements OrderedFixtureInterface
{   
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {  /* 
        //Se obtiene todas las habitaciones (rooms)
        $rooms = $manager->getRepository("NavicuDomain:Room")->findAll();
        $i = 0;

        foreach ($rooms as $currentRoom) {
            
            foreach ($currentRoom->getPackages() as $currentPack) {

                //Si entra en este condicional se cargan vinculaciones
                //Rango de fechas : Fecha actual / Fecha actual +5 días
                if(rand(1,5) < 2) {
                        
                    $startDate = date_create();
                    $endDate = date_create()->modify("+5 day");

                    $roomPackLinkage = new RoomPackLinkage();
                    $roomPackLinkage->setParent($currentRoom);
                    $roomPackLinkage->setChild($currentPack);
                    $roomPackLinkage->setStartDate($startDate);
                    $roomPackLinkage->setEndDate($endDate);
                    $roomPackLinkage->setIsLinkedAvailability(rand(-5,5));
                    $roomPackLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $roomPackLinkage->setIsLinkedMinNight(rand(-5,5));

                    $manager->persist($roomPackLinkage);
                    $manager->flush();
                }
                
                //Si entra en este condicional se cargan vinculaciones
                //Carga de dias : Fecha actual + 10 días / Fecha actual + 15 días                
                if(rand(1,5) < 2) {

                    $startDate = date_create()->modify("+10 day");
                    $endDate = date_create()->modify("+15 day");
                    
                    $roomPackLinkage = new RoomPackLinkage();
                    $roomPackLinkage->setParent($currentRoom);
                    $roomPackLinkage->setChild($currentPack);
                    $roomPackLinkage->setStartDate($startDate);
                    $roomPackLinkage->setEndDate($endDate);
                    $roomPackLinkage->setIsLinkedAvailability(rand(-5,5));
                    $roomPackLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $roomPackLinkage->setIsLinkedMinNight(rand(-5,5));

                    $manager->persist($roomPackLinkage);
                    $manager->flush();
                }
            }
        }
        */
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 100;
    }
}