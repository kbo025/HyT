<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy;

/**
 * Clase LoadPropertyCancellationPolicyRule "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de las reglas politicas de cancelación.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */

class LoadPropertyCancellationPolicyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {

        //Se obtiene todas los establecimientos             
        $properties = $manager->getRepository("NavicuDomain:Property")->findAll();

        foreach ($properties as $currentProperty) {

            //Se obtiene todas las politicas de cancelación        
            $cancellationPolicies = $manager->getRepository("NavicuDomain:CancellationPolicy")
            ->findAll();
            foreach ($cancellationPolicies as $currentCancellationPolicy) {

                if (rand(1,5) >= 2) {
                    // politica de cancelacion para el establecimiento
                    $propertyCancellationPolicy = new PropertyCancellationPolicy();
                    $propertyCancellationPolicy->setCancellationPolicy($currentCancellationPolicy);
                    $propertyCancellationPolicy->setProperty($currentProperty);

                    foreach ($currentProperty->getRooms() as $currentRoom) {
                        foreach ($currentRoom->getPackages() as $currentPack) {
                            $propertyCancellationPolicy->addPackage($currentPack);
                        }
                    }
                    $manager->persist($propertyCancellationPolicy);
                    $manager->flush();
                }

            }
            
            $propertyCancellationPolicy = $manager
                ->getRepository("NavicuDomain:PropertyCancellationPolicy")
            ->findByProperty($currentProperty->getId());

            
            $currentProperty->setBasePolicy($propertyCancellationPolicy['0']);

            $manager->persist($currentProperty);
            $manager->flush();
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */
    public function getOrder()
    {
        return 33;
    }
}
