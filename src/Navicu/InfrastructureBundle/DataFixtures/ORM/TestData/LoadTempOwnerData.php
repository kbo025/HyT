<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\ContactPerson;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
/**
 * Clase LoadTempOwnerData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de los establecimientos.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 */

class LoadTempOwnerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {   
        $i = 20;

        $user0 = new User();
        $temp0 = new TempOwner('tempowner2', new EmailAddress('tempowner2@example.com'),new Password('1234$HjFd'));


        $user0->setUsername('tempowner2');
        $user0->setEmail('tempowner2@example.com');
        $user0->setPlainPassword('123456');
        $user0->setEnabled(true);

        $user0->addRole(3);
        $temp0->setUserId($user0);
        $temp0->setSlug('tempowner2');

        $manager->persist($user0);
        $manager->persist($temp0);

        $current_property = new Property();

        /*$location = $manager->getRepository('NavicuDomain:Location')->findOneBy(array('title'=>'Valencia'));
        $currency = $manager->getRepository('NavicuDomain:Category')->findOneBy(array('title'=>'Bolívar'));

        $span = $manager->getRepository('NavicuDomain:Language')->find('spa');
        $eng =  $manager->getRepository('NavicuDomain:Language')->find('eng');
        $ita =  $manager->getRepository('NavicuDomain:Language')->find('ita');
        $epo =  $manager->getRepository('NavicuDomain:Language')->find('por');
        $hotel = $manager->getRepository('NavicuDomain:Accommodation')->findOneByTitle('Hoteles');
        $room1 = $manager->getRepository('NavicuDomain:Room')->find(1);
        $room2 = $manager->getRepository('NavicuDomain:Room')->find(2);
        $room3 = $manager->getRepository('NavicuDomain:Room')->find(3);
        $food = $manager->getRepository('NavicuDomain:FoodType')->find(1);

        $ServiceType = $manager->getRepository("NavicuDomain:ServiceType");


        //tipos booleanos
        $wifi = $ServiceType->findOneByTitle('Wifi');
        $remo = $ServiceType->findOneByTitle('Remo');

        //tipo costos
        //$gym = $ServiceType->findOneByTitle('Gym');
        $spa = $ServiceType->findOneByTitle('Spa');
        
        //tipo restaurant
        $restaurantes = $ServiceType->findOneByTitle('Restaurante');

        //tipo bar
        $bares = $ServiceType->findOneByTitle('Bar');

        //tipo salon
        $salones = $ServiceType->findOneByTitle('Salones');

        //tipo horario
        $recepcion = $ServiceType->findOneByTitle('Horario de recepcion');

        //tipo cantidad
        $piscina = $ServiceType->findOneByTitle('Piscinas');


        $property_service1 = new PropertyService();
        $property_service1->setType($wifi);

        $property_service4 = new PropertyService();
        $property_service4->setType($remo);

        //$property_service2 = new PropertyService();
        //$property_service2->setType($gym);
        //$property_service2->setFree(false);

        $property_service3 = new PropertyService();
        $property_service3->setType($spa);
        $property_service3->setFree(false);

        $property_recepcion = new PropertyService();
        $property_recepcion->setType($recepcion);
        $property_recepcion->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));

        $property_piscina = new PropertyService();
        $property_piscina->setType($piscina);
        $property_piscina->setQuantity(3);

        $property_salones = new PropertyService();
        $property_salones->setType($salones);
        for($i=1;$i<6;$i++)
        {
            $salon = new Salon();
            $salon->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $salon->setSize(40.75*rand(1,10));
            $salon->setType(rand(1,2));
            $salon->setQuantity(rand(5,20));
            $salon->setNaturalLight( (rand(0,100) % 2 == 0) ? true : false );
            $property_salones->addSalon($salon);
        }

        $property_restaurantes = new PropertyService();
        $property_restaurantes->setType($restaurantes);
        for($i=1;$i<6;$i++)
        {
            $restaurant = new Restaurant();
            $restaurant->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setBreakfastTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setLunchTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setDinnerTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setName('rest'.$i);
            $restaurant->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $restaurant->setDietaryMenu( (rand(0,100) % 2 == 0) ? true : false );
            $restaurant->setBuffetCarta(1);
            $restaurant->setType($food);
            $restaurant->setDescription('restaurant'.$i);
            $property_restaurantes->addRestaurant($restaurant);
        }

        $property_bares = new PropertyService();
        $property_bares->setType($bares);
        for ($i=1;$i<6;$i++)
        {
            $bar = new Bar();
            $bar->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $bar->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $bar->setMinAge(rand(18,30));
            $bar->setFood( (rand(0,100) % 2 == 0) ? true : false );
            $bar->setName('Babylon Clud'.$i);
            $bar->setDescription('Babylon Clud'.$i);
            $property_bares->addBar($bar);
        }
        

        $current_property->setName('Property'.$i);
        $current_property->setFiscalName('Property'.$i);
        $current_property->setOpeningYear(2000);
        $current_property->setAddress('Address'.$i);
        $current_property->setSlug('Property'.$i);
        $current_property->setPhones(new Phone('+58-555-5555555'));
        $current_property->setFax(new Phone('+582225554488'));
        $current_property->setEmails(new EmailAddress('My_Name_Property'.$i.'@example.com'));
        $current_property->setDescription('My_Name_Property'.$i.', these are our descriptions');
        $randRating = 0.1 + mt_rand() / mt_getrandmax() * (10-0.1);
        $current_property->setRating(round($randRating,2));
        $current_property->setTaxId('Rif-xxxxxx');
        $current_property->setAddress('Street-Central-xxxxxxx');
        $current_property->setStar(5);
        $current_property->setUrlWeb(new Url('https://www.Property'.$i.'com.ve'));
        $current_property->setAmountRoom(100);
        $current_property->setCheckIn(new \DateTime("08:00:00"));
        $current_property->setCheckOut(new \DateTime("22:00:00"));
        $current_property->setRating(10);
        $current_property->setDiscountRate(0.2);
        $current_property->setTax(15);
        $current_property->setTaxRate(true);
        $current_property->setNumberFloor(12);
        $current_property->setAdultAge(21);
        $current_property->setRenewalYear(2012);
        $current_property->setPublicAreasRenewalYear(2012);
        $current_property->setCheckInAge(18);
        $current_property->setCheckOutAge(18);
        $current_property->setPets(true);
        $current_property->setPetsAdditionalCost(true);
        $current_property->setChild(true);
        $current_property->setChildAdditionalCost(False);
        $current_property->setChildMaxAge(12);
        $current_property->setBaby(true);
        $current_property->setBabyAdditionalCost(False);
        $current_property->setBabyMaxAge(2);
        $current_property->setCribs(true);
        $current_property->setCribsAdditionalCost(true);
        $current_property->setCribsMax(3);
        $current_property->setBeds(true);
        $current_property->setAdditionalInfo('Aqui va la informacion adicional');
        $current_property->setBedsAdditionalCost(true);
        $current_property->setCash(true);
        $current_property->setMaxCash(20000);
        $current_property->setCreditCard(true);
        $current_property->setCreditCardAmex(false);
        $current_property->setCreditCardMc(true);
        $current_property->setCityTax(15);
        $current_property->setCityTaxCurrency($currency);
        $current_property->setCityTaxType(2);
        $current_property->setCityTaxMaxNights(7);
        $current_property->setCreditCardVisa(true);
        $current_property->setCoordinates(new Coordinate($i,$i));
        $current_property->setLocation($location);
        $current_property->setPostalCode('2010');
        $current_property->setCurrency($currency);
        $current_property->setOtherCurrency($currency);
        $current_property->setAirportCode($icoa);
        $current_property->setZoneUtc($zh);
        $current_property->addLanguage( $span );
        $current_property->addLanguage( $eng );
        $current_property->addLanguage( $epo );
        $current_property->addLanguage( $ita );
        $current_property->setAccommodation($hotel);
        $current_property->setHotelChainName('NH');
        $contact = new ContactPerson();
        $contact->setName('Rosa Meltroso');
        $contact->setCharge('contabilidad');
        $contact->setType(0);
        $contact->setPhone(new Phone('04125555555'));
        $contact->setFax(new Phone('04125555555'));
        $contact->setEmail(new EmailAddress('vacitodeagua@hotmail.com'));
        $current_property->addContact($contact);
        $contact = new ContactPerson();
        $contact->setName('Lola Mento');
        $contact->setCharge('Gerente de Reservas');
        $contact->setType(1);
        $contact->setPhone(new Phone('04124444444'));
        $contact->setFax(new Phone('04124444444'));
        $contact->setEmail(new EmailAddress('labicicleta@hotmail.com'));
        $current_property->addContact($contact);
        $contact = new ContactPerson();
        $contact->setName('Soila Mesa');
        $contact->setCharge('Gerente General');
        $contact->setType(2);
        $contact->setPhone(new Phone('04123333333'));
        $contact->setFax(new Phone('04123333333'));
        $contact->setEmail(new EmailAddress('tortadecumpleanio@hotmail.com'));
        $current_property->addContact($contact);*/

        $user1 = new User();
        $temp1 = new TempOwner('tempowner1', new EmailAddress('tempowner1@example.com'),new Password('1234$HjFd'));
        //$temp1->setSlug('tempowner1');
        //$temp1->setLastsec(0);
        //$temp1->setProgress(0,1);
        $user1->setUsername('tempowner1');
        $user1->setEmail('tempowner1@example.com');
        $user1->setPlainPassword('123456');
        $user1->setEnabled(true);
        $user1->addRole(3);
        //$temp1->setPropertyForm($current_property);
        $temp1->setUserId($user1);


        $manager->persist($user1);
        $manager->persist($temp1);

        $user3 = new User();
        $temp3 = new TempOwner('tempowner3', new EmailAddress('tempowner3@example.com'),new Password('1234$HjFd'));
        //$temp3->setLastsec(2);
        //$temp3->setProgress(0,1);
        //$temp3->setProgress(1,1);
        $user3->setUsername('tempowner3');
        $user3->setEmail('tempowner3@example.com');
        $user3->setPlainPassword('123456');
        $user3->setEnabled(true);

        $user3->addRole(3);
        $temp3->setUserId($user3);
        //$temp3->setSlug('tempowner3');

        //$temp3->setPropertyForm($current_property);
        //$temp3->getServices()->add($property_service1);
        //$temp3->getServices()->add($property_service2);
        /*$temp3->getServices()->add($property_service3);
        $temp3->getServices()->add($property_service4);
        $temp3->getServices()->add($property_recepcion);
        $temp3->getServices()->add($property_piscina);
        $temp3->getServices()->add($property_restaurantes);
        $temp3->getServices()->add($property_salones);
        $temp3->getServices()->add($property_bares);*/

        $manager->persist($user3);
        $manager->persist($temp3);

        $user4 = new User();
        $temp4 = new TempOwner('tempowner4', new EmailAddress('tempowner4@example.com'),new Password('1234$HjFd'));
        /*$temp4->setLastsec(3);
        $temp4->setProgress(0,1);
        $temp4->setProgress(1,1);
        $temp4->setProgress(2,1);*/
        $user4->setUsername('tempowner4');
        $user4->setEmail('tempowner4@example.com');
        $user4->setPlainPassword('123456');
        $user4->setEnabled(true);

        $user4->addRole(3);
        $temp4->setUserId($user4);
        //$temp4->setSlug('tempowner4');

        /*$temp4->setPropertyForm($current_property);
        $temp4->getServices()->add($property_service1);*/
        //$temp4->getServices()->add($property_service2);
        /*$temp4->getServices()->add($property_service3);
        $temp4->getServices()->add($property_service4);
        $temp4->getServices()->add($property_recepcion);
        $temp4->getServices()->add($property_piscina);
        $temp4->getServices()->add($property_restaurantes);
        $temp4->getServices()->add($property_salones);
        $temp4->getServices()->add($property_bares);
        $temp4->addRoom($room1);
        $temp4->addRoom($room2);
        $temp4->addRoom($room3);*/

        $manager->persist($user4);
        $manager->persist($temp4);

        $user5 = new User();
        $temp5 = new TempOwner('tempowner5', new EmailAddress('tempowner5@example.com'),new Password('1234$HjFd'));
        /*$temp5->setLastsec(4);
        $temp5->setProgress(0,1);
        $temp5->setProgress(1,1);
        $temp5->setProgress(2,1);
        $temp5->setProgress(3,0);*/
        $user5->setUsername('tempowner5');
        $user5->setEmail('tempowner5@example.com');
        $user5->setPlainPassword('123456');
        $user5->setEnabled(true);

        $user5->addRole(3);
        $temp5->setUserId($user5);
        //$temp5->setSlug('tempowner5');

        $temp5->setPropertyForm($current_property);
        /*$temp5->getServices()->add($property_service1);
        //$temp5->getServices()->add($property_service2);
        $temp5->getServices()->add($property_service3);
        $temp5->getServices()->add($property_service4);
        $temp5->getServices()->add($property_recepcion);
        $temp5->getServices()->add($property_piscina);
        $temp5->getServices()->add($property_restaurantes);
        $temp5->getServices()->add($property_salones);
        $temp5->getServices()->add($property_bares);
        $temp5->addRoom($room1);
        $temp5->addRoom($room2);
        $temp5->addRoom($room3);*/



        $manager->persist($user5);
        $manager->persist($temp5);

        $user6 = new User();
        $temp6 = new TempOwner('tempowner6', new EmailAddress('tempowner6@example.com'),new Password('1234$HjFd'));
        /*$temp6->setLastsec(5);
        $temp6->setProgress(0,1);
        $temp6->setProgress(1,1);
        $temp6->setProgress(2,1);
        $temp6->setProgress(3,1);
        $temp6->setProgress(4,1);*/
        $user6->setUsername('tempowner6');
        $user6->setEmail('tempowner6@example.com');
        $user6->setPlainPassword('123456');
        $user6->setEnabled(true);

        $user6->addRole(3);
        $temp6->setUserId($user6);
        //$temp6->setSlug('tempowner6');

        //$temp6->setPropertyForm($current_property);
        //$temp6->getServices()->add($property_service1);
        //$temp6->getServices()->add($property_service2);
        /*$temp6->getServices()->add($property_service3);
        $temp6->getServices()->add($property_service4);
        $temp6->getServices()->add($property_recepcion);
        $temp6->getServices()->add($property_piscina);
        $temp6->getServices()->add($property_restaurantes);
        $temp6->getServices()->add($property_salones);
        $temp6->getServices()->add($property_bares);
        $temp6->addRoom($room1);
        $temp6->addRoom($room2);
        $temp6->addRoom($room3);*/


        $manager->persist($user6);
        $manager->persist($temp6);

        $user7 = new User();
        $temp7 = new TempOwner('tempowner7', new EmailAddress('tempowner7@example.com'),new Password('1234$HjFd'));
        /*$temp7->setLastsec(6);
        $temp7->setProgress(5,1);
        $temp7->setProgress(0,1);
        $temp7->setProgress(1,1);
        $temp7->setProgress(2,1);
        $temp7->setProgress(3,1);
        $temp7->setProgress(4,1);*/
        $user7->setUsername('tempowner7');
        $user7->setEmail('tempowner7@example.com');
        $user7->setPlainPassword('123456');
        $user7->setEnabled(true);

        $user7->addRole(3);
        $temp7->setUserId($user7);
        //$temp7->setSlug('tempowner7');

        $user7->addRole('ROLE_TEMPOWNER');
        $temp7->setUserId($user7);
        //$temp7->setSlug(7);

        $temp7->setPropertyForm($current_property);
        /*$temp7->getServices()->add($property_service1);
        //$temp7->getServices()->add($property_service2);
        $temp7->getServices()->add($property_service3);
        $temp7->getServices()->add($property_service4);
        $temp7->getServices()->add($property_recepcion);
        $temp7->getServices()->add($property_piscina);
        $temp7->getServices()->add($property_restaurantes);
        $temp7->getServices()->add($property_salones);
        $temp7->getServices()->add($property_bares);
        $temp7->addRoom($room1);
        $temp7->addRoom($room2);
        $temp7->addRoom($room3);*/

        $manager->persist($user7);
        $manager->persist($temp7);

        $manager->flush();
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */
    public function getOrder()
    {
        return 65;
    }
}
