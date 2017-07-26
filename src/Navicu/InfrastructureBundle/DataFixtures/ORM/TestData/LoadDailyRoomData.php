<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\DailyRoom;

/**
 * Clase LoadDailyRoomData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los datos de las habitaciones por día.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadDailyRoomData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        //Se obtiene todas las habitaciones (rooms)
        /*$rooms = $manager->getRepository("NavicuDomain:Room")->findAll();


        foreach ($rooms as $currentRoom) {
            
            $currentDate = date_create();
            
            for ($i = 0; $i < 30 ; $i++) { 
                
                $dailyRoom = new DailyRoom();
                $dailyRoom->setRoom($currentRoom);
                $dailyRoom->setDate($currentDate);
                $dailyRoom->setIsCompleted(1);
                $dailyRoom->setAvailability(rand(7,20));
                $dailyRoom->setCutOff(0);
                $dailyRoom->setStopSell(rand(0,1));
                $dailyRoom->setMinNight(rand(1,2));
                $dailyRoom->setMaxNight(rand(3,5));

                $manager->persist($dailyRoom);
                $manager->flush();

                date_modify($currentDate, "+1 day");
            }
        }*/
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 120;
    }
}