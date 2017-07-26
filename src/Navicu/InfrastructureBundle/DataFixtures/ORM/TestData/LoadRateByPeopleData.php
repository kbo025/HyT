<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\RateByPeople;

/**
 * Clase LoadRateByPeopleData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los servicios adicionales que ofrece los establecimientos.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadRateByPeopleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        //Se obtiene todas las habitaciones (rooms)
        $rooms = $manager->getRepository("NavicuDomain:Room")->findAll();

        foreach ($rooms as $currentRoom) {
                        
            $minPeople = $currentRoom->getMinPeople();
            $maxPeople = $currentRoom->getMaxPeople();
            $variationType = $currentRoom->getVariationTypePeople();
            
            //variación por porcentaje
            if($variationType == 1) {
                $amountBase = round(1.0 + mt_rand() / mt_getrandmax() * (30-1.0),2);
                $incrementBase = round(1.0 + mt_rand() / mt_getrandmax() * (5-1.0),2);
            } else { //variación por valor
                $amountBase = round(10.0 + mt_rand() / mt_getrandmax() * (200-10.0),2);
                $incrementBase = round(1.0 + mt_rand() / mt_getrandmax() * (10-1.0),2);
            }

            for ($i = $minPeople; $i <= $maxPeople  ; $i++) { 

                $rateByPeople = new RateByPeople();
                $rateByPeople->setRoom($currentRoom);
                $rateByPeople->setNumberPeople($i);

                // Si $i == mínimos de personas no se incrementa el valor
                if($i == $minPeople) {
                    $rateByPeople->setAmountRate(0);
                } else {
                    $rateByPeople->setAmountRate($amountBase + ($incrementBase * $i));
                }

                $manager->persist($rateByPeople);
                $manager->flush();
            }
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 80;
    }
}