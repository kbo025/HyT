<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\PackLinkage;

/**
 * Clase LoadPackLinkageData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para las vinculaciones entre servicios.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadPackLinkageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {  /* 
        //Se obtiene todos los servicios (pack)
        $packages = $manager->getRepository("NavicuDomain:Pack")->findAll();

        $i = 0;

        foreach ($packages as $currentPack) {

            //Si no es el primer servicio por habitación
            //La división es entre 4 porque son 4 servicios por habitaciones
            if($i%4 != 0) {
                
                //Si entra en este condicional se cargan vinculaciones
                //Rango de fechas : Fecha actual + 5 días / Fecha actual + 10 días
                if(rand(1,5) <3) {

                    $startDate = date_create()->modify("+5 day");
                    $endDate = date_create()->modify("+10 day");

                    $packLinkage = new PackLinkage();
                    $packLinkage->setParent($parentPack);
                    $packLinkage->setChild($currentPack);
                    $packLinkage->setStartDate($startDate);
                    $packLinkage->setEndDate($endDate);
                    $packLinkage->setIsLinkedAvailability(rand(-5,5));
                    $packLinkage->setIsLinkedCloseOut(rand(0,1));
                    $packLinkage->setIsLinkedCta(rand(0,1));
                    $packLinkage->setIsLinkedCtd(rand(0,1));
                    $packLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $packLinkage->setIsLinkedMinNight(rand(-5,5));
                    $packLinkage->setRoom($currentPack->getRoom());

                    //Si entre en el condicional, el monto es por porcentaje
                    if(rand(0,1)) {
                        $packLinkage->setVariationTypePack(1);
                        $amount = round(0.01 + mt_rand() / mt_getrandmax() * (0.3-0.01),2);
                        $packLinkage->setIsLinkedSellRate($amount);
                    } else { //Si entre en el condicional, el monto es por valor
                        $packLinkage->setVariationTypePack(2);
                        $amount = round(10.0 + mt_rand() / mt_getrandmax() * (200-10.0),2);
                        $packLinkage->setIsLinkedSellRate($amount);
                    }

                    $manager->persist($packLinkage);
                    $manager->flush();
                }

                //Si entra en este condicional se cargan vinculaciones
                //Rango de fechas : Fecha actual + 20 días / Fecha actual + 25 días
                if(rand(1,5) <3) {

                    $startDate = date_create()->modify("+20 day");
                    $endDate = date_create()->modify("+25 day");

                    $packLinkage = new PackLinkage();
                    $packLinkage->setParent($parentPack);
                    $packLinkage->setChild($currentPack);
                    $packLinkage->setStartDate($startDate);
                    $packLinkage->setEndDate($endDate);
                    $packLinkage->setIsLinkedAvailability(rand(-5,5));
                    $packLinkage->setIsLinkedCloseOut(rand(0,1));
                    $packLinkage->setIsLinkedCta(rand(0,1));
                    $packLinkage->setIsLinkedCtd(rand(0,1));
                    $packLinkage->setIsLinkedMaxNight(rand(-5,5));
                    $packLinkage->setIsLinkedMinNight(rand(-5,5));
                    $packLinkage->setRoom($currentPack->getRoom());

                    //Si entre en el condicional, el monto es por porcentaje
                    if(rand(0,1)) {
                        $packLinkage->setVariationTypePack(1);
                        $amount = round(0.01 + mt_rand() / mt_getrandmax() * (0.3-0.01),2);
                        $packLinkage->setIsLinkedSellRate($amount);
                    } else { //Si entre en el condicional, el monto es por valor
                        $packLinkage->setVariationTypePack(2);
                        $amount = round(10.0 + mt_rand() / mt_getrandmax() * (200-10.0),2);
                        $packLinkage->setIsLinkedSellRate($amount);
                    }

                    $manager->persist($packLinkage);
                    $manager->flush();
                } 

            } else { 
                //$parentPack almacena el primer servicio por habitación
                //como servicio padre en las vinculaciones de servicio a servicios
                $parentPack = $currentPack;
            }

            $i++;
        }
        */
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 110;
    }
}
