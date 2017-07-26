<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\FoodType;
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
class RestaurantTest extends \PHPUnit_Framework_TestCase
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
        $this->baseEntity = new Restaurant();
    }

    /**
    * La función Crea un Bar y estudia si las validaciones estan correctas
    *
    * @version 01/06/2015
    */    
    public function testCreateSalon()
    {
        echo "------------------------------\n";
        echo "* Clase: Restaurant\n";
        echo "* Prueba Unitaria: Crear un Restaurant Valido\n";

        $i = 1;

        $restaurant = new Restaurant();

        $this->assertFalse($restaurant->validate(),
            "\n- Metodo Validate no funciona\n");

        $restaurant->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
        $restaurant->setBreakfastTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
        $restaurant->setLunchTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
        $restaurant->setDinnerTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
        $restaurant->setName('rest'.$i);
        $restaurant->setStatus( (rand(0,100) % 2 == 0) ? true : false );

        $this->assertFalse($restaurant->validate(),
            "\n- Metodo Validate no funciona\n");

        $restaurant->setDietaryMenu( (rand(0,100) % 2 == 0) ? true : false );
        $restaurant->setBuffetCarta(1);
        $food = new FoodType();
        $food->setTitle('mexicana');
        $restaurant->setType($food);
        $restaurant->setDescription('restaurant'.$i);

        $this->assertTrue($restaurant->validate(),
            "\n- Metodo Validate no funciona\n");
    }

}
