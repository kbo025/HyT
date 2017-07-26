<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\Entity\propertyService;
use Navicu\Core\Domain\Model\ValueObject\Schedule;

/**
 * Clase Loadproperty_serviceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los servicios adicionales que ofrece los establecimientos.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class Loadproperty_serviceData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        $property_service = new PropertyService();

        $properties = $manager->getRepository("NavicuDomain:Property")->findAll();
        $ServiceType = $manager->getRepository("NavicuDomain:ServiceType");

        $wifi = $ServiceType->findOneByTitle('Wifi');
        $gym = $ServiceType->findOneByTitle('Gym');
        $spa = $ServiceType->findOneByTitle('Spa');
        $rentCar = $ServiceType->findOneByTitle('Remo');
        
        foreach ($properties as $currentProperty) {

            $property_service = new PropertyService();

            //Por defecto todos los establecimientos cuenta solo Wi-fi como servicio adicional
            $property_service->setProperty($currentProperty);
            $property_service->SetType($wifi);
            $property_service->setCostService(1.0 + mt_rand() / mt_getrandmax() * (3-1.0));
            $property_service->setFree(false);
            $manager->persist($property_service);
            $manager->flush();

            //La variable $rand define cuantos servicios adicionales tendra la propiedad
            $rand = rand(1,4);
            if($rand > 1) {
                //Se carga el servicio Spa
                $property_service = new PropertyService();
                $property_service->setProperty($currentProperty);
                $property_service->SetType($spa);
                $property_service->setCostService(1.0 + mt_rand() / mt_getrandmax() * (9-1.0));
                $property_service->setFree(true);
                $property_service->setQuantity(40);
                $property_service->setSchedule(new Schedule(\DateTime::createFromFormat('H:i:s','08:00:00'),\DateTime::createFromFormat('H:i:s','12:00:00')));
                $manager->persist($property_service);
                $manager->flush();

                if($rand > 2) {
                    //Se carga el servicio Gym
                    $property_service = new PropertyService();
                    $property_service->setProperty($currentProperty);
                    $property_service->SetType($gym);
                    $property_service->setCostService(1.0 + mt_rand() / mt_getrandmax() * (6-1.0));
                    $property_service->setFree(false);
                    $property_service->setQuantity(20);
                    $property_service->setSchedule(new Schedule(\DateTime::createFromFormat('H:i:s','08:00:00'),\DateTime::createFromFormat('H:i:s','12:00:00')));
                    $manager->persist($property_service);
                    $manager->flush();

                    if($rand > 3) {
                        //Se carga el servicio rentCar
                        $property_service = new PropertyService();
                        $property_service->setProperty($currentProperty);
                        $property_service->SetType($rentCar);
                        $property_service->setCostService(10.0 + mt_rand() / mt_getrandmax() * (35-10));
                        $property_service->setFree(true);
                        $manager->persist($property_service);
                        $manager->flush();
                    }
                }
            }            
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 30;
    }
}