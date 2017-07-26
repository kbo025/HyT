<?php

namespace Navicu\InfrastructureBundle\Tests\Unit\Core\Domain\Model;

use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\ServiceType;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\Salon;
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
class PropertyServiceTest extends \PHPUnit_Framework_TestCase
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
        $this->baseEntity = new PropertyService();
    }

    /**
    * La función Crea un Bar y estudia si las validaciones estan correctas
    *
    * @version 01/06/2015
    */    
    public function testCreatePropertyService()
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyService\n";
        echo "* Prueba Unitaria: Crear un Servicio de hotel Valido\n";

        //tipos booleanos
        $wifi = new ServiceType();
        $wifi->setType(0);
        $remo = new ServiceType();
        $remo->setType(0);

        //tipo costos
        $gym = new ServiceType();
        $gym->setType(5);
        $spa = new ServiceType();
        $spa->setType(5);
        
        //tipo restaurant
        $restaurantes = new ServiceType();
        $restaurantes->setType(3);

        //tipo bar
        $bares = new ServiceType();
        $bares->setType(2);

        //tipo salon
        $salones = new ServiceType();
        $salones->setType(6);

        //tipo horario
        $recepcion = new ServiceType();
        $recepcion->setType(4);

        //tipo cantidad
        $piscina = new ServiceType();
        $piscina->setType(8);


        $property_service1 = new PropertyService();
        $property_service1->setType($wifi);

        $this->assertTrue($property_service1->validate(),
            "\n- Metodo Validate boolean no funciona\n");

        $property_service4 = new PropertyService();
        $property_service4->setType($remo);

        $this->assertTrue($property_service4->validate(),
            "\n- Metodo Validate boolean no funciona\n");

        $property_service2 = new PropertyService();
        $property_service2->setType($gym);
        $property_service2->setFree(false);

        $this->assertTrue($property_service2->validate(),
            "\n- Metodo Validate costo no funciona\n");

        $property_service3 = new PropertyService();
        $property_service3->setType($spa);
        $property_service3->setFree(false);

        $this->assertTrue($property_service3->validate(),
            "\n- Metodo Validate costo no funciona\n");

        $property_recepcion = new PropertyService();
        $property_recepcion->setType($recepcion);
        $property_recepcion->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));

        $this->assertTrue($property_recepcion->validate(),
            "\n- Metodo Validate horario no funciona\n");

        $property_piscina = new PropertyService();
        $property_piscina->setType($piscina);
        $property_piscina->setQuantity(3);

        $this->assertTrue($property_piscina->validate(),
            "\n- Metodo Validate cantidad no funciona\n");

        $property_salones = new PropertyService();
        $property_salones->setType($salones);
        for($i=1;$i<6;$i++)
        {
            $salon = new Salon();
            $salon->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $salon->setSize(40.75*rand(1,10));
            $salon->setCapacity(rand(40,200));
            $salon->setType(rand(1,2));
            $salon->setQuantity(rand(5,20));
            $salon->setNaturalLight( (rand(0,100) % 2 == 0) ? true : false );
            $property_salones->addSalon($salon);
        }


        $this->assertTrue($property_salones->validate(),
            "\n- Metodo Validate salones no funciona\n");

        $property_restaurantes = new PropertyService();
        $property_restaurantes->setType($restaurantes);
        for($i=1;$i<6;$i++)
        {
            $restaurant = new Restaurant();
            $restaurant->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setBreakfastTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setLunchTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setDinnerTime(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $restaurant->setName('rest'.$i);
            $restaurant->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $restaurant->setDietaryMenu( (rand(0,100) % 2 == 0) ? true : false );
            $restaurant->setBuffetCarta(1);
            $restaurant->setType(new FoodType());
            $restaurant->setDescription('restaurant'.$i);
            $property_restaurantes->addRestaurant($restaurant);
        }

        $this->assertTrue($property_restaurantes->validate(),
            "\n- Metodo Validate restaurantes no funciona\n");

        $property_bares = new PropertyService();
        $property_bares->setType($bares);
        for($i=1;$i<6;$i++)
        {
            $bar = new Bar();
            $bar->setSchedule(new Schedule(new \DateTime('08:00:00'),new \DateTime('20:00:00')));
            $bar->setStatus( (rand(0,100) % 2 == 0) ? true : false );
            $bar->setMinAge(rand(18,30));
            $bar->setFood( (rand(0,100) % 2 == 0) ? true : false );
            $bar->setName('Babylon Clud'.$i);
            $bar->setDescription('Babylon Clud'.$i);
            $property_bares->addBar($bar);
        }

        $this->assertTrue($property_bares->validate(),
            "\n- Metodo Validate bares no funciona\n");

    }

}
