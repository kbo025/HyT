<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\ValueObject\Schedule;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * room (Habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class BarTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La función Crea un Bar y estudia si las validaciones estan correctas
    *
    * @version 01/06/2015
    */    
    public function testCreateBar()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Crear un Bar Valido\n";

        $i = 1; 

        $bar = new Bar();

        $this->assertFalse($bar->validate(),
            "\n- Metodo Validate no funciona\n");

        $bar->setSchedule(new Schedule(new \DateTime('08:00:00'), new \DateTime('20:00:00')));
        $bar->setStatus( (rand(0,100) % 2 == 0) ? true : false );
        $bar->setMinAge(rand(18,30));

        $this->assertFalse($bar->validate(),
            "\n- Metodo Validate no funciona\n");

        $bar->setFood( (rand(0,100) % 2 == 0) ? true : false );
        $bar->setName('Babylon Clud'.$i);
        $bar->setDescription('Babylon Clud'.$i);

        $this->assertTrue($bar->validate(),
            "\n- Metodo Validate no funciona\n");
    }

    public function testSetBarName()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Agregar nombre al bar\n";

        $bar = new Bar();
        $bar->setName('Babylon Club');

        $this->assertEquals($bar->getName(), 'Babylon Club');
    }

    public function testGetBarType()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Obtener el tipo 'Bar' del bar\n";

        $bar = new Bar();
        $bar->setType(Bar::TYPE_BAR);

        $this->assertEquals($bar->getType(), Bar::TYPE_BAR);
    }

    public function testGetDiscotype()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Obtener el tipo 'Discoteca' del bar\n";

        $bar = new Bar();
        $bar->setType(Bar::TYPE_DISCO);

        $this->assertEquals($bar->getType(), Bar::TYPE_DISCO);
    }

    public function testGetBarStringType()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Obtener el tipo 'Bar' como string\n";

        $bar = new Bar();
        $bar->setType(Bar::TYPE_BAR);

        $this->assertEquals($bar->getTypeString(), 'Bar');
    }

    public function testGetDiscoStringType()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Obtener el tipo 'Discoteca' como string\n";

        $bar = new Bar();
        $bar->setType(Bar::TYPE_DISCO);

        $this->assertEquals($bar->getTypeString(), 'Discoteca');
    }

    public function testGetSchedule()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\Entity\\Bar\n";
        echo "* Prueba: Obtener el horario del bar\n";

        $schedule = new Schedule(new \DateTime('08:00:00'), new \DateTime('20:00:00'));
        $bar = new Bar();

        $bar->setSchedule($schedule);

        $this->assertEquals($bar->getSchedule(), $schedule);
    }
}

/* End of file */