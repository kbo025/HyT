<?php
namespace Navicu\Core\Application\UseCases\Admin\UpdateServices\DeleteServiceInstance;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

class DeleteServiceInstanceHandler implements Handler
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
        if (!is_null($property)) {
            $rServiceType = $this->rf->get('ServiceType');
            $serviceType = $rServiceType->getById($this->command->get('idType'));
            if (!is_null($serviceType)) {

                if ( $serviceType->isRestaurant() ) {
                    $entity = 'Restaurant';
                    $getArray = 'getRestaurants';
                } elseif ( $serviceType->isBar() ) {
                    $entity = 'Bar';
                    $getArray = 'getBars';
                } elseif ( $serviceType->isSalon() ) {
                    $entity = 'Salon';
                    $getArray = 'getSalons';
                } else {
                    return new ResponseCommandBus(400,'Bad Request tipo de servicio Incorrecto');
                }

                $rInstance = $this->rf->get($entity);
                $instance = $rInstance->getById($this->command->get('id'));

                if(!is_null($instance)) {
                    $propertyService = $instance->getService();
                    $instanceProperty = $propertyService->getProperty();
                    if($instanceProperty->getId() != $property->getId())
                        return new ResponseCommandBus(400,'Bad Request Establecimiento no coinciede');
                    $rInstance->remove($instance);
                    /* Datos para el log*/
                    $action = "delete";
                    $this->saveDeleteServiceInstanceToLog($instance, $entity." service", $action, $property, $this->rf);
                    $arrayInstance = $propertyService->$getArray();
                    if (empty($arrayInstance)) {
                        $rPropertyService = $this->rf->get('PropertyService');
                        $rPropertyService->remove($propertyService);
                    }
                    return new ResponseCommandBus(201, 'OK Eliminó');
                } else {
                    return new ResponseCommandBus(400,'Bad Request Restaurant no existe');
                }
            } else {
                return new ResponseCommandBus(400,'Bad Request tipo de servicio no existe');
            }
        } else {
            return new ResponseCommandBus(400,'Bad Request establecimiento no existe');
        }
    }

    /**
     * Funcion encargada de guardar cuando un servicio es eliminado
     *
     * @param object $instance, tipo de servicio a eliminar que se quiere guardar en la base de datos de log
     * @param string $resource, definicion sobre la entidad que esta sufriendo el cambio
     * @param string $action, accion CRUD que se esta llevando a cabo
     * @param $property
     * @param $rf
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 22/06/2016
     */
    public function saveDeleteServiceInstanceToLog($instance, $resource, $action, $property, $rf) {
        $dataToLog['action'] = $action;
        $dataToLog['resource'] = $resource;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        $dataToLog['idResource'] = null;
        $dataToLog["description"]['name'] = $instance->getName();

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
}