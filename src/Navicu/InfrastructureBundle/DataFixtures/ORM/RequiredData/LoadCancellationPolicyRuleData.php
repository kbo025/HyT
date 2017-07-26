<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\CancellationPolicyRule;

/**
 * Clase LoadCancellationPolicyRule "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, de las reglas politicas de cancelación.
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */

class LoadCancellationPolicyRuleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        //Se obtiene todas las politicas de cancelación        
        $cancellationPolicies = $manager->getRepository("NavicuDomain:CancellationPolicy")
            ->findAll();

        foreach ($cancellationPolicies as $currentCancellationPolicy) {

            $cancellationPolicyRule = new CancellationPolicyRule();
            $variationType = $currentCancellationPolicy->getVariationTypeRule();

            if ($currentCancellationPolicy->getType()->getTitle() == "No Refundable") {

                $cancellationPolicyRule->setUpperBound(365);
                $cancellationPolicyRule->setBottomBound(0);
                $cancellationPolicyRule->setCancellationPolicy($currentCancellationPolicy);
                $cancellationPolicyRule->setVariationAmount($this->getVariationAmount($variationType ));

                $manager->persist($cancellationPolicyRule);
                $manager->flush();

            } else {

                $cancellationPolicyRule2 = new CancellationPolicyRule();
                $cancellationPolicyRule3 = new CancellationPolicyRule();
                $topRand = rand(12,20);
                $bottomRand = rand(5,8);

                $cancellationPolicyRule->setUpperBound(365);
                $cancellationPolicyRule->setBottomBound($topRand);
                $cancellationPolicyRule->setCancellationPolicy($currentCancellationPolicy);
                $cancellationPolicyRule->setVariationAmount($this->getVariationAmount($variationType ));
                
                
                $cancellationPolicyRule2->setUpperBound($topRand);
                $cancellationPolicyRule2->setBottomBound($bottomRand);
                $cancellationPolicyRule2->setCancellationPolicy($currentCancellationPolicy);
                $cancellationPolicyRule2->setVariationAmount($this->getVariationAmount($variationType ));
                
                
                $cancellationPolicyRule3->setUpperBound($bottomRand);
                $cancellationPolicyRule3->setBottomBound(0);
                $cancellationPolicyRule3->setCancellationPolicy($currentCancellationPolicy);
                $cancellationPolicyRule3->setVariationAmount($this->getVariationAmount($variationType ));

                $manager->persist($cancellationPolicyRule);
                $manager->persist($cancellationPolicyRule2);
                $manager->persist($cancellationPolicyRule3);
                $manager->flush();
                
            }

        }

    }

    public function getVariationAmount($variationType)
    {
        if ($variationType == 1) {//Porcentaje
            return rand(30, 80)/100;
        } else { // Monto Fijo
            return rand(250, 1000);;
        }
    }
    
    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */
    public function getOrder()
    {
        return 32;
    }
}
