<?php
namespace Navicu\InfrastructureBundle\Tests\Unit\InfrastructureBundle\Resources\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Navicu\InfrastructureBundle\Resources\Services\RepositoryFactory;

class DbRepositoryFactoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp()
    {
        // Asi se obtiene el EntityManager desde las pruebas unitarias
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\InfrastructureBundle\\Resources\\Services\\RepositoryFactory\n";
    }

    public function testGetDbAccommodationRepository()
    {
        echo "* Prueba: Instanciacion correcta de DbAccommodationRepository\n";

        $rf = new RepositoryFactory($this->em);
        $dbAccoRepo = $rf->get('Accommodation');

        $this->assertInstanceOf('Navicu\InfrastructureBundle\Repositories\DbAccommodationRepository', $dbAccoRepo);
    }
}

/* End of file */