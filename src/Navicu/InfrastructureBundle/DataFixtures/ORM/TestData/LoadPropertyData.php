<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Domain\Model\ValueObject\Phone;
/**
 * Clase LoadPropertyData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de los establecimientos.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadPropertyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        //Se consultan el conjuntos de datos informativos necesarios de la propiedad

        //$images = $manager->getRepository("NavicuInfrastructureBundle:Document");

        //Se carga solamente 2 establecimientos
        for ($i = 1; $i <= 3; $i++) { 

            $current_property = new Property();

            $location = $manager->getRepository('NavicuDomain:Location')->findOneBy(array('title'=>'Valencia'));
            $icoa = $manager->getRepository('NavicuDomain:Category')->findOneBy(array('title'=>'MAD'));
            $zh = $manager->getRepository('NavicuDomain:Category')->findOneBy(array('title'=>'GMT'));
            $currency = $manager->getRepository('NavicuDomain:CurrencyType')->findOneBy(array('title'=>'Bolívar'));
            $spa = $manager->getRepository('NavicuDomain:Language')->find('spa');
            $eng =  $manager->getRepository('NavicuDomain:Language')->find('eng');
            $ita =  $manager->getRepository('NavicuDomain:Language')->find('ita');
            $epo =  $manager->getRepository('NavicuDomain:Language')->find('por');
            $hotel =  $manager->getRepository('NavicuDomain:Accommodation')->findOneByTitle('Hoteles');
    
            $current_property->setName('Property'.$i);
            $current_property->setActive(true);
            $current_property->setOpeningYear(2000);
            $current_property->setPhones(new Phone('+58-555-5555555'));
            $current_property->setFax(new Phone('582225554488'));
            $current_property->setEmails(new EmailAddress('My_Name_Property'.$i.'@example.com'));
            $current_property->setDescription('My_Name_Property'.$i.', these are our descriptions');
            $randRating = 0.1 + mt_rand() / mt_getrandmax() * (10-0.1);
            $current_property->setRating(round($randRating,2));
            $current_property->setAddress('Street-Central-xxxxxxx');
            $current_property->setStar(5);
            $current_property->setUrlWeb(new Url('http://www.Property'.$i.'com.ve'));
            $current_property->setAmountRoom(100);
            $current_property->setCheckIn(new \DateTime("08:00:00"));
            $current_property->setCheckOut(new \DateTime("22:00:00"));
            $current_property->setDiscountRate(0.2);
            $current_property->setNumberFloor(12);
            $current_property->setRenewalYear(2012);
            $current_property->setPublicAreasRenewalYear(2012);
            $current_property->setCheckInAge(18);
            $current_property->setChildMaxAge(12);
            $current_property->setCoordinates(new Coordinate(10*$i,10*$i));
            $current_property->setLocation($location);
            $current_property->setCurrency($currency);
            $current_property->addLanguage($spa);
            $current_property->addLanguage($eng);
            $current_property->addLanguage($epo);
            $current_property->addLanguage($ita);
            $current_property->setTax(true);
            $current_property->setTaxRate(15);
            $current_property->setPets(true);
            $current_property->setPetsAdditionalCost(true);
            $current_property->setChild(true);
            $current_property->setChildAdditionalCost(False);
            $current_property->setChildMaxAge(12);
            $current_property->setCribs(true);
            $current_property->setCribsAdditionalCost(true);
            $current_property->setCribsMax(3);
            $current_property->setBeds(true);
            $current_property->setBedsAdditionalCost(true);
            $current_property->setCash(true);
            $current_property->setMaxCash(20000);
            $current_property->setCreditCard(true);
            $current_property->setCreditCardAmex(false);
            $current_property->setCreditCardMc(true);
            $current_property->setCreditCardVisa(true);
            $current_property->generateSlug();
            $current_property->generatePublicId();

            $manager->persist($current_property);
            $manager->flush();        
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 20;
    }
}