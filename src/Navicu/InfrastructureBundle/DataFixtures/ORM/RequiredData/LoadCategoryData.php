<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Category;

/**
 * Clase LoadCateogoryData "DataFixtures".
 *
 * La clase carga los datos pruebas del sistema, donde los datos tenga un comportamiento de lista.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {

        $currencies = array(
            //array('AED','Dirham de los Emiratos Árabes Unidos'),
            //array('AFN','Afgani afgano'),
            //array('ALL','Lek albanés'),
            //array('AMD','Dram armenio'),
            array('ANG','Florín antillano neerlandés'),
            //array('AOA','Kwanza angoleño'),
            array('ARS','Peso argentino'),
            //array('AUD','Dólar australiano'),
            array('AWG','Florín arubeño'),
            //array('AZN','Manat azerbaiyano'),
            //array('BAM','Marco bosnioherzegovino'),
            //array('BBD','Dólar de Barbados'),
            //array('BDT','Taka bangladesí'),
            //array('BGN','Lev búlgaro'),
            //array('BHD','Dinar bahreiní'),
            //array('BIF','Franco burundés'),
            //array('BMD','Dólar de Bermuda'),
            //array('BND','Dólar de Brunéi'),
            array('BOB','Boliviano'),
            //array('BOV','Mvdol boliviano'),
            array('BRL','Real brasileño'),
            //array('BSD','Dólar bahameño'),
            //array('BTN','Ngultrum de Bután'),
            //array('BWP','Pula de Botsuana'),
            //array('BYR','Rublo bielorruso'),
            //array('BZD','Dólar de Belice'),
            array('CAD','Dólar canadiense'),
            //array('CDF','Franco congoleño'),
            //array('CHE','WIR Euro'),
            //array('CHF','Franco suizo'),
            //array('CLF','Unidades de fomento chilenas'),
            array('CLP','Peso chileno'),
            array('CNY','Yuan chino'),
            array('COP','Peso colombiano'),
            array('CRC','Colón costarricense'),
            //array('CUC','Peso cubano convertible'),
            array('CUP','Peso cubano'),
            //array('CVE','Escudo caboverdiano'),
            //array('CZK','Corona checa'),
            //array('DJF','Franco yibutiano'),
            //array('DKK','Corona danesa'),
            array('DOP','Peso dominicano'),
            //array('DZD','Dinar argelino'),
            //array('EGP','Libra egipcia'),
            //array('ERN','Nakfa eritreo'),
            //array('ETB','Birr etíope'),
            array('EUR','Euro'),
            //array('FJD','Dólar fiyiano'),
            //array('FKP','Libra malvinense'),
            //array('GBP','Libra esterlina'),
            //array('GEL','Lari georgiano'),
            //array('GHS','Cedi ghanés'),
            //array('GIP','Libra de Gibraltar'),
            //array('GMD','Dalasi gambiano'),
            //array('GNF','Franco guineano'),
            array('GTQ','Quetzal guatemalteco'),
            array('GYD','Dólar guyanés'),
            array('HKD','Dólar de Hong Kong'),
            array('HNL','Lempira hondureño'),
            //array('HRK','Kuna croata'),
            array('HTG','Gourde haitiano'),
            //array('HUF','Forint húngaro'),
            //array('IDR','Rupia indonesia'),
            //array('ILS','Nuevo shéquel israelí'),
            array('INR','Rupia india'),
            //array('IQD','Dinar iraquí'),
            //array('IRR','Rial iraní'),
            //array('ISK','Króna islandesa'),
            array('JMD','Dólar jamaicano'),
            //array('JOD','Dinar jordano'),
            array('JPY','Yen japonés'),
            //array('KES','Chelín keniata'),
            //array('KGS','Som kirguís'),
            //array('KHR','Riel camboyano'),
            //array('KMF','Franco comorense'),
            //array('KPW','Won norcoreano'),
            //array('KRW','Won surcoreano'),
            //array('KWD','Dinar kuwaití'),
            //array('KYD','Dólar caimano'),
            //array('KZT','Tenge kazajo'),
            //array('LAK','Kip lao'),
            //array('LBP','Libra libanesa'),
            //array('LKR','Rupia de Sri Lanka'),
            //array('LRD','Dólar liberiano'),
            //array('LSL','Loti lesotense'),
            //array('LYD','Dinar libio'),
            ///array('MAD','Dirham marroquí'),
            //array('MDL','Leu moldavo'),
            //array('MGA','Ariary malgache'),
            //array('MKD','Denar macedonio'),
            //array('MMK','Kyat birmano'),
            //array('MNT','Tugrik mongol'),
            //array('MOP','Pataca de Macao'),
            //array('MRO','Uquiya mauritana'),
            //array('MUR','Rupia mauricia'),
            //array('MVR','Rupia de Maldivas'),
            //array('MWK','Kwacha malauí'),
            array('MXN','Peso mexicano'),
            //array('MXV','UDI mexicana'),
            //array('MYR','Ringgit malayo'),
            //array('MZN','Metical mozambiqueño'),
            //array('NAD','Dólar namibio'),
            //array('NGN','Naira nigeriana'),
            //array('NIO','Córdoba nicaragüense'),
            //array('NOK','Corona noruega'),
            //array('NPR','Rupia nepalesa'),
            //array('NZD','Dólar neozelandés'),
            //array('OMR','Rial omaní'),
            array('PEN','Nuevo sol peruano'),
            //array('PGK','Kina de Papúa Nueva Guinea'),
            //array('PHP','Peso filipino'),
            //array('PKR','Rupia pakistaní'),
            //array('PLN','zloty polaco'),
            array('PYG','Guaraní paraguayo'),
            //array('QAR','Rial qatarí'),
            //array('RON','Leu rumano'),
            //array('RSD','Dinar serbio'),
            array('RUB','Rublo ruso'),
            //array('RWF','Franco ruandés'),
            //array('SAR','Riyal saudí'),
            //array('SBD','Dólar de las Islas Salomón'),
            //array('SCR','Rupia de Seychelles'),
            //array('SDG','Dinar sudanés'),
            //array('SEK','Corona sueca'),
            //array('SGD','Dólar de Singapur'),
            //array('SHP','Libra de Santa Helena'),
            //array('SLL','Leone de Sierra Leona'),
            //array('SOS','Chelín somalí'),
            //array('SRD','Dólar surinamés'),
            //array('SSP','Libra sursudanesa'),
            //array('STD','Dobra de Santo Tomé y Príncipe'),
            //array('SYP','Libra siria'),
            //array('SZL','Lilangeni suazi'),
            array('THB','Baht tailandés'),
            //array('TJS','Somoni tayiko'),
            //array('TMT','Manat turcomano'),
            //array('TND','Dinar tunecino'),
            //array('TOP','Paanga tongano'),
            //array('TRY','Lira turca'),
            array('TTD','Dólar de Trinidad y Tobago'),
            array('TWD','Dólar taiwanés'),
            //array('TZS','Chelín tanzano'),
            //array('UAH','Grivna ucraniana'),
            array('UGX','Chelín ugandés'),
            array('USD','Dólar estadounidense'),
            array('UYU','Peso uruguayo'),
            //array('UZS','Som uzbeko'),
            array('VEF','Bolívar'),
            //array('VND','Dong vietnamita'),
            //array('VUV','Vatu vanuatense'),
            //array('WST','Tala samoana'),
            //array('XAF','Franco CFA de África Central'),
            //array('XAG','Onza de plata'),
            //array('XAU','Onza de oro'),
            //array('XCD','Dólar del Caribe Oriental'),
            //array('XOF','Franco CFA de África Occidental'),
            //array('XPD','Onza de paladio'),
            //array('XPF','Franco CFP'),
            //array('XPT','Onza de platino'),
            //array('XTS','Reservado para pruebas'),
            //array('YER','Rial yemení'),
            //array('ZAR','Rand sudafricano'),
            //array('ZMW','Kwacha zambiano'),
            //array('ZWL','Dólar zimbabuense')
        );
        //Carga de Establecimientos
        $propertyType = new Category();
        $propertyType->setTitle('Property Type');

        $hotel = new Category();
        $hotel->setTitle('Hotel');
        $hotel->setParent($propertyType);

        $hotelDescription = new Category();
        $hotelDescription->setTitle('MyDescription Hotel');
        $hotelDescription->setParent($hotel);

        $hostel = new Category();
        $hostel->setTitle('Hostel');
        $hostel->setParent($propertyType);

        $hostelDescription = new Category();
        $hostelDescription->setTitle('MyDescription Hostel');
        $hostelDescription->setParent($hostel);

        $manager->persist($propertyType);
        $manager->persist($hotel);
        $manager->persist($hotelDescription);
        $manager->persist($hostel);
        $manager->persist($hostelDescription);
        $manager->flush();
     
        //Carga de Tipo de Habitaciones 
        $roomType = new Category();
        $roomType->setTitle('Room Type');

        $basicApartament = new Category();
        $basicApartament->setTitle('Basic Apartament');
        $basicApartament->setParent($roomType);

        $basicADescription = new Category();
        $basicADescription->setTitle('MyDescription Basic Apartament');
        $basicADescription->setParent($basicApartament);

        $deluxeApartament = new Category();
        $deluxeApartament->setTitle('Deluxe Apartament');
        $deluxeApartament->setParent($roomType);

        $deluxeADescription = new Category();
        $deluxeADescription->setTitle('MyDescription Delexu Apartament');
        $deluxeADescription->setParent($deluxeApartament);

        $exclusiveApartament = new Category();
        $exclusiveApartament->setTitle('Exclusive Apartament');
        $exclusiveApartament->setParent($roomType);

        $exclusiveADescription = new Category();
        $exclusiveADescription->setTitle('MyDescription Exclusive Apartament');
        $exclusiveADescription->setParent($deluxeApartament);

        $manager->persist($roomType);
        $manager->persist($basicApartament);
        $manager->persist($basicADescription);
        $manager->persist($deluxeApartament);
        $manager->persist($deluxeADescription);
        $manager->persist($exclusiveApartament);
        $manager->persist($exclusiveADescription);
        $manager->flush();
     
        //Carga de Tipo de Services
        $serviceType = new Category();
        $serviceType->setTitle('Service Type');

        $roomOnly = new Category();
        $roomOnly->setTitle('Room Only');
        $roomOnly->setParent($serviceType);

        $roomODescription = new Category();
        $roomODescription->setTitle('MyDescription Room Only');
        $roomODescription->setParent($roomOnly);

        $breakFastIncluded = new Category();
        $breakFastIncluded->setTitle('BreakFast Included');
        $breakFastIncluded->setParent($serviceType);

        $breakFastIDescription = new Category();
        $breakFastIDescription->setTitle('MyDescription BreakFast Included');
        $breakFastIDescription->setParent($breakFastIncluded);

        $nonRedundable = new Category();
        $nonRedundable->setTitle('Non Redundable');
        $nonRedundable->setParent($serviceType);

        $nonRDescription = new Category();
        $nonRDescription->setTitle('MyDescription Non Redundable');
        $nonRDescription->setParent($nonRedundable);

        $engenciaPeferred = new Category();
        $engenciaPeferred->setTitle('Egencia Preferred');
        $engenciaPeferred->setParent($serviceType);

        $engenciaPDescription = new Category();
        $engenciaPDescription->setTitle('MyDescription Egencia Preferred');
        $engenciaPDescription->setParent($engenciaPeferred);


        $manager->persist($serviceType);
        $manager->persist($roomOnly);
        $manager->persist($roomODescription);
        $manager->persist($breakFastIncluded);
        $manager->persist($breakFastIDescription);
        $manager->persist($nonRedundable);
        $manager->persist($nonRDescription);  
        $manager->persist($engenciaPeferred);
        $manager->persist($engenciaPDescription);
        $manager->flush();

        //Carga Tipo (Perfil) de Propietario

        $ownerType = new Category();
        $ownerType->setTitle('Owner Type');

        $superOwner = new Category();
        $superOwner->setTitle('Super Owner');
        $superOwner->setParent($ownerType);

        $assistantOwner = new Category();
        $assistantOwner->setTitle('Assistant Owner');
        $assistantOwner->setParent($ownerType);

        $manager->persist($ownerType);
        $manager->persist($superOwner);
        $manager->persist($assistantOwner);
        $manager->flush();

        //Carga de servicios extras del establecimiento

        $propertyServices = new Category();
        $propertyServices->setTitle('Property Services');

        $propertyService1 = new Category();
        $propertyService1->setTitle('Wi-Fi');
        $propertyService1->setParent($propertyServices);

        $propertyService2 = new Category();
        $propertyService2->setTitle('SPA');
        $propertyService2->setParent($propertyServices);

        $propertyService3 = new Category();
        $propertyService3->setTitle('Gym');
        $propertyService3->setParent($propertyServices);

        $propertyService4 = new Category();
        $propertyService4->setTitle('Rent a car');
        $propertyService4->setParent($propertyServices);

        $manager->persist($propertyServices);
        $manager->persist($propertyService1);
        $manager->persist($propertyService2);
        $manager->persist($propertyService3);
        $manager->persist($propertyService4);
        $manager->flush();


        //Carga de Cadenas hoteleras

        $hotelChainType = new Category();
        $hotelChainType->setTitle('Cadena Hotelera Type');

        $hotelChain1 = new Category();
        $hotelChain1->setTitle('Hilton Caracas');
        $hotelChain1->setParent($hotelChainType);

        $hotelChain2 = new Category();
        $hotelChain2->setTitle('Hilton Hesperia');
        $hotelChain2->setParent($hotelChainType);

        $manager->persist($hotelChainType);
        $manager->persist($hotelChain1);
        $manager->persist($hotelChain2);        
        $manager->flush();

        //Carga de Codigo postales 

        $postalCodeType = new Category();
        $postalCodeType->setTitle('Postal Code Type');

        $postalCode1 = new Category();
        $postalCode1->setTitle('2051');
        $postalCode1->setParent($postalCodeType);

        $postalCode2 = new Category();
        $postalCode2->setTitle('2050');
        $postalCode2->setParent($postalCodeType);

       $manager->persist($postalCodeType);
        $manager->persist($postalCode1);
        $manager->persist($postalCode2);
        $manager->flush();

        //Carga de Zona Horaria

        $timeZoneType = new Category();
        $timeZoneType->setTitle('Time zone Type');
        
        $timeZone1 = new Category();
        $timeZone1->setTitle('UTC-4:30');
        $timeZone1->setParent($timeZoneType);       

        $timeZone2 = new Category();
        $timeZone2->setTitle('GMT');
        $timeZone2->setParent($timeZoneType);         

        $manager->persist($timeZoneType);
        $manager->persist($timeZone1);
        $manager->persist($timeZone2);
        $manager->flush();

        //Carga de Aeropuertos 

        $airportCodeType = new Category();
        $airportCodeType->setTitle('Airport Code Type');

        $airportCode1 = new Category();
        $airportCode1->setTitle('MAD');
        $airportCode1->setParent($airportCodeType);

        $airportCode2 = new Category();
        $airportCode2->setTitle('BAR');
        $airportCode2->setParent($airportCodeType);

        $manager->persist($airportCodeType);
        $manager->persist($airportCode1);
        $manager->persist($airportCode2);
        $manager->flush();

        //Carga de las monedas del establecimiento

        $currencyType = new Category();
        $currencyType->setTitle('Currency Type');
        $manager->persist($currencyType);

        foreach ($currencies as $key => $currency) {
            $currency1 = new Category();
            $currency1->setTitle($currency[1]);
            $currency1->setParent($currencyType);
            $manager->persist($currency1);
        }
        $manager->flush();

        //Carga de las caracteristicas de la habitación

        $featureRoomType = new Category();
        $featureRoomType->setTitle('Feature Room Type');

        $featureRoom1 = new Category();
        $featureRoom1->setTitle('Kitchen');
        $featureRoom1->setParent($featureRoomType);

        $featureRoom2 = new Category();
        $featureRoom2->setTitle('TV');
        $featureRoom2->setParent($featureRoomType);

        $manager->persist($featureRoomType);
        $manager->persist($featureRoom1);
        $manager->persist($featureRoom2);
        $manager->flush();

        //Carga Tipos de politicas de cancelación

        $cancellationPolicyType = new Category();
        $cancellationPolicyType->setTitle('Cancellation Pilicy Type');

        $noRefundable = new Category();
        $noRefundable->setTitle('No Refundable');
        $noRefundable->setParent($cancellationPolicyType);

        $policy1 = new Category();
        $policy1->setTitle('Politicy1');
        $policy1->setParent($cancellationPolicyType);
        
        $policy2 = new Category();
        $policy2->setTitle('Politicy2');
        $policy2->setParent($cancellationPolicyType);

        $manager->persist($cancellationPolicyType);
        $manager->persist($noRefundable);
        $manager->persist($policy1);
        $manager->persist($policy2);
        $manager->flush();

        //Carga de galería del establecimiento
        $propertyGallery = new Category();
        $propertyGallery->setTitle('Property Gallery Type');

        $propertyBasicGallery = new Category();
        $propertyBasicGallery->setTitle('Property Basic Gallery');
        $propertyBasicGallery->setParent($propertyGallery);

        $propertyCommonZonesGallery = new Category();
        $propertyCommonZonesGallery->setTitle('Property Common Zone Gallery');
        $propertyCommonZonesGallery->setParent($propertyGallery);

        $manager->persist($propertyGallery);
        $manager->persist($propertyBasicGallery);
        $manager->persist($propertyCommonZonesGallery);
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