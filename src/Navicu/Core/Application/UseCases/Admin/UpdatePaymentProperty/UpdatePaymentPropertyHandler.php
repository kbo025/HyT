<?php

namespace Navicu\Core\Application\UseCases\Admin\UpdatePaymentProperty;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Agreement;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\ValueObject\BankAccount;
use Navicu\Core\Application\Services\LogsUserService;
use Navicu\Core\Domain\Model\Entity\LogsUser;
use Navicu\Core\Domain\Adapter\CoreSession;


class UpdatePaymentPropertyHandler implements Handler
{

    /**
     *   instancia del repositoryFactory
     */
    protected $rf;

    /**
     *  el comando que se ejecutó
     */
    protected $command;

    /**
     * @var Almacena la data necesaria para generar el pdf personalizado de terminos y condiciones
     */
    protected $response;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Carlos Aguilera <ceaf.21@gmail.com>
     * @author Currently Working: Carlos Aguilera <ceaf.21@gmail.com>
     * @version 20-11-15
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $this->response = array();
        $this->command = $command;
        $rProperty = $this->rf->get('Property');
        $rPaymentInfoProperty = $this->rf->get('PaymentInfoProperty');
        $rLocation = $this->rf->get('Location');
        $rLanguage = $this->rf->get('Language');
        $rCurrencyType = $this->rf->get('CurrencyType');
        $property = $rProperty->findOneByArray(array('slug'=>$this->command->get('slug')));

        if (!is_null($property)) {
            try {

                $payment = $property->getPaymentInfo();
                $agreement = $property->getAgreement();

                /* Datos para el log*/
                $paymentOld = clone $payment;
                $propertyOld = clone $property;
                $aggrementOld = clone $agreement;

                $agreement->setCreditDays($this->command->get('creditDays'));
                $property->setDiscountRate($this->command->get('discountRate'));

                $payment->setSameDataProperty($this->command->get('same_data_property'));
                if($this->command->get('same_data_property')==true){
                    $payment->setName($property->getName());
                    $payment->setLocation($property->getLocation());
                    $payment->setAddress($property->getAddress());
                }else{
                    $payment->setName($this->command->get('name'));
                    $location = $rLocation->findOneByArray(
                    array('id' => $this->command->get('location')));
                    $payment->setLocation($location);
                    $payment->setAddress($this->command->get('address'));
                }
                $payment->setTaxId($this->command->get('fiscal_code'));
                $payment->setChargingSystem($this->command->get('charging_system'));
                $account = new BankAccount(
                    $this->command->get('account_number_part1'),
                    $this->command->get('account_number_part2'),
                    $this->command->get('account_number_part3'),
                    $this->command->get('account_number_part4')
                    );
                $payment->setAccount($account);
                
                $currencyTolookUp = $this->command->get('currency_id');
                if ( ( empty($this->command->get('currency_id')) ) OR is_null($this->command->get('currency_id')) )
                    $currencyTolookUp = 148; // "Por defecto le dejamos bolivar"

                $currency = $rCurrencyType->findOneByArray(
                    array('id' => $currencyTolookUp)
                );
                $payment->setCurrency($currency);
                $payment->setSwift($this->command->get('swift'));

                /* Invocar la funcion para crear el log de ser necesario*/
                $this->createLogRegister($paymentOld, $payment, $property, $rf);

                //$rPaymentInfoProperty->save($payment);
                $rProperty->save($property);
                //$rAgreement->save($agreement);
                $response = new ResponseCommandBus(201, 'Ok');
            } catch (EntityValidationException $e) {
                $response = new ResponseCommandBus(400, 'Bad Request','Error en los datos ingresado, verifique e intente de nuevo'.$e->getAttribute().$e->getMessage());
            } catch (\Exception $e) {
                $response = new ResponseCommandBus(500, 'Internal Server Error','Hubo un error almacenando los datos');
            }
        } else {
            $response = new ResponseCommandBus(400, 'Bad Request','Error en la petición');
        }
        return $response;
    }

    /**
     * Funcion encargada de revisar los objetos existentes con los nuevos y guardarlos en la
     * entidad LogsUser
     *
     * @param object $paymentOld, objeto de tipo paymentInfo con los valores antiguos
     * @param object $property, objeto que identifica el property donde se ejecuta la accion
     * @param object $payment, objeto nuevo modificado
     * @param object $rf, repository factory
     *
     * @version 17/06/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function createLogRegister($paymentOld, $payment, $property, $rf)
    {
        /* Datos para el log de usuario, solo si los son modificados*/
        $dataToLog['property'] = $property;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['action'] = "update";
        $dataToLog['idResource'] = $paymentOld->getId();
        $dataToLog['resource'] = "payment";

        // Si los datos no son los mismos que ya existian en la BD
        if ($paymentOld->getSameDataProperty() != $payment->getSameDataProperty())
            $dataToLog['description']['sameDataProperty'] = $payment->getSameDataProperty();
        if (!$paymentOld->getAddress())
            $dataToLog['description']['address'] = $property->getAddress(); //$property->getName();
        else if ($paymentOld->getAddress() != $payment->getAddress())
            $dataToLog['description']['address'] = $payment->getAddress();
        if (!$paymentOld->getLocation())
            $dataToLog['description']['location'] = $property->getLocation()->getId();
        else if ($paymentOld->getLocation()->getId() != $payment->getLocation()->getId())
            $dataToLog['description']['location'] = $payment->getLocation()->getId();
        if (!$paymentOld->getName())
            $dataToLog['description']['name'] = $property->getName();
        else if ($paymentOld->getName() != $payment->getName())
            $dataToLog['description']['name'] = $payment->getName();
        if ($paymentOld->getTaxId() != $payment->getTaxId())
            $dataToLog['description']['taxId'] = $payment->getTaxId();
        if ($paymentOld->getChargingSystem() != $payment->getChargingSystem())
            $dataToLog['description']['chargeSystem'] = $payment->getChargingSystem();
        if ($paymentOld->getAccount() != $payment->getAccount()->toArray())
            $dataToLog['description']['account'] = $payment->getAccount();
        if (!is_null($paymentOld->getCurrency()) AND ($paymentOld->getCurrency()->getId() != $payment->getCurrency()->getId()))
            $dataToLog['description']['currency'] = $payment->getCurrency()->getId();
        if ( ($paymentOld->getSwift() != $payment->getSwift()) && ( !is_null($payment->getSwift()) ) )
            $dataToLog['description']['switf'] = $payment->getSwift();

        /* Crear el log con los valores modificados*/
        if (isset($dataToLog['description'])) {
            $logsUser = new LogsUser();
            $logsUser->updateObject($dataToLog);
            $rf->get('LogsUser')->save($logsUser);
        }
    }
}