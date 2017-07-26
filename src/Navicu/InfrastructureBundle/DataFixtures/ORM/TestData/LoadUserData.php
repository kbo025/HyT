<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * Clase LoadUserData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema de todos los usuarios. 
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {        
        //Se obtiene todos los perfiles de los usuarios (ownerProfiles)
        $ownerProfiles = $manager->getRepository("NavicuDomain:OwnerProfile")->findAll();
        $i = 1;

        /*$user1 = new User();
        $user1->setUsername('user1');
        $user1->setEmail('user1Temp@example.com');
        $user1->setPlainPassword('123456');
        $user1->setEnabled(true);
        $user1->addRole(3);
           
        $manager->persist($user1);
        $manager->flush();*/
        foreach ($ownerProfiles as $currenteOwnerProfile) {
            
            $user = new User();
            $user->setUsername('user'.($i*100));
            $user->setEmail('system'.($i*100).'@example.com');
            $user->setPlainPassword('123456');
            $user->setEnabled(true);

            //Si entra en el condicional, se carga un usuario propietario
            if($i == 1)
                $user->addRole(1);
            else //Si entra en el condicional, se carga un usuario asistente
                $user->addRole(2);

            $user->setOwnerProfile($currenteOwnerProfile);

            $manager->persist($user);
            $manager->flush();
            $i++;
        }
        
        //Carga de datos del usuario administrador
        $user2 = new User();

        $user2 = new User();
        $user2->setUsername('admin');
        $user2->setEmail('admin@example.com');
        $user2->setPlainPassword('123456');
        $user2->setEnabled(true);
        $user2->addRole(1);
           
        $manager->persist($user2);
        $manager->flush();
        
        //Carga de datos del usuario cliente
        $user3 = new User();

        $user3->setUsername('cliente');
        $user3->setEmail('cliente@example.com');
        $user3->setPlainPassword('123456');
        $user3->setEnabled(true);
        $user3->addRole(2);
           
        $manager->persist($user3);
        $manager->flush();

        //Carga de datos del usuario administrador
        $user4 = new User();

        $user4->setUsername('superadmin2');
        $user4->setEmail('superadmin2@example.com');
        $user4->setPlainPassword('123456');
        $user4->setEnabled(true);
        $user4->addRole(0);

        $manager->persist($user4);
        $manager->flush();
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */

    public function getOrder()
    {
        return 50;
    }
}