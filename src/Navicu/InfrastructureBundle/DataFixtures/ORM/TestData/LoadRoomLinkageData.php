<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\RoomLinkage;

/**
 * Clase LoadRoomLinkageData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema de las vinculaciones entre habitaciones.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRoomLinkageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {   /*     
        Se obtiene todas las habitaciones
        $rooms = $manager->getRepository("NavicuDomain:Room")->findAll();
        $i = 0;
        
        foreach ($rooms as $currentRoom) {

            //Si no es la primer habitación por establecimiento
            //La división es entre 3 porque son 3 habitaciones por establecimiento
            if($i%3 !=  0) {

                //Si entra en este condicional se cargan vinculaciones
                //Rango de fechas : Fecha actual / Fecha actual + 5 días               
                if(rand(0,1) == 1) {
                    
                    $startDate = date_create();
                    $endDate = date_create()->modify("+5 day");

                    $roomLinkage = new RoomLinkage();
                    $roomLinkage->setParent($parentRoom);
                    $roomLinkage->setChild($currentRoom);
                    $roomLinkage->setStartDate($startDate);
                    $roomLinkage->setEndDate($endDate);
                    $roomLinkage->setIsLinkedAvailability(rand(-5,5));
                    $roomLinkage->setIsLinkedStopSell(rand(0,1));
                    $roomLinkage->setIsLinkedCutOff(rand(0,1));
                    $roomLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $roomLinkage->setIsLinkedMinNight(rand(-5,5));

                    $manager->persist($roomLinkage);
                    $manager->flush();
                }

                //Si entra en este condicional se cargan vinculaciones
                //Rango de fechas : Fecha actual + 25 días / Fecha actual + 29 días
                if(rand(0,1) == 1) {

                    $startDate = date_create()->modify("+25 day");
                    $endDate = date_create()->modify("+30 day");

                    $roomLinkage = new RoomLinkage();
                    $roomLinkage->setParent($parentRoom);
                    $roomLinkage->setChild($currentRoom);
                    $roomLinkage->setStartDate($startDate);
                    $roomLinkage->setEndDate($endDate);
                    $roomLinkage->setIsLinkedAvailability(rand(-5,5));
                    $roomLinkage->setIsLinkedStopSell(rand(0,1));
                    $roomLinkage->setIsLinkedCutOff(rand(0,1));
                    $roomLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $roomLinkage->setIsLinkedMinNight(rand(-5,5));

                    $manager->persist($roomLinkage);
                    $manager->flush();
                }    

            } else {
                //$parentRom almacena la primera habitación por establecimiento
                //como habitación padre en las vinculaciones de habitación a habitación
                $parentRoom = $currentRoom;
            }

            $i++;
        }
        */
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 90;
    }
}