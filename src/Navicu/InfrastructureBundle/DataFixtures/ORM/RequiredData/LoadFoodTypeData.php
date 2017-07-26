<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Navicu\Core\Domain\Model\Entity\FoodType;

/**
 * Clase LoadPropertyServiceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de comidas que ofrecen los restaurantes
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadFoodTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {

        $foods = array(
            'China',
            'Africana',
            'Americana',
            'Árabe',
            'Cubana',
            'China',
            'Coreana',
            'Española',
            'Etíope',
            'Francesa',
            'Fusión',
            'Gourmet',
            'Halal',
            'India',
            'Italiana',
            'Japonesa',
            'Libanesa',
            'Mediterránea',
            'Mexicana',
            'Nepalí',
            'Peruana',
            'Sin Gluten',
            'Tailandesa',
            'Tex-Mex',
            'Vegetariana',
            'Venezolana',
            'Hamburguesas',
            'Otros Tipos'
        );
        foreach ($foods as $food) {
            $food1 = new FoodType(); 
            $food1->setTitle($food);
            $manager->persist($food1);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}