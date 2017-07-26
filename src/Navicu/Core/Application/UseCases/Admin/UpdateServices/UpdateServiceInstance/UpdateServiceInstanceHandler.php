<?php
namespace Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateServiceInstance;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Symfony\Component\Config\Definition\Exception\Exception;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

class UpdateServiceInstanceHandler implements Handler
{

    /**
     * Instancia del comando que llama el caso de uso
     * @var Command $command
     */
    private $command;

    /**
     *  Instancia del RepositoryFactory
     *  @var RepositoryFactoryInterface
     */
    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->command = $command;
        $this->rf = $rf;
        $rProperty = $this->rf->get('Property');
        $property = $rProperty->findOneByArray(array('slug'=>$this->command->get('slug')));
        $data =  $this->command->get('data');
        if (!is_null($property)) {
            $rServiceType = $this->rf->get('ServiceType');
            $serviceType = $rServiceType->getById($this->command->get('idType'));
            if (!is_null($serviceType)) {

                if ( $serviceType->isRestaurant() ) {
                    $entity = 'Restaurant';
                    $setInstanceMethod = 'setRestaurant';
                    $addInstanceMethod = 'addRestaurant';
                } elseif ( $serviceType->isBar() ) {
                    $entity = 'Bar';
                    $setInstanceMethod = 'setBar';
                    $addInstanceMethod = 'addBar';
                } elseif ( $serviceType->isSalon() ) {
                    $entity = 'Salon';
                    $setInstanceMethod = 'setSalon';
                    $addInstanceMethod = 'addSalon';
                } else {
                    return new ResponseCommandBus(400,'Bad Request tipo de servicio Incorrecto');
                }

                $rInstance = $this->rf->get($entity);
                if(!empty($data['id'])) {
                    $instance = $rInstance->getById($data['id']);
                    $oldInstance = clone $instance;
                }
                else
                    $instance = null;
                try {
                    $this->$setInstanceMethod($instance);
                } catch (EntityValidationException $e) {
                    return new ResponseCommandBus(400, 'Bad Request hubo un error de validcacion en los tipos de datos del servicio');
                } catch (Exception $e) {
                    return new ResponseCommandBus(400, 'Bad Request hubo un error en los tipos de datos del servicio');
                }
                $rPropertyService = $this->rf->get('PropertyService');
                $propertyService = $rPropertyService->findByPropertyType($property->getId(), $serviceType->getId());
                if (empty($propertyService)) {
                        $propertyService = new PropertyService();
                        $propertyService->setType($serviceType);
                        $propertyService->setProperty($property);
                        $property->addService($propertyService);
                        $rPropertyService->save($propertyService);
                    }

                $propertyServiceInstance = $instance->getService();
                if (empty($propertyServiceInstance)) {
                        $propertyService->$addInstanceMethod($instance);
                        $instance->setService($propertyService);
                    }

                $rInstance->save($instance);

                /* Datos para el log*/
                $service = " service";
                if (isset($oldInstance)) {
                    $action = "update";
                    $this->updateServiceToLog($instance, $oldInstance, $entity, $service, $action, $property, $rf);
                }
                else {
                    $action = "create";
                    $this->saveNewServiceToLog($instance, $entity, $service, $action, $property, $rf);
                }

                return new ResponseCommandBus(201, 'OK Actualizó',$instance->toArray());

            } else {
                return new ResponseCommandBus(400,'Bad Request tipo de servicio no existe');
            }
        } else {
            return new ResponseCommandBus(400,'Bad Request establecimiento no existe');
        }
    }

    private function setRestaurant(Restaurant &$restaurant = null)
    {
        $data = $this->command->get('data');
        if (is_null($restaurant))
            $restaurant = new Restaurant();
        $restaurant->setName($data['nombre']);
        $restaurant->setDescription($data['descripcion']);
        $restaurant->setBuffetCarta($data['buffet_carta']);
        $restaurant->setDietaryMenu($data['menu_dietetico']);
        $restaurant->setStatus($data['status']);
        //foodType
        $rFoodType = $this->rf->get('FoodType');
        $food = $rFoodType->getById($data['tipo_cocina']);
        $restaurant->setType($food);
        //Schedule
        $schedule = $data['breakfast'];
        $restaurant->setBreakfastTime(
            !empty($schedule['breakfast']) ?
                new Schedule($schedule['apertura'], $schedule['cierre']) :
                null
        );
        $schedule = $data['dinner'];
        $restaurant->setDinnerTime(
            !empty($schedule['dinner']) ?
                new Schedule($schedule['apertura'], $schedule['cierre']) :
                null
        );
        $schedule = $data['lunch'];
        $restaurant->setLunchTime(
            !empty($schedule['lunch']) ?
                new Schedule($schedule['apertura'], $schedule['cierre']) :
                null
        );
        $restaurant->setSchedule(new Schedule(null, null, $data['dias_apertura'],false));
    }

    private function setBar(Bar &$bar = null)
    {
        $data = $this->command->get('data');
        if (is_null($bar))
            $bar = new Bar();

        $schedule = $data['horario'];
        $bar->setSchedule(
            new Schedule($schedule['apertura'],$schedule['cierre'])
        );
        $bar->setStatus($data['status']);
        $bar->setDescription($data['descripcion']);
        $bar->setFood(!empty($data['comida']));
        if($bar->getFood()) {
            $rFoodType = $this->rf->get('FoodType');
            $food = $rFoodType->getById($data['tipo_comida']);
        } else {
            $food = null;
        }
        $bar->setFoodType($food);
        $bar->setMinAge($data['edad_min']);
        $bar->setName($data['nombre']);
        $bar->setType($data['tipo']);
    }

    private function setSalon(Salon &$salon = null)
    {
        $data = $this->command->get('data');
        if (is_null($salon))
            $salon = new Salon();

        $salon->setCapacity($data['capacidad']);
        $salon->setName($data['nombre']);
        $salon->setNaturalLight($data['luz_natural']);
        $salon->setStatus($data['status']);
        $salon->setSize($data['tamano']);
        $salon->setType($data['tipo']);
        $salon->setDescription(!empty($data['descripcion']) ? $data['descripcion'] : null);
    }

    /**
     * Funcion encargada de guardar cuando un servicio es generado por primera vez
     *
     * @param object $instance, tipo de servicio que se quiere guardar en la base de datos del log
     * @param string $entity, definicion sobre la entidad que esta sufriendo el cambio
     * @param string $service, string con la palabra " Service"
     * @param string $action, accion CRUD que se esta llevando a cabo
     * @param $property
     * @param $rf
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 22/06/2016
     */
    public function saveNewServiceToLog($instance, $entity, $service, $action, $property, $rf) {
        $dataToLog['action'] = $action;
        $dataToLog['resource'] = $entity.$service;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        $dataToLog['idResource'] = $instance->getId();
        $dataToLog["description"]['name'] = $instance->getName();

        if ($entity == "Restaurant") {
            if (!is_null($instance->getBreakfastTime()))
                $dataToLog["description"]['breakfast_time'] = $instance->getBreakfastTime()->toArray();
            if (!is_null($instance->getLunchTime()))
                $dataToLog["description"]['lunch_time'] = $instance->getLunchTime()->toArray();
            if (!is_null($instance->getDinnerTime()))
                $dataToLog["description"]['dinner_time'] = $instance->getDinnerTime()->toArray();
            if ($instance->getDietaryMenu())
                $dataToLog["description"]['dietary_menu'] = $instance->getDietaryMenu();
            if (!is_null($instance->getSchedule()))
                $dataToLog["description"]['schedule'] = $instance->getSchedule();
            $dataToLog["description"]['food_type'] = $instance->getType()->getTitle();
        }
        else if ($entity == "Bar") {
            if (!is_null($instance->getMinAge()))
                $dataToLog["description"]['min_age'] = $instance->getMinAge();
            if ($instance->getFood())
                $dataToLog["description"]['food_type'] = $instance->getFoodType()->getTitle();
            if ($instance->getType() == 1)
                $dataToLog["description"]['type'] = "bar";
            else
                $dataToLog["description"]['type'] = "disco";
            if (!is_null($instance->getSchedule()))
                $dataToLog["description"]['schedule'] = $instance->getSchedule();
        }
        else if ($entity == "Salon") {
            if ($instance->getType() == 1)
                $dataToLog["description"]['type'] = "salon";
            else if ($instance->getType() == 2)
                $dataToLog["description"]['type'] = "auditorium";
            else
                $dataToLog["description"]['type'] = "teatro";
            $dataToLog["description"]['capacity'] = $instance->getCapacity();
            $dataToLog["description"]['natural_light'] = $instance->getNaturalLight();
            $dataToLog["description"]['size'] = $instance->getSize();
            if (!is_null($instance->getQuantity()))
                $dataToLog["description"]['quantity'] = $instance->getQuantity();
        }

        if (count($instance->getDescription()) > 1)
            $dataToLog["description"]['description'] = $instance->getDescription();
        $dataToLog["description"]['status'] = $instance->getStatus();

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }

    /**
     * Funcion encargada de actualizar cuando un servicio es modificado
     *
     * @param object $instance, tipo de servicio que se quiere guardar en la base de datos del log
     * @param object $oldInstance, clon previo a la modificacion
     * @param string $entity, definicion sobre la entidad que esta sufriendo el cambio
     * @param string $service, string con la palabra " Service"
     * @param string $action, accion CRUD que se esta llevando a cabo
     * @param $property
     * @param $rf
     *
     * @version 27/06/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function updateServiceToLog($instance, $oldInstance, $entity, $service, $action, $property, $rf) {
        $dataToLog['action'] = $action;
        $dataToLog['resource'] = $entity.$service;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        $dataToLog['idResource'] = $instance->getId();
        $dataToLog["description"]['name'] = $instance->getName();

        if ($entity == "Restaurant") {
            if (!is_null($instance->getBreakfastTime())) {
                if ($instance->getBreakfastTime()->toArray() != $oldInstance->getBreakfastTime()->toArray())
                    $dataToLog["description"]['breakfast_time'] = $instance->getBreakfastTime()->toArray();
            }
            if (!is_null($instance->getLunchTime())) {
                if ($instance->getLunchTime()->toArray() != $oldInstance->getLunchTime()->toArray())
                    $dataToLog["description"]['lunch_time'] = $instance->getLunchTime()->toArray();
            }
            if (!is_null($instance->getDinnerTime())) {
                if ($instance->getDinnerTime()->toArray() != $oldInstance->getDinnerTime()->toArray())
                    $dataToLog["description"]['dinner_time'] = $instance->getDinnerTime()->toArray();
            }
            if ($instance->getDietaryMenu()) {
                if ($instance->getDietaryMenu() != $oldInstance->getDietaryMenu())
                    $dataToLog["description"]['dietary_menu'] = $instance->getDietaryMenu();
            }
            if (!is_null($instance->getSchedule())) {
                if ($instance->getSchedule() != $oldInstance->getSchedule())
                    $dataToLog["description"]['schedule'] = $instance->getSchedule();
            }
            if ($instance->getType()->getTitle() != $oldInstance->getType()->getTitle())
                $dataToLog["description"]['food_type'] = $instance->getType()->getTitle();
        }
        else if ($entity == "Bar") {
            if (!is_null($instance->getMinAge()) && ($instance->getMinAge() != $oldInstance->getMinAge()) )
                $dataToLog["description"]['min_age'] = $instance->getMinAge();
            if (!is_null($instance->getFood()) && ($instance->getFood()) ) {
                if ($instance->getFoodType()->getTitle() != $oldInstance->getFoodType()->getTitle())
                    $dataToLog["description"]['food_type'] = $instance->getFoodType()->getTitle();

            }
            if (!is_null($instance->getType()) && ($instance->getType() != $oldInstance->getType()) ) {
                if ($instance->getType() == 1)
                    $dataToLog["description"]['type'] = "bar";
                else
                    $dataToLog["description"]['type'] = "disco";

            }
            if (!is_null($instance->getSchedule()) && ($instance->getSchedule() != $oldInstance->getSchedule()) ) {
                $dataToLog["description"]['schedule'] = $instance->getSchedule();
            }
        }
        else if ($entity == "Salon") {
            if (!is_null($instance->getType()) && ($instance->getType() != $oldInstance->getType()) ) {
                if ($instance->getType() == 1)
                    $dataToLog["description"]['type'] = "salon";
                else if ($instance->getType() == 2)
                    $dataToLog["description"]['type'] = "auditorium";
                else
                    $dataToLog["description"]['type'] = "teatro";
            }
            if (!is_null($instance->getCapacity()) && ($instance->getCapacity() != $oldInstance->getCapacity()) ) {
                $dataToLog["description"]['capacity'] = $instance->getCapacity();
            }
            if (!is_null($instance->getNaturalLight()) && ($instance->getNaturalLight() != $oldInstance->getNaturalLight()) ) {
                $dataToLog["description"]['natural_light'] = $instance->getNaturalLight();
            }
            if (!is_null($instance->getSize()) && ($instance->getSize() != $oldInstance->getSize()) ) {
                $dataToLog["description"]['size'] = $instance->getSize();
            }
            if (!is_null($instance->getQuantity()) && ($instance->getQuantity() != $oldInstance->getQuantity()) )
                $dataToLog["description"]['quantity'] = $instance->getQuantity();
        }

        if (
            ($instance->getDescription() != $oldInstance->getDescription())
            && (count($instance->getDescription()) > 1)
        )
            $dataToLog["description"]['description'] = $instance->getDescription();
        if ($instance->getStatus() != $oldInstance->getStatus())
            $dataToLog["description"]['status'] = $instance->getStatus();

        /* Si existio algun cambio en la entidad entonces se guarda el registro en el log*/
        if (count($dataToLog["description"]) > 0) {
            $logsUser = new LogsUser();
            $logsUser->updateObject($dataToLog);
            $rf->get('LogsUser')->save($logsUser);
        }
    }
}