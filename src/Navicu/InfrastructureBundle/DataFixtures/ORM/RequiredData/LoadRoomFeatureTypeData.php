<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Navicu\Core\Domain\Model\Entity\RoomFeatureType;

/**
 * Clase LoadRoomTypeData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de habitaciones que puede cargar un establecimiento
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRoomFeatureTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    { 
        $all = array(
            array(1,'Habitación',0,0,null,null),
            array(2,'Baño',0,0,null,null),
            array(3,'Balcón',0,0,null,null),
            array(4,'Terraza',0,0,null,null),
            array(6,'Comedor',0,0,null,null),
            array(7,'Cocina',0,0,null,null),
            array(8,'Salón',0,0,null,null),
            array(9,'Spa',0,0,null,null),
            array(10,'Piscina',0,0,null,null),
            array(11,'Jardín privado',0,0,null,null),
            array(12,'Lavandero',0,0,null,null),
            array(13,'otro',0,0,null,null),
            array(14,'Aire acondicionado',1,0,1,1),
            array(15,'Wifi',1,0,2,1),
            array(16,'Caja fuerte',1,0,6,1),
            array(17,'Minibar',1,0,8,1),
            array(18,'TV',1,0,11,1),
            array(19,'Carta de almohadas',1,0,15,1),
            array(20,'TV Pay-per-view',1,0,18,1),
            array(21,'Insonorización',1,0,21,1),
            array(22,'TV satelital',1,0,25,1),
            array(23,'TV por cable',1,0,28,1),
            array(24,'Llamadas internacionales gratuitas',1,0,30,1),
            array(25,'Llamadas locales gratuitas',1,0,32,1),
            array(26,'Plancha para ropa',1,0,33,1),
            array(27,'Radio',1,0,34,1),
            array(28,'Películas de pago',1,0,35,1),
            array(29,'Consola de videojuegos',1,0,36,1),
            array(30,'Computadora',1,0,37,1),
            array(31,'Cafetera',1,0,38,1),
            array(32,'Altavoces bluetooth',1,0,39,1),
            array(33,'Teléfono',1,0,40,1),
            array(34,'Ventilador',1,0,41,1),
            array(35,'Calefacción',1,0,42,1),
            array(36,'Armario',1,0,43,1),
            array(37,'Habitaciones comunicadas',1,0,44,1),
            array(38,'Mosquitero',1,0,45,1),
            array(39,'Reloj despertador',1,0,46,1),
            array(40,'Reproductor de Blu-ray',1,0,47,1),
            array(41,'Reproductor de CD',1,0,48,1),
            array(42,'Reproductor de DVD',1,0,49,1),
            array(43,'Vestidor',1,0,50,1),
            array(44,'Escritorio',1,0,51,1),
            array(45,'Chimenea',1,0,52,1),
            array(46,'Jacuzzi',1,0,5,2),
            array(47,'Amenities VIP',1,0,7,2),
            array(48,'Bañera de hidromasaje',1,0,9,2),
            array(49,'Amenities',1,0,13,2),
            array(50,'Secador de pelo',1,0,14,2),
            array(51,'Espejo de aumento',1,0,17,2),
            array(52,'Ducha',1,0,22,2),
            array(53,'Bañera',1,0,23,2),
            array(54,'Bidé',1,0,26,2),
            array(55,'Alojamiento Compartido',1,0,3,13),
            array(56,'Apta para discapacitados',1,0,4,13),
            array(57,'Apta para hipoalergénicos',1,0,10,13),
            array(58,'No fumadores',1,0,12,13),
            array(59,'Lavadora',1,0,16,13),
            array(60,'Nevera',1,0,16,13),
            array(61,'Secadora de ropa',1,0,19,13),
            array(62,'Horno',1,0,20,13),
            array(63,'Lavavajillas',1,0,24,13),
            array(64,'Microondas',1,0,27,13),
            array(65,'Tendedor portátil',1,0,29,13),
            array(66,'Menaje',1,0,31,13),
        );

        $types = array(
                'Habitación' => array('Individual','Doble','Triple','Cuádruple','Deluxe','Superior Room','Junior Suite','Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Baño' => array('Individual','Doble','Triple','Cuádruple','Deluxe','Superior Room','Junior Suite','Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Balcón' => array('Individual','Doble','Triple','Cuádruple','Deluxe','Superior Room','Junior Suite','Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Terraza' => array('Individual','Doble','Triple','Cuádruple','Deluxe','Superior Room','Junior Suite','Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Vestidor' => array('Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Comedor' => array('Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Cocina' => array('Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Salón' => array('Suite','Premium','Executive','Barco','Apartamento','Presidencial','Villa'),
                'Spa' => array('Apartamento','Presidencial','Villa'),
                'Piscina' => array('Apartamento','Presidencial','Villa'),
                'Jardín privado' => array('Apartamento','Presidencial','Villa'),
                'Lavandero' => array('Apartamento','Presidencial','Villa'),
                'otro' => array()
            );

        $rooms = $manager->getRepository('NavicuDomain:RoomType')->findBy(array('lvl'=>0));
        $roomtypes=array();
        foreach($rooms as $room)
        {
            $roomtypes[$room->getTitle()]=$room;
        }

        $allobjects = array();
        foreach ($all as $key => $service) {
            $allobjects[$key] = new RoomFeatureType();
            $allobjects[$key]->setId($service[0]);
            $allobjects[$key]->setTitle($service[1]);
            $allobjects[$key]->setType($service[2]);
            $allobjects[$key]->setTypeVal($service[3]);
            $allobjects[$key]->setPriority($service[4]);

            switch($service[5])
            {
                case 1:
                    $allobjects[$key]->setParent($allobjects[0]);
                break;
                case 2:
                    $allobjects[$key]->setParent($allobjects[1]);
                break;
                case 13:
                    $allobjects[$key]->setParent($allobjects[12]);
                break;
            }
            if ($service[5]==1) {
                $allobjects[$key]->setParent($allobjects[0]);
            }
            if ($service[5]==2) {
                $allobjects[$key]->setParent($allobjects[1]);
            }
            if ($service[5]==13) {
                $allobjects[$key]->setParent($allobjects[12]);
            }
            if($service[2]==0){
                foreach($types[$service[1]] as $roomtype)
                {
                    $allobjects[$key]->addRoomType($roomtypes[$roomtype]);
                }
            }
            $manager->persist($allobjects[$key]);
        }

        $manager->flush();
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 11;
    }
}