<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Navicu\Core\Domain\Model\Entity\ServiceType;

/**
 * Clase LoadPropertyServiceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los servicios adicionales que ofrece los establecimientos.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadServiceTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {   

        $services = array(
            1=>array(1,NULL,NULL,'Recepción',0,1,0,1,null),
                2=>array(2,1,1,'Horario de recepción',1,4,1,0,55),
                3=>array(3,1,1,'Horario de Conserjería',1,4,0,0,42),
                4=>array(4,1,1,'Caja Fuerte',1,0,0,0,38),
                5=>array(5,1,1,'Venta de entradas',1,0,0,0,42),
                6=>array(6,1,1,'Cajero Automatico',1,0,0,0,40),
                //6=>array(6,1,1,'Cajero Automático',1,0,0,0,40),
                7=>array(7,1,1,'Prensa',1,0,0,0,41),
                8=>array(8,1,1,'Guarda Equipaje',1,5,0,0,43),
                9=>array(9,1,1,'Información turística',1,0,0,0,37),
                //9=>array(9,1,1,'Información turística',1,0,0,0,37),
                10=>array(10,1,1,'Cambio de divisas',1,0,0,0,44),
                11=>array(11,1,1,'Servicio de despertador',1,0,0,0,45),
                12=>array(12,1,1,'Periódicos en el vestíbulo',1,0,0,0,null),
                13=>array(13,1,1,'Maletero/botones',1,0,0,0,null),
            14=>array(14,NULL,NULL,'Internet',0,1,0,0,null),
                15=>array(15,14,14,'En las zonas comunes por Wi-Fi',1,5,0,0,1),
                16=>array(16,14,14,'En la habitación por Wi-Fi',1,5,0,0,1),
                17=>array(17,14,14,'En la habitación por cable',1,5,0,0,34),
            18=>array(18,NULL,NULL,'Aparcamiento',0,1,0,1,2),
                19=>array(19,18,18,'Aparcamiento',1,5,0,0,2),
                20=>array(20,18,18,'Aparca coches',1,5,0,0,30),
                21=>array(21,18,18,'En el mismo edificio',1,5,0,0,46),
                22=>array(22,18,18,'En las cercanías',1,5,0,0,47),
            23=>array(23,NULL,NULL,'Restaurante',0,3,0,1,3),
            24=>array(24,NULL,NULL,'Bar',0,2,0,1,4),
            //25=>array(25,NULL,NULL,'Servicio a la habitación',0,1,0,0,null),
            25=>array(25,NULL,NULL,'Servicio a la habitación',0,1,0,0,null),
                26=>array(26,25,25,'Room Service',1,4,0,0,null),
                27=>array(27,25,25,'Lavado en seco',1,5,0,0,48),
                28=>array(28,25,25,'Limpieza',1,5,0,0,49),
                29=>array(29,25,25,'Lavandería',1,5,0,0,50),
                //29=>array(29,25,25,'Lavandería',1,5,0,0,50),
                30=>array(30,25,25,'Planchado',1,5,0,0,51),
                31=>array(31,25,25,'LimpiaBotas',1,5,0,0,52),
                32=>array(32,25,25,'Planchado de pantalones',1,5,0,0,53),
                33=>array(33,25,25,'Tintorería',1,5,0,0,null),
            34=>array(34,NULL,NULL,'Gimnasio',0,5,0,1,5),
            35=>array(35,NULL,NULL,'Spa',0,1,0,1,6),
                36=>array(36,35,35,'Sauna',1,5,0,0,6),
                37=>array(37,35,35,'Hammam',1,5,0,0,6),
                38=>array(38,35,35,'Bañera',1,5,0,0,6),
                39=>array(39,35,35,'Hidromasaje',1,5,0,0,6),
                40=>array(40,35,35,'Baños termales',1,5,0,0,6),
                41=>array(41,35,35,'Piscina interior',1,5,0,0,6),
                42=>array(42,35,35,'Salón de belleza',1,5,0,0,6),
                43=>array(43,35,35,'Masajes a la carta',1,5,0,0,6),
                44=>array(44,35,35,'Solarium',1,5,0,0,6),
                //44=>array(44,35,35,'Solárium',1,5,0,0,6),
                45=>array(45,35,35,'Baño de vapor',1,5,0,0,6),
                46=>array(46,35,35,'Ducha bitérmica',1,5,0,0,6),
                47=>array(47,35,35,'Cubo de hielo',1,5,0,0,6),
                48=>array(48,35,35,'Zona de relajación',1,5,0,0,6),
                49=>array(49,35,35,'Piscina de efectos',1,5,0,0,6),
            50=>array(50,NULL,NULL,'Salones',0,6,0,1,7),
            51=>array(51,NULL,NULL,'Zonas comunes',0,1,0,1,null),
                52=>array(52,51,51,'Internas',1,9,0,0,null),
                    53=>array(53,52,51,'Biblioteca',2,5,0,0,16),
                    54=>array(54,52,51,'Capilla',2,5,0,0,15),
                    55=>array(55,52,51,'Salón compartido',2,5,0,0,22),
                    56=>array(56,52,51,'Ascensor',2,0,0,0,54),
                    57=>array(57,52,51,'Asistencia turística',2,5,0,0,null),
                    58=>array(58,52,51,'Organización de bodas',2,5,0,0,null),
                    59=>array(59,52,51,'Sala de ordenadores',2,5,0,0,null),
                    60=>array(60,52,51,'Máquina de vending',2,5,0,0,23),
                    61=>array(61,52,51,'Sala de juegos',2,5,0,0,20),
                    62=>array(62,52,51,'Zona TV',2,5,0,0,21),
                    63=>array(63,52,51,'Zona VIP',2,5,0,0,9),
                    64=>array(64,52,51,'Guardería',2,5,0,0,10),
                    65=>array(65,52,51,'Casino',2,5,0,0,8),
                    66=>array(66,52,51,'Musica en vivo',2,5,0,0,29),
                    67=>array(67,52,51,'Comedor',2,5,0,0,17),
                68=>array(68,51,51,'Externas',1,9,0,0,null),
                    69=>array(69,68,51,'Jardín',2,0,0,0,11),
                    70=>array(70,68,51,'Terraza Solárium',2,0,0,0,18),
                    71=>array(71,68,51,'Terraza',2,0,0,0,19),
                    72=>array(72,68,51,'Zona BBQ',2,0,0,0,24),
                    73=>array(73,68,51,'Piscinas',2,8,0,0,12),
                    74=>array(74,68,51,'Playa',2,0,0,0,13),
                    75=>array(75,68,51,'Playa con acceso directo',2,0,0,0,26),
                    76=>array(76,68,51,'Cancha de deportes',2,5,0,0,25),
                    77=>array(77,68,51,'Pesca en las instalaciones',2,5,0,0,null),
                    78=>array(78,68,51,'Aguas termales',2,5,0,0,null),
                    79=>array(79,68,51,'Motoras en las instalaciones',2,5,0,0,null),
                    80=>array(80,68,51,'Rafting',2,5,0,0,null),
            81=>array(81,NULL,NULL,'Transporte',0,1,0,0,null),
                82=>array(82,81,81,'Bicicletas',1,5,0,0,27),
                83=>array(83,81,81,'Vehículos',1,5,0,0,28),
                84=>array(84,81,81,'Transporte al aeropuerto',1,5,0,0,14),
                85=>array(85,81,81,'Transporte en el área',1,5,0,0,29),
                86=>array(86,81,81,'Transporte a la playa',1,5,0,0,null),
                87=>array(87,81,81,'Transporte a la terminal barcos',1,5,0,0,null),
                88=>array(88,81,81,'Transporte al centro comercial',1,5,0,0,null),
                89=>array(89,81,81,'Limusina o coche con chófer',1,5,0,0,null),
                90=>array(90,81,81,'Recogida en la estación de tren',1,5,0,0,null),
                91=>array(91,81,81,'Tarifas con descuento en aparcamiento externo',1,5,0,0,33),
            92=>array(92,NULL,NULL,'Deporte y recreación',0,1,0,0,15),
                93=>array(93,92,92,'Outdoor',1,9,0,0,15),
                    94=>array(94,93,92,'Montaña',2,9,0,0,15),
                        95=>array(95,94,92,'Senderismo',3,5,0,0,15),
                        96=>array(96,94,92,'Alta montaña',3,5,0,0,15),
                        97=>array(97,94,92,'Expediciones',3,5,0,0,15),
                        98=>array(98,94,92,'Escalada',3,5,0,0,15),
                        99=>array(99,94,92,'Maratón de montaña',3,5,0,0,15),
                        100=>array(100,94,92,'Barranquismo',3,5,0,0,15),
                        101=>array(101,94,92,'Esquí',3,5,0,0,15),
                    102=>array(102,93,92,'Playa',2,9,0,0,15),
                        103=>array(103,102,92,'Esquí acuático',3,5,0,0,15),
                        104=>array(104,102,92,'Wakeboard',3,5,0,0,15),
                        105=>array(105,102,92,'Kitesurf',3,5,0,0,15),
                        106=>array(106,102,92,'Surf',3,5,0,0,15),
                        107=>array(107,102,92,'Bodysurfing',3,5,0,0,15),
                        108=>array(108,102,92,'Bodyboard',3,5,0,0,15),
                        109=>array(109,102,92,'Longboard',3,5,0,0,15),
                        110=>array(110,102,92,'Windsurf',3,5,0,0,15),
                        111=>array(111,102,92,'Vela ligera',3,5,0,0,15),
                        112=>array(112,102,92,'Motonáutica',3,5,0,0,15),
                        113=>array(113,102,92,'Kayakismo',3,5,0,0,15),
                        114=>array(114,102,92,'Remo',3,5,0,0,15),
                        115=>array(115,102,92,'Salto',3,5,0,0,15),
                        116=>array(116,102,92,'Buceo',3,5,0,0,15),
                        117=>array(117,102,92,'Esnórquel',3,5,0,0,15),
                        118=>array(118,102,92,'Fútbol de playa',3,5,0,0,15),
                        119=>array(119,102,92,'Juegos de pala',3,5,0,0,15),
                        120=>array(120,102,92,'Vóley playa',3,5,0,0,15),
                        121=>array(121,102,92,'Juegos de pala',3,5,0,0,15),
                        122=>array(122,102,92,'Vóley playa',3,5,0,0,15),
                        123=>array(123,102,92,'Barranquismo',3,5,0,0,15),
                        124=>array(124,102,92,'Submarinismo',3,5,0,0,15),
                        125=>array(125,102,92,'Visitas en barco',3,5,0,0,15),
                        126=>array(126,102,92,'Paddle surf',3,5,0,0,15),
                        127=>array(127,102,92,'Pesca deportiva',3,5,0,0,15),
                        128=>array(128,102,92,'Piragüismo',3,5,0,0,15),
                    129=>array(129,93,92,'Campo',2,9,0,0,15),
                        130=>array(130,129,92,'Bolas Criollas',3,5,0,0,15),
                        131=>array(131,129,92,'Lanzamiento',3,5,0,0,15),
                        132=>array(132,129,92,'Saltos',3,5,0,0,15),
                        133=>array(133,129,92,'Equitación',3,5,0,0,15),
                        134=>array(134,129,92,'Picnis',3,5,0,0,15),
                        135=>array(135,129,92,'Paddle',3,5,0,0,15),
                        136=>array(136,129,92,'Tenis',3,5,0,0,15),
                        137=>array(137,129,92,'Fútbol',3,5,0,0,15),
                        138=>array(138,129,92,'Voleyball',3,5,0,0,15),
                        139=>array(139,129,92,'Polideportivo',3,5,0,0,15),
                        140=>array(140,129,92,'Pista multiusos',3,5,0,0,15),
                    141=>array(141,93,92,'Campo de Golf',2,9,0,0,15),
                        142=>array(142,141,92,'Campo de prácticas',3,5,0,0,15),
                        143=>array(143,141,92,'Golf 9 hoyos',3,5,0,0,15),
                        144=>array(144,141,92,'Golf 18 hoyos',3,5,0,0,15),
                        145=>array(145,141,92,'Casa club',3,5,0,0,15),
                        146=>array(146,141,92,'Picnic',3,5,0,0,15),
                147=>array(147,92,92,'Indoor',1,9,0,0,15),
                    148=>array(148,147,92,'Salón',2,9,0,0,15),
                        149=>array(149,148,92,'Billar',3,5,0,0,15),
                        150=>array(150,148,92,'Ajedrez',3,5,0,0,15),
                        151=>array(151,148,92,'Ping pong',3,5,0,0,15),
                        152=>array(152,148,92,'Dardos',3,5,0,0,15),
        );
        
        $all = array();
        foreach ($services as $index => $service) {
            $all[$index] =  new ServiceType();
            $all[$index]->setId($service[0]);
            $all[$index]->setTitle($service[3]);
            $all[$index]->setType($service[5]);
            $all[$index]->setLvl($service[4]);
            $all[$index]->setPriority($service[8]);
            $all[$index]->setRequired($service[6]);
            $all[$index]->setGallery($service[7]);
            if(isset($service[2])){
                $all[$index]->setRoot($all[$service[2]]);
            }
            if(isset($service[1])){
                $all[$index]->setParent($all[$service[1]]);
            }
            $manager->persist($all[$index]);
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