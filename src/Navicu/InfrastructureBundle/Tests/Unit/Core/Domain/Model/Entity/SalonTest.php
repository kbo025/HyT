<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\Salon;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas unitarias de la entidad
 * room (Habitación)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class SalonTest extends \PHPUnit_Framework_TestCase
{
    /**
    * La variable representa la entidad base
    *
    * @var Object Room
    */
    private $baseEntity;

    /**
    * Metodo constructor asignado las clases bases a las variables de la clase
    */
    public function __construct()
    {
        $this->baseEntity = new Salon();
    }

    /**
    * La función Crea un Bar y estudia si las validaciones estan correctas
    *
    * @version 01/06/2015
    */    
    public function testCreateSalon()
    {
        echo "------------------------------\n";
        echo "* Clase: Salon\n";
        echo "* Prueba Unitaria: Crear un Salon Valido\n";

        $salon = new Salon();

        $this->assertFalse($salon->validate(),
            "\n- Metodo Validate no funciona\n");

        $salon->setStatus( (rand(0,100) % 2 == 0) ? true : false );
        $salon->setSize(40.75*rand(1,10));
        $salon->setCapacity(rand(40,200));

        $this->assertFalse($salon->validate(),
            "\n- Metodo Validate no funciona\n");

        $salon->setType(rand(1,2));
        $salon->setQuantity(rand(5,20));
        $salon->setNaturalLight( (rand(0,100) % 2 == 0) ? true : false );

        $this->assertTrue($salon->validate(),
            "\n- Metodo Validate no funciona\n");

    }

}
