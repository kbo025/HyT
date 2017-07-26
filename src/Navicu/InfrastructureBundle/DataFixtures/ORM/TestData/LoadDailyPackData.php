<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\DailyPack;

/**
 * Clase LoadDailyPackData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los datos de los servicios de las habitaciones por día.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadDailyPackData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
       /* //Se obtiene todos los servicios (pack)
        $packages = $manager->getRepository("NavicuDomain:Pack")->findAll();
        $servicePackLinkage = $manager->getRepository("NavicuDomain:PackLinkage");
        $serviceRoomPackLinkage = $manager->getRepository("NavicuDomain:RoomPackLinkage");

        foreach ($packages as $currentPack) {
            
            $currentDate = date_create();
            
            //Se cargan los datos en base a 30 días
            for ($i = 0; $i < 30 ; $i++) {
                //$packLinkage = $servicePackLinkage->findByChildIdRange($currentPack->getId(), $currentDate, $currentDate);
                //$roomPackLinkage = $serviceRoomPackLinkage->findByChildIdRange($currentPack->getId(), $currentDate, $currentDate);

                $dailyPack = new DailyPack();
                $dailyPack->setPack($currentPack);
                $dailyPack->setDate($currentDate);
                $dailyPack->setIsCompleted(1);
                $dailyPack->setSpecificAvailability(rand(7,20));
                $dailyPack->setMinNight(rand(1,2));
                $dailyPack->setMaxNight(rand(3,5));
                $dailyPack->setCloseOut(rand(0,1));
                $dailyPack->setClosedToArrival(rand(0,1));
                $dailyPack->setClosedToDeparture(rand(0,1));

                $dailyPack->setBaseRate(rand(500,1500));

                $cancellationPolicy = $currentPack->getRoom()->getProperty()->getBasePolicy()->getCancellationPolicy();
                $data["variationAmount"] = $cancellationPolicy->getVariationAmount();
                $data["variationType"] = $cancellationPolicy->getVariationType();
                if ($data["variationType"] == 1) {
                    $sellRate = $dailyPack->getBaseRate() + ($dailyPack->getBaseRate() * $data["variationAmount"]);
                } else {
                    $sellRate = $dailyPack->getBaseRate() + $data["variationAmount"];
                }
                $dailyPack->setSellRate($sellRate);

                $discountRate = $currentPack->getRoom()->getProperty()->getDiscountRate();
                $netRate = $dailyPack->getSellRate() - ($dailyPack->getSellRate() * $discountRate);
                $dailyPack->setNetRate($netRate);

                
                $manager->persist($dailyPack);
                $manager->flush();

                date_modify($currentDate, "+1 day");
                $linkage = false;
            }
        }*/
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 130;
    }
}