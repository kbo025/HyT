<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\CancellationPolicy;

/**
 * Clase LoadCancellationPolicy "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de las politicas de cancelación.
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 */

class LoadCancellationPolicyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        $category = $manager->getRepository("NavicuDomain:Category");
        
        $noRefundable = $category->findOneByTitle('No Refundable');
        $politicy1 = $category->findOneByTitle('Politicy1');
        $politicy2 = $category->findOneByTitle('Politicy2');

        $currentCancellationPolicy = new CancellationPolicy();
        $currentCancellationPolicy2 = new CancellationPolicy();
        $currentCancellationPolicy3 = new CancellationPolicy();

        $currentCancellationPolicy->setType($noRefundable);
        $currentCancellationPolicy->setVariationType(2);
        $currentCancellationPolicy->setVariationAmount(0);
        $currentCancellationPolicy->setVariationTypeRule(1);


        $currentCancellationPolicy2->setType($politicy1);
        $currentCancellationPolicy2->setVariationType(1);
        $currentCancellationPolicy2->setVariationAmount(rand(30,100)/100);
        $currentCancellationPolicy2->setVariationTypeRule(rand(1,2));

        $currentCancellationPolicy3->setType($politicy2);
        $currentCancellationPolicy3->setVariationType(2);
        $currentCancellationPolicy3->setVariationAmount(rand(30,100));
        $currentCancellationPolicy3->setVariationTypeRule(rand(1,2));

        $manager->persist($currentCancellationPolicy);
        $manager->persist($currentCancellationPolicy2);
        $manager->persist($currentCancellationPolicy3);
        $manager->flush();
    }
    
    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */
    public function getOrder()
    {
        return 31;
    }
}
