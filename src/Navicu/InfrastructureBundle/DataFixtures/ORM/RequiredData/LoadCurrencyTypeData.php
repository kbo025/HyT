<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\CurrencyType;

/**
 * Clase LoadCateogoryData "DataFixtures".
 *
 * La clase carga los datos pruebas del sistema, donde los datos tenga un comportamiento de lista.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadCurrencyTypeData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {

        $currencies = array(
            //array(1,'AED','Dirham de los Emiratos Árabes Unidos'),
            //array(2,'AFN','Afgani afgano'),
            //array(3,'ALL','Lek albanés'),
            //array(4,'AMD','Dram armenio'),
            //array(5,'ANG','Florín antillano neerlandés'),
            //array(6,'AOA','Kwanza angoleño'),
            //array(7,'ARS','Peso argentino'),
            //array(8,'AUD','Dólar australiano'),
            //array(9,'AWG','Florín arubeño'),
            //array(10,'AZN','Manat azerbaiyano'),
            //array(11,'BAM','Marco bosnioherzegovino'),
            //array(12,'BBD','Dólar de Barbados'),
            //array(13,'BDT','Taka bangladesí'),
            //array(14,'BGN','Lev búlgaro'),
            //array(15,'BHD','Dinar bahreiní'),
            //array(16,'BIF','Franco burundés'),
            //array(17,'BMD','Dólar de Bermuda'),
            //array(18,'BND','Dólar de Brunéi'),
            //array(19,'BOB','Boliviano'),
            //array(20,BOV','Mvdol boliviano'),
            //array(21,'BRL','Real brasileño'),
            //array(22,'BSD','Dólar bahameño'),
            //array(23,'BTN','Ngultrum de Bután'),
            //array(24,'BWP','Pula de Botsuana'),
            //array(25,'BYR','Rublo bielorruso'),
            //array(26,'BZD','Dólar de Belice'),
            //array(27,'CAD','Dólar canadiense'),
            //array(28,'CDF','Franco congoleño'),
            //array(29,'CHE','WIR Euro'),
            //array(30,'CHF','Franco suizo'),
            //array(31,'CLF','Unidades de fomento chilenas'),
            //array(32,'CLP','Peso chileno'),
            //array(33,'CNY','Yuan chino'),
            //array(34,'COP','Peso colombiano'),
            //array(35,'CRC','Colón costarricense'),
            //array(36,'CUC','Peso cubano convertible'),
            //array(37,'CUP','Peso cubano'),
            //array(38,'CVE','Escudo caboverdiano'),
            //array(39,'CZK','Corona checa'),
            //array(40,'DJF','Franco yibutiano'),
            //array(41,'DKK','Corona danesa'),
            //array(42,'DOP','Peso dominicano'),
            //array(43,'DZD','Dinar argelino'),
            //array(44,'EGP','Libra egipcia'),
            //array(45,'ERN','Nakfa eritreo'),
            //array(46,'ETB','Birr etíope'),
            array(47,'EUR','Euro'),
            //array(48,'FJD','Dólar fiyiano'),
            //array(49,'FKP','Libra malvinense'),
            //array(50,'GBP','Libra esterlina'),
            //array(51,'GEL','Lari georgiano'),
            //array(52,'GHS','Cedi ghanés'),
            //array(53,'GIP','Libra de Gibraltar'),
            //array(54,'GMD','Dalasi gambiano'),
            //array(55,'GNF','Franco guineano'),
            //array(56,'GTQ','Quetzal guatemalteco'),
            //array(57,'GYD','Dólar guyanés'),
            //array(58,'HKD','Dólar de Hong Kong'),
            //array(59,'HNL','Lempira hondureño'),
            //array(60,'HRK','Kuna croata'),
            //array(61,'HTG','Gourde haitiano'),
            //array(62,'HUF','Forint húngaro'),
            //array(63,'IDR','Rupia indonesia'),
            //array(64,'ILS','Nuevo shéquel israelí'),
            //array(65,'INR','Rupia india'),
            //array(66,'IQD','Dinar iraquí'),
            //array(67,'IRR','Rial iraní'),
            //array(68,'ISK','Króna islandesa'),
            //array(69,'JMD','Dólar jamaicano'),
            //array(70,'JOD','Dinar jordano'),
            //array(71,'JPY','Yen japonés'),
            //array(72,'KES','Chelín keniata'),
            //array(73,'KGS','Som kirguís'),
            //array(74,'KHR','Riel camboyano'),
            //array(75,'KMF','Franco comorense'),
            //array(76,'KPW','Won norcoreano'),
            //array(77,'KRW','Won surcoreano'),
            //array(78,'KWD','Dinar kuwaití'),
            //array(79,'KYD','Dólar caimano'),
            //array(80,'KZT','Tenge kazajo'),
            //array(81,'LAK','Kip lao'),
            //array(82,'LBP','Libra libanesa'),
            //array(83,'LKR','Rupia de Sri Lanka'),
            //array(84,'LRD','Dólar liberiano'),
            //array(85,'LSL','Loti lesotense'),
            //array(86,'LYD','Dinar libio'),
            ///array(87,'MAD','Dirham marroquí'),
            //array(88,'MDL','Leu moldavo'),
            //array(89,'MGA','Ariary malgache'),
            //array(90,'MKD','Denar macedonio'),
            //array(91,'MMK','Kyat birmano'),
            //array(92,'MNT','Tugrik mongol'),
            //array(93,'MOP','Pataca de Macao'),
            //array(94,'MRO','Uquiya mauritana'),
            //array(95,'MUR','Rupia mauricia'),
            //array(96,'MVR','Rupia de Maldivas'),
            //array(97,'MWK','Kwacha malauí'),
            //array(98,'MXN','Peso mexicano'),
            //array(99,'MXV','UDI mexicana'),
            //array(100,'MYR','Ringgit malayo'),
            //array(101,'MZN','Metical mozambiqueño'),
            //array(102,'NAD','Dólar namibio'),
            //array(103,'NGN','Naira nigeriana'),
            //array(104,'NIO','Córdoba nicaragüense'),
            //array(105,'NOK','Corona noruega'),
            //array(106,'NPR','Rupia nepalesa'),
            //array(107,'NZD','Dólar neozelandés'),
            //array(108,'OMR','Rial omaní'),
            //array(109,'PEN','Nuevo sol peruano'),
            //array(110,'PGK','Kina de Papúa Nueva Guinea'),
            //array(111,'PHP','Peso filipino'),
            //array(112,'PKR','Rupia pakistaní'),
            //array(113,'PLN','zloty polaco'),
            //array(114,'PYG','Guaraní paraguayo'),
            //array(115,'QAR','Rial qatarí'),
            //array(116,'RON','Leu rumano'),
            //array(117,'RSD','Dinar serbio'),
            //array(118,'RUB','Rublo ruso'),
            //array(119,'RWF','Franco ruandés'),
            //array(120,'SAR','Riyal saudí'),
            //array(121,'SBD','Dólar de las Islas Salomón'),
            //array(122,'SCR','Rupia de Seychelles'),
            //array(123,'SDG','Dinar sudanés'),
            //array(124,'SEK','Corona sueca'),
            //array(125,'SGD','Dólar de Singapur'),
            //array(126,'SHP','Libra de Santa Helena'),
            //array(127,'SLL','Leone de Sierra Leona'),
            //array(128,'SOS','Chelín somalí'),
            //array(129,'SRD','Dólar surinamés'),
            //array(130,'SSP','Libra sursudanesa'),
            //array(131,'STD','Dobra de Santo Tomé y Príncipe'),
            //array(132,'SYP','Libra siria'),
            //array(133,'SZL','Lilangeni suazi'),
            //array(134,'THB','Baht tailandés'),
            //array(135,'TJS','Somoni tayiko'),
            //array(136,'TMT','Manat turcomano'),
            //array(137,'TND','Dinar tunecino'),
            //array(138,'TOP','Paanga tongano'),
            //array(139,'TRY','Lira turca'),
            //array(140,'TTD','Dólar de Trinidad y Tobago'),
            //array(141,'TWD','Dólar taiwanés'),
            //array(142,'TZS','Chelín tanzano'),
            //array(143,'UAH','Grivna ucraniana'),
            //array(144,'UGX','Chelín ugandés'),
            array(145,'USD','Dólar estadounidense'),
            //array(146,'UYU','Peso uruguayo'),
            //array(147,'UZS','Som uzbeko'),
            array(148,'VEF','Bolívar'),
            //array(149,'VND','Dong vietnamita'),
            //array(150,'VUV','Vatu vanuatense'),
            //array(151,'WST','Tala samoana'),
            //array(152,'XAF','Franco CFA de África Central'),
            //array(153,'XAG','Onza de plata'),
            //array(154,'XAU','Onza de oro'),
            //array(155,'XCD','Dólar del Caribe Oriental'),
            //array(156,'XOF','Franco CFA de África Occidental'),
            //array(157,'XPD','Onza de paladio'),
            //array(158,'XPF','Franco CFP'),
            //array(159,'XPT','Onza de platino'),
            //array(160,'XTS','Reservado para pruebas'),
            //array(161,'YER','Rial yemení'),
            //array(162,'ZAR','Rand sudafricano'),
            //array(163,'ZMW','Kwacha zambiano'),
            //array(164,'ZWL','Dólar zimbabuense')
        );


        /*$currencyType = new Category();
        $currencyType->setTitle('Currency Type');
        $manager->persist($currencyType);*/

        foreach ($currencies as $key => $currency) {
            $currency1 = new CurrencyType();
            $currency1->setId($currency[0]);
            $currency1->setTitle($currency[2]);
            $currency1->setAlfa3($currency[1]);
            //$currency1->setParent($currencyType);
            $manager->persist($currency1);
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