<?php
namespace Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateService;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\ServiceType;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

class UpdateServiceHandler implements Handler
{
    /**
     *  Instancia del repositoryFactory
     *
     * @var RepositoryFactoryInterface
     */
    private $rf;

    /**
     *  Instancia del Comando
     *
     * @var Command
     */
    private $command;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;
        $this->command = $command;
        $rProperty = $this->rf->get('Property');
        $property = $rProperty->findOneByArray(array('slug'=>$this->command->get('slug')));
        if (!is_null($property)) {

            $rServiceType = $this->rf->get('ServiceType');
            $serviceType = $rServiceType->getById($this->command->get('idType'));
            if (!is_null($serviceType)) {

                //si el servicio es de tipo lista
                if ( $serviceType->getType()==1 || $serviceType->getType()==9 ) {

                    return new ResponseCommandBus(400, 'Bad Request');
                } else {
                    $propertyService = $this->getPropertyService($property,$serviceType);
                    $rPropertyService = $this->rf->get('PropertyService');
                    if ($this->command->get('status')) {
                        if (!$propertyService) {
                            $propertyService = new PropertyService();
                            $propertyService->setType($serviceType);
                            $propertyService->setProperty($property);
                            $property->addService($propertyService);
                        }
                        switch($serviceType->getType()) {
                            case 0:
                                break;
                            case 4:
                                $dataSchedule = $command->get('schedule');
                                    try{
                                        $schedule = new Schedule(
                                            $dataSchedule['opening'],
                                            $dataSchedule['closing'],
                                            isset($dataSchedule['days']) ? $dataSchedule['days'] : null,
                                            isset($dataSchedule['full_time']) ? $dataSchedule['full_time'] : null
                                        );
                                        $propertyService->setSchedule($schedule);
                                    } catch (\Exception $e) {
                                        return new ResponseCommandBus(400,'Bad Request Horario incorrecto'.$e->getMessage());
                                    }
                                break;
                            case 5:
                                $free = $this->command->get('free');
                                $propertyService->setFree(isset($free) ? $free : true);
                                break;
                            case 8:
                                $quantity = $this->command->get('quantity');
                                if(!empty($quantity))
                                    $propertyService->setQuantity($quantity);
                                else
                                    return new ResponseCommandBus(400,'Bad Request cantidad requerida para el tipo de servicio');
                                break;
                            default:
                                return new ResponseCommandBus(400,'Bad Request tipo de servicio invalido para editar');
                                break;
                        }
                        $rPropertyService->save($propertyService);
                        /* Datos para el log*/
                        $serviceName = $this->command->get('name');
                        $action = "create";
                        $this->saveOrDeleteSimpleService($propertyService, $action, $serviceName, $serviceType, $property, $rf);
                        return new ResponseCommandBus(201,'OK Guardó',['id'=>$propertyService->getId()]);
                    } else {
                        if($propertyService){
                            if(
                                $serviceType->getType()==0 ||
                                $serviceType->getType()==4 ||
                                $serviceType->getType()==5 ||
                                $serviceType->getType()==8
                            ) {
                                /* Datos para el log*/
                                $serviceName = $this->command->get('name');
                                $action = "delete";
                                $this->saveOrDeleteSimpleService($propertyService, $action, $serviceName, $serviceType, $property, $rf);

                                $rPropertyService->remove($propertyService);
                                return new ResponseCommandBus(201, 'OK Eliminó');
                            } else
                                return new ResponseCommandBus(400,'Bad Request invalido para eliminar'.$serviceType->getId());
                        } else
                            return new ResponseCommandBus(400,'Bad Request no encontrado'.$serviceType->getId());
                    }
                }
            } else
                return new ResponseCommandBus(400,'Bad Request tipo de servicio no existe'.$this->command->get('type'));
        } else
            return new ResponseCommandBus(400,'Bad Request establecimiento no existe');
    }

    private function getPropertyService(Property $property,ServiceType $serviceType)
    {
        $response = null;
        foreach($property->getServices() as $service)
            if( $service->getType()->getId() == $serviceType->getId() )
                $response = $service;
        return $response;
    }

    /**
     * Funcion encargada de insertar o eliminar valores nuevos de los servicios que se estan registrando
     *
     * @param object $propertyService, servicio que se esta generando o eliminando
     * @param string $action, Accion CRUD que se esta realizando
     * @param string $serviceName
     * @param object $serviceType
     * @param $property
     * @param $rf
     *
     * @version 27/06/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function saveOrDeleteSimpleService($propertyService, $action, $serviceName, $serviceType, $property, $rf) {
        $dataToLog['action'] = $action;
        $dataToLog['resource'] = "simple service";
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        $dataToLog["description"]['name'] = $serviceName;
        $dataToLog["description"]['type'] = $serviceType->getType();

        if ($action == 'create') {
            $dataToLog['idResource'] = $propertyService->getId();
            if ($serviceType->getType() == 4)
                $dataToLog["description"]['schedule'] = $propertyService->getSchedule();
            else if ($serviceType->getType() == 5)
                $dataToLog["description"]['free'] = $propertyService->getFree();
            else if ($serviceType->getType() == 8)
                $dataToLog["description"]['quantity'] = $propertyService->getQuantity();
        }
        else if ($action == 'delete')
            $dataToLog['idResource'] = null;

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
} 