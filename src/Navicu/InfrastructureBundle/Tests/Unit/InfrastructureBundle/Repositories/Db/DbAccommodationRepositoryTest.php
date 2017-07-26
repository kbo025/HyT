<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\InfrastructureBundle\Repositories\Db;

use Symfony\Component\Config\FileLocator;
use Navicu\InfrastructureBundle\Repositories\Db\DbAccommodationRepository;

class DbAccommodationRepositoryTest extends DbBaseTestCase
{
    public function setUp()
    {
        $fl = new FileLocator('src/Navicu/InfrastructureBundle');
        $this->loadFixturesFromFile($fl->locate('DataFixtures/ORM/RequiredData/LoadAccommodationData.php'));

        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\InfrastructureBundle\\Repositories\\Db\\DbAccommodationRepository\n";
    }

    public function testGetAccommodationList()
    {
        echo "* Prueba: Obtener listado de tipos de alojamientos\n";

        $repo = new DbAccommodationRepository($this->em);
        $result = $repo->getAccommodationList();

        $this->assertCount(15, $result, 'Deben haber 15 tipos de alojamientos.');

        $this->assertEquals('Apartamento', $result[0]['name']);
        $this->assertEquals('Barcos', $result[2]['name']);
        $this->assertEquals('Camping', $result[4]['name']);
        $this->assertEquals('Casas de montaña', $result[6]['name']);
    }

    public function testGetAllWithKeys()
    {
        echo "* Prueba: Obtener listado de tipos de alojamientos con array de tuplas Clave:Valor\n";

        $repo = new DbAccommodationRepository($this->em);
        $result = $repo->getAllWithKeys();

        $this->assertCount(15, $result, 'Deben haber 15 tipos de alojamientos.');

        $this->assertEquals('Hoteles', end($result)->getTitle());
        $this->assertEquals('Casas de montaña', prev($result)->getTitle());
    }

    public function testGetByName()
    {
        echo "* Prueba: Obtener un Tipo de Alojamiento dado su nombre\n";

        $repo = new DbAccommodationRepository($this->em);
        $result = $repo->getByName('Posada');

        $this->assertInstanceOf('Navicu\Core\Domain\Model\Entity\Accommodation', $result);

        $this->assertEquals('Posada', $result->getTitle());
    }
}

/* End of file */