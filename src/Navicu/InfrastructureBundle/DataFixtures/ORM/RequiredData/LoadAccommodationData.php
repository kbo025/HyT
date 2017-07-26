<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Accommodation;

/**
 * Clase LoadPropertyServiceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de comidas que ofrecen los restaurantes
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadAccommodationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        $acc1 = new Accommodation();
        $acc1->setTitle('Apartamento');
        $manager->persist($acc1);
        $acc2 = new Accommodation();
        $acc2->setTitle('Posada');
        $manager->persist($acc2);
        $acc3 = new Accommodation();
        $acc3->setTitle('Camping');
        $manager->persist($acc3);
        $acc4 = new Accommodation();
        $acc4->setTitle('Villa');
        $manager->persist($acc4);
        $acc5 = new Accommodation();
        $acc5->setTitle('Casa rural');
        $manager->persist($acc5);
        $acc6 = new Accommodation();
        $acc6->setTitle('Hostales');
        $manager->persist($acc6);
        $acc7 = new Accommodation();
        $acc7->setTitle('Pensiones');
        $manager->persist($acc7);
        $acc8 = new Accommodation();
        $acc8->setTitle('Barcos');
        $manager->persist($acc8);
        $acc9 = new Accommodation();
        $acc9->setTitle('Bed and Breakfast');
        $manager->persist($acc9);
        $acc10 = new Accommodation();
        $acc10->setTitle('Aparthoteles');
        $manager->persist($acc10);
        $acc11 = new Accommodation();
        $acc11->setTitle('Moteles');
        $manager->persist($acc11);
        $acc12 = new Accommodation();
        $acc12->setTitle('Love hotels');
        $manager->persist($acc12);
        $acc13 = new Accommodation();
        $acc13->setTitle('Resorts');
        $manager->persist($acc13);
        $acc14 = new Accommodation();
        $acc14->setTitle('Casas de montaña');
        $manager->persist($acc14);
        $acc15 = new Accommodation();
        $acc15->setTitle('Hoteles');
        $manager->persist($acc15);
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}