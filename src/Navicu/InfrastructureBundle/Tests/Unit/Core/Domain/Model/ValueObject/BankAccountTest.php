<?php

namespace Navicu\InfrastructureBundle\Tests\Model\ValueObject;

use Navicu\Core\Domain\Model\ValueObject\BankAccount;

class BankAccountTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateValidBankAccount()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Crear una cuenta bancaria correcta\n";

        try {
            $bankAccount = new BankAccount('1234', '5678', '90', '1324567890');
            echo "* Resultado: Prueba Exitosa\n";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function testBankAccountToString()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Metodo toString()\n";

        $bankAccount = new BankAccount('1234', '5678', '90', '1234567890');

        $this->assertEquals($bankAccount->toString(), '1234-5678-90-1234567890');
    }

    public function testBankAccountToArrayGetEntitySection()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Obtener la seccion Entity al convertir a array\n";

        $bankAccount = new BankAccount('1234', '5678', '90', '1234567890');
        $bankAccountArray = $bankAccount->toArray();

        $this->assertEquals($bankAccountArray['entity'], '1234');
    }

    public function testBankAccountToArrayGetOfficeSection()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Obtener la seccion Office al convertir a array\n";

        $bankAccount = new BankAccount('1234', '5678', '90', '1234567890');
        $bankAccountArray = $bankAccount->toArray();

        $this->assertEquals($bankAccountArray['office'], '5678');
    }

    public function testBankAccountToArrayGetControlSection()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Obtener la seccion Control al convertir a array\n";

        $bankAccount = new BankAccount('1234', '5678', '90', '1234567890');
        $bankAccountArray = $bankAccount->toArray();

        $this->assertEquals($bankAccountArray['control'], '90');
    }

    public function testBankAccountToArrayGetAccountSection()
    {
        echo "------------------------------\n";
        echo "* Clase: Navicu\\Core\\Domain\\Model\\ValueObject\\BankAccount\n";
        echo "* Prueba: Obtener la seccion Account al convertir a array\n";

        $bankAccount = new BankAccount('1234', '5678', '90', '1234567890');
        $bankAccountArray = $bankAccount->toArray();

        $this->assertEquals($bankAccountArray['account'], '1234567890');
    }
}

/* End of file */