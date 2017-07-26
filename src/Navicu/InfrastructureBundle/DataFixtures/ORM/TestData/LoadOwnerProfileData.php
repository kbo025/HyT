<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;

/**
 * Clase LoadOwnerProfileData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, se cargan los datos del usuario propiertario de un establecimiento.
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadOwnerProfileData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {   
        //Se crean 3 usuarios, 1 usuario propietario y 2 usuarios asistentes
        for ($i = 1; $i <= 3 ; $i++) { 
            $ownerProfile = new OwnerProfile();
            $ownerProfile->setNames('Names'.$i);
            $ownerProfile->setLastNames('LastNames'.$i);
            $ownerProfile->setPhones('0412-0000'.$i);
            $ownerProfile->setFax('012031301'.$i);

            //Se obtiene el establecimiento 1
            $property1 = $manager->getRepository('NavicuDomain:Property')->findOneById(1);
            //Se obtiene establecimiento 2
            $property2 = $manager->getRepository('NavicuDomain:Property')->findOneById(2);

            //Se carga el usario propiertario
            if($i == 1) {

                $typeOwner = $manager->getRepository('NavicuDomain:Category')
                    ->findOneByTitle('Super Owner');
                $superOwner = $ownerProfile;

                $ownerProfile->addProperty($property1);
                $ownerProfile->addProperty($property2);

            } else if($i <= 3){ //Se cargan los usuarios asistentes

                $typeOwner = $manager->getRepository('NavicuDomain:Category')
                    ->findOneByTitle('Assistant Owner');
                $ownerProfile->setParent($superOwner);

                //Se asigna al usuario asistente el establecimiento 1
                if($i == 2) { 
                    $ownerProfile->addProperty($property1);
                } else { //Se asigna al usuario asistente el establecimiento 2
                    $ownerProfile->addProperty($property2);
                }
            }

            $ownerProfile->setOffice($typeOwner);

            $manager->persist($ownerProfile);
            $manager->flush();
        }
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 40;
    }
}