<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\RoomFeature;
use Navicu\Core\Domain\Model\Entity\Bedroom;
use Navicu\Core\Domain\Model\Entity\Bed;

/**
 * Clase LoadRoomData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema de todas las habitaciones.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRoomData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        //Se obtiene todas los establecimientos
        $properties = $manager->getRepository("NavicuDomain:Property")
            ->findAll();

        $room = $manager->getRepository("NavicuDomain:RoomType");
        $feature = $manager->getRepository("NavicuDomain:RoomFeatureType");
        $category = $manager->getRepository("NavicuDomain:Category");
        
        //tipos de habitaciones
        $basicApartament = $room->findOneByTitle('Individual');
        $deluxeApartament = $room->findOneByTitle('Doble');
        $exclusiveApartament = $room->findOneByTitle('Suite');

        //Espacios de las habitaciones
        $habitacion = $feature->findOneByTitle('habitación');
        $baño = $feature->findOneByTitle('baño');
        $salon =  $feature->findOneByTitle('salón');
        $comedor =  $feature->findOneByTitle('comedor');
        $terraza =  $feature->findOneByTitle('terraza');

        //Servicios de la habitacion
        $nofumadores = $feature->findOneByTitle('Habitaciones para no fumadores');
        $lavadora = $feature->findOneByTitle('Lavadora');
        $wifi = $feature->findOneByTitle('Wifi');
        $tv = $feature->findOneByTitle('Tv');
        $computadora = $feature->findOneByTitle('Computadora');

        foreach ($properties as $currentProperty) {
            
            // Habitación de tipo Basic Apartament
            $room = new Room();
            $room->setType($basicApartament);
            $room->setBaseAvailability(rand(5,10));
            $room->setMinPeople(rand(1,2));
            $room->setMaxPeople(rand(2,2));
            $room->setVariationTypePeople(rand(1,2));
            $room->setBaseAvailability(3);
            $room->setSmokingPolicy(true);
            $room->setMaxPricePerson(43000);
            $room->setMinPricePerson(18000);
            $room->setAmountRooms(12);
            $room->setLowRate(round(10.0 + mt_rand() / mt_getrandmax() * (15-10.0),2));
            $room->setProperty($currentProperty);
            
            //camas
            $beds1 = new Bedroom();
            $beds1->setAmountPeople(5);
            $beds1->setBath(false);
            $beds1->addBed(new Bed(Bed::SINGLE80,1));
            $beds1->addBed(new Bed(Bed::DOBLE,2));
            $beds2 = new Bedroom();
            $beds2->setAmountPeople(3);
            $beds2->setBath(false);
            $beds2->addBed(new Bed(Bed::SINGLE90,2));
            $beds2->addBed(new Bed(Bed::DOBLE,2));
            $room->addBedroom($beds1);
            $room->addBedroom($beds2);

            //espacios
            $space = new RoomFeature();
            $space->setFeature($habitacion);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($baño);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);

            //servicios
            $service = new RoomFeature();
            $service->setFeature($wifi);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($tv);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $manager->persist($room);
            $manager->flush();

            // Habitación de tipo Deluxe Apartament
            $room = new Room();
            $room->setType($deluxeApartament);
            $room->setBaseAvailability(rand(5,10));
            $room->setMinPeople(rand(1,2));
            $room->setMaxPeople(rand(2,2));
            $room->setVariationTypePeople(rand(1,2));
            $room->setAmountRooms(8);
            $room->setSmokingPolicy(false);
            $room->setMaxPricePerson(50000);
            $room->setMinPricePerson(12000);
            $room->setLowRate(round(10.0 + mt_rand() / mt_getrandmax() * (15-10.0),2));
            $room->setProperty($currentProperty);

            //camas
            /*$beds1 = new Bedroom();
            $beds1->setAmountPeople(9);
            $beds1->addBed(new Bed(Bed::SINGLE90,1));
            $beds1->addBed(new Bed(Bed::DOBLE,2));
            $beds2 = new Bedroom();
            $beds2->setAmountPeople(13);
            $beds2->addBed(new Bed(Bed::SINGLE110,2));
            $beds2->addBed(new Bed(Bed::DOBLE,2));
            $room->addBedroom($beds1);
            $room->addBedroom($beds2);*/

            //espacios
            $space = new RoomFeature();
            $space->setFeature($habitacion);
            $space->setAmount(2);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($baño);
            $space->setAmount(3);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($terraza);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);


            //servicios
            $service = new RoomFeature();
            $service->setFeature($wifi);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($tv);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($nofumadores);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($computadora);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $manager->persist($room);
            $manager->flush();

            

            // Habitación de tipo Exclusive Apartament
            $room = new Room();
            $room->setType($exclusiveApartament);
            $room->setBaseAvailability(rand(5,10));
            $room->setMinPeople(rand(1,2));
            $room->setMaxPeople(rand(2,2));
            $room->setAmountRooms(4);
            $room->setSmokingPolicy(true);
            $room->setMaxPricePerson(20000);
            $room->setMinPricePerson(7000);
            $room->setVariationTypePeople(rand(1,2));
            $room->setLowRate(round(10.0 + mt_rand() / mt_getrandmax() * (15-10.0),2));
            $room->setProperty($currentProperty);

            /*//camas
            $beds1 = new Bedroom();
            $beds1->setAmountPeople(1);
            $beds1->addBed(new Bed(Bed::SINGLE110,1));
            $beds1->addBed(new Bed(Bed::DOBLE,2));
            $beds2 = new Bedroom();
            $beds2->setAmountPeople(2);
            $beds2->addBed(new Bed(Bed::SINGLE80,2));
            $beds2->addBed(new Bed(Bed::DOBLE,2));
            $room->addBedroom($beds1);
            $room->addBedroom($beds2);*/

            //espacios
            $space = new RoomFeature();
            $space->setFeature($habitacion);
            $space->setAmount(4);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($baño);
            $space->setAmount(5);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($terraza);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($salon);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);

            $space = new RoomFeature();
            $space->setFeature($comedor);
            $space->setAmount(1);
            $space->setRoom($room);
            $room->addFeature($space);

            //servicios
            $service = new RoomFeature();
            $service->setFeature($wifi);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($tv);
            $service->setAmount(3);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($nofumadores);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($computadora);
            $service->setAmount(3);
            $service->setRoom($room);
            $room->addFeature($service);

            $service = new RoomFeature();
            $service->setFeature($lavadora);
            $service->setAmount(1);
            $service->setRoom($room);
            $room->addFeature($service);

            $manager->persist($room);
            $manager->flush();
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 60;
    }
}
