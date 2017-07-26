<?php

namespace Navicu\InfrastructureBundle\Tests\Core\Application\UseCases\RegisterTempProperty;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand;

/**
* pruebas para el comando register temppropertycommandtest
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 27/05/2015
*/
class RegisterTempPropertyCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCommandRegisterTempProperty()
    {
        echo "------------------------------\n";
        echo "* Clase: RegisterTempPropertyCommand\n";
        echo "* Prueba: Crear un commando RegisterTempPropertyCommand\n";
        $command = new RegisterTempPropertyCommand();
        $this->assertInstanceOf('Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand', $command);
        echo "* Resultado: Prueba Exitosa\n";   
    }

    public function testCreateCommandRegisterTempPropertyArray()
    {
        echo "------------------------------\n";
        echo "* Clase: RegisterTempPropertyCommand\n";
        echo "* Prueba: Crear un commando RegisterTempPropertyCommand con datos\n";
        $command_array = array(
                'name'=>'El Matadero',
                'emails'=>'tirarico@xxx.com',
                'address'=>'direccion',
                'star'=>1,
                'url_web'=>'http://matadero.com.ve',
                'amount_room'=>7,
                'number_floor'=>25,
                'check_in'=>'4:00 pm',
                'check_out'=>'2:00 pm',
                'phones'=>'+582418783924',
                'fax'=>'+582418783924',
                'opening_year'=>2012,
                'renewal_year'=>2012,
                'public_areas_renewal_year'=>2012,
                'check_in_age'=>21,
                'child_max_age'=>12,
                'baby_max_age'=>2,
                'tax'=>12,
                'tax_rate'=>12,
                'additional_info'=>'Ninguna',
                'longitude'=>50,
                'latitude'=>50,
                'slug'=>5,
            );
        $command = new RegisterTempPropertyCommand($command_array);
        $this->assertInstanceOf('Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand', $command);
        echo "* Resultado: Prueba Exitosa\n";   
    }

    public function testGetSetCommandRegisterTempPropert()
    {
        echo "------------------------------\n";
        echo "* Clase: RegisterTempPropertyCommand\n";
        echo "* Prueba: Datos Validos en RegisterTempPropertyCommand\n";
        $command_array = array(
                'name'=>'El Matadero',
                'emails'=>'tirarico@xxx.com',
                'address'=>'direccion',
                'star'=>1,
                'url_web'=>'http://matadero.com.ve',
                'amount_room'=>7,
                'number_floor'=>25,
                'check_in'=>'4:00 pm',
                'check_out'=>'2:00 pm',
                'phones'=>'+582418783924',
                'fax'=>'+582418783924',
                'opening_year'=>2012,
                'renewal_year'=>2012,
                'public_areas_renewal_year'=>2012,
                'check_in_age'=>21,
                'child_max_age'=>12,
                'baby_max_age'=>2,
                'tax'=>12,
                'tax_rate'=>12,
                'additional_info'=>'Ninguna',
                'longitude'=>50,
                'latitude'=>50,
                'slug'=>5
            );
        $command = new RegisterTempPropertyCommand($command_array);
        $request = $command->getRequest();
        $this->assertEquals($request['slug'],5);
        $this->assertEquals(1,$command->getStar());
        $command->setName('El Super Matadero');
        $this->assertEquals('El Super Matadero',$command->getName());
        echo "* Resultado: Prueba Exitosa\n";
    }
}