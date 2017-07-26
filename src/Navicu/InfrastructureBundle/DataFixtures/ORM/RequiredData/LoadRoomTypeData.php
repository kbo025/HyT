<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Navicu\Core\Domain\Model\Entity\RoomType;
use Navicu\Core\Domain\Model\Entity\RoomFeatureType;

/**
 * Clase LoadRoomTypeData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de habitaciones que puede cargar un establecimiento
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRoomTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {  
        $types = array (
            array(
                'id' =>1,
                'name'=> 'Individual',
                'nameRooms'=> array(
                    array('id'=>15,'name'=>'Vista al jardín'),
                    array('id'=>16,'name'=>'Vista al océano'),
                    array('id'=>17,'name'=>'Vista a la piscina'),
                    array('id'=>18,'name'=>'Vista al mar'),
                    array('id'=>19,'name'=>'Vista a la montaña'),
                    array('id'=>20,'name'=>'Vista al lago'),
                    array('id'=>21,'name'=>'Vista al parque'),
                    array('id'=>22,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>2,
                'name'=> 'Doble',
                'nameRooms'=> array(
                    array('id'=>23,'name'=>'Vista al jardín'),
                    array('id'=>24,'name'=>'Vista al océano'),
                    array('id'=>25,'name'=>'Vista a la piscina'),
                    array('id'=>26,'name'=>'Vista al mar'),
                    array('id'=>27,'name'=>'Vista a la montaña'),
                    array('id'=>28,'name'=>'Vista al lago'),
                    array('id'=>29,'name'=>'Vista al parque'),
                    array('id'=>30,'name'=>'Vista a la ciudad'),
                    array('id'=>31,'name'=>'Motor Home'),
                ),
            ),
            array(
                'id' =>3,
                'name'=> 'Triple',
                'nameRooms'=> array(
                    array('id'=>32,'name'=>'Vista al jardín'),
                    array('id'=>33,'name'=>'Vista al océano'),
                    array('id'=>34,'name'=>'Vista a la piscina'),
                    array('id'=>35,'name'=>'Vista al mar'),
                    array('id'=>36,'name'=>'Vista a la montaña'),
                    array('id'=>37,'name'=>'Vista al lago'),
                    array('id'=>38,'name'=>'Vista al parque'),
                    array('id'=>39,'name'=>'Vista a la ciudad'),
                    array('id'=>40,'name'=>'Motor Home'),
                ),
            ),
            array(
                'id' =>4,
                'name'=> 'Cuádruple',
                'nameRooms'=> array(
                    array('id'=>41,'name'=>'Vista al jardín'),
                    array('id'=>42,'name'=>'Vista al océano'),
                    array('id'=>43,'name'=>'Vista a la piscina'),
                    array('id'=>44,'name'=>'Vista al mar'),
                    array('id'=>45,'name'=>'Vista a la montaña'),
                    array('id'=>46,'name'=>'Vista al lago'),
                    array('id'=>47,'name'=>'Vista al parque'),
                    array('id'=>48,'name'=>'Vista a la ciudad'),
                    array('id'=>49,'name'=>'Motor Home'),
                ),
            ),
            array(
                'id' =>5,
                'name'=> 'Deluxe',
                'nameRooms'=> array(
                    array('id'=>50,'name'=>'Vista al jardín'),
                    array('id'=>51,'name'=>'Vista al océano'),
                    array('id'=>52,'name'=>'Vista a la piscina'),
                    array('id'=>53,'name'=>'Vista al mar'),
                    array('id'=>54,'name'=>'Vista a la montaña'),
                    array('id'=>55,'name'=>'Vista al lago'),
                    array('id'=>56,'name'=>'Vista al parque'),
                    array('id'=>57,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>6,
                'name'=> 'Superior Room',
                'nameRooms'=> array(
                    array('id'=>58,'name'=>'Vista al jardín'),
                    array('id'=>59,'name'=>'Vista al océano'),
                    array('id'=>60,'name'=>'Vista a la piscina'),
                    array('id'=>61,'name'=>'Vista al mar'),
                    array('id'=>62,'name'=>'Vista a la montaña'),
                    array('id'=>63,'name'=>'Vista al lago'),
                    array('id'=>64,'name'=>'Vista al parque'),
                    array('id'=>65,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>7,
                'name'=> 'Junior Suite',
                'nameRooms'=> array(
                    array('id'=>66,'name'=>'Vista al jardín'),
                    array('id'=>67,'name'=>'Vista al océano'),
                    array('id'=>68,'name'=>'Vista a la piscina'),
                    array('id'=>69,'name'=>'Vista al mar'),
                    array('id'=>70,'name'=>'Vista a la montaña'),
                    array('id'=>71,'name'=>'Vista al lago'),
                    array('id'=>72,'name'=>'Vista al parque'),
                    array('id'=>73,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>8,
                'name'=> 'Suite',
                'nameRooms'=> array(
                    array('id'=>74,'name'=>'Vista al jardín'),
                    array('id'=>75,'name'=>'Vista al océano'),
                    array('id'=>76,'name'=>'Vista a la piscina'),
                    array('id'=>77,'name'=>'Vista al mar'),
                    array('id'=>78,'name'=>'Vista a la montaña'),
                    array('id'=>79,'name'=>'Vista al lago'),
                    array('id'=>80,'name'=>'Vista al parque'),
                    array('id'=>81,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>9,
                'name'=> 'Premium',
                'nameRooms'=> array(
                    array('id'=>82,'name'=>'Vista al jardín'),
                    array('id'=>83,'name'=>'Vista al océano'),
                    array('id'=>84,'name'=>'Vista a la piscina'),
                    array('id'=>85,'name'=>'Vista al mar'),
                    array('id'=>86,'name'=>'Vista a la montaña'),
                    array('id'=>87,'name'=>'Vista al lago'),
                    array('id'=>88,'name'=>'Vista al parque'),
                    array('id'=>89,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>10,
                'name'=> 'Executive',
                'nameRooms'=> array(
                    array('id'=>90,'name'=>'Vista al jardín'),
                    array('id'=>91,'name'=>'Vista al océano'),
                    array('id'=>92,'name'=>'Vista a la piscina'),
                    array('id'=>93,'name'=>'Vista al mar'),
                    array('id'=>94,'name'=>'Vista a la montaña'),
                    array('id'=>95,'name'=>'Vista al lago'),
                    array('id'=>96,'name'=>'Vista al parque'),
                    array('id'=>97,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>11,
                'name'=> 'Barco',
                'nameRooms'=> array(
                    array('id'=>98,'name'=>'Vista al jardín'),
                    array('id'=>99,'name'=>'Vista al océano'),
                    array('id'=>100,'name'=>'Vista a la piscina'),
                    array('id'=>101,'name'=>'Vista al mar'),
                ),
            ),
            array(
                'id' =>12,
                'name'=> 'Apartamento',
                'nameRooms'=> array(
                    array('id'=>102,'name'=>'Vista al jardín'),
                    array('id'=>103,'name'=>'Vista al océano'),
                    array('id'=>104,'name'=>'Vista a la piscina'),
                    array('id'=>105,'name'=>'Vista al mar'),
                    array('id'=>106,'name'=>'Vista a la montaña'),
                    array('id'=>107,'name'=>'Vista al lago'),
                    array('id'=>108,'name'=>'Vista al parque'),
                    array('id'=>109,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>13,
                'name'=> 'Presidencial',
                'nameRooms'=> array(
                    array('id'=>110,'name'=>'Vista al jardín'),
                    array('id'=>111,'name'=>'Vista al océano'),
                    array('id'=>112,'name'=>'Vista a la piscina'),
                    array('id'=>113,'name'=>'Vista al mar'),
                    array('id'=>114,'name'=>'Vista a la montaña'),
                    array('id'=>115,'name'=>'Vista al lago'),
                    array('id'=>116,'name'=>'Vista al parque'),
                    array('id'=>117,'name'=>'Vista a la ciudad'),
                ),
            ),
            array(
                'id' =>14,
                'name'=> 'Villa',
                'nameRooms'=> array(
                    array('id'=>118,'name'=>'Vista al jardín'),
                    array('id'=>119,'name'=>'Vista al océano'),
                    array('id'=>120,'name'=>'Vista a la piscina'),
                    array('id'=>121,'name'=>'Vista al mar'),
                    array('id'=>122,'name'=>'Vista a la montaña'),
                    array('id'=>123,'name'=>'Vista al lago'),
                    array('id'=>124,'name'=>'Vista al parque'),
                    array('id'=>125,'name'=>'Vista a la ciudad'),
                ),
            )
        );
        foreach ($types as $type) {
            $roomtype = new RoomType();
            $roomtype->setId($type['id']);
            $roomtype->setTitle($type['name']);
            $roomtype->setLvl(0);
            foreach($type['nameRooms'] as $subtype)
            {
                $room_subtype = new RoomType();
                $room_subtype->setTitle($subtype['name']);
                $room_subtype->setId($subtype['id']);
                $room_subtype->setLvl(1);
                $room_subtype->setParent($roomtype);
                $manager->persist($room_subtype);
            }
            $manager->persist($roomtype);
        }
        $manager->flush();
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