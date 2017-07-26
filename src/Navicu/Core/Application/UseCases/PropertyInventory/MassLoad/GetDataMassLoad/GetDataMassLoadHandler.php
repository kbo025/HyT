<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\GetDataMassLoad;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Class GetDataMassLoadHandler
 *
 * La siguiente clase realiza el caso de uso de obtener los datos
 * de un establecimiento para pocresarlos en carga másiva del inventario
 *
 * @package Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\GetDataMassLoad
 */
class GetDataMassLoadHandler implements Handler
{
    protected $rf;

    protected $command;

    /**
     * Ejecuta las tareas solicitadas del caso de uso "GetDataMassLoad"
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $this->command = $command;
        if (!is_null($command->get('slug'))) {
            $response = $this->getDataProperty($command->get('slug'));
            if (!empty($response)) {
                return new ResponseCommandBus(201, 'Ok', $response);
            }
        }

        return new ResponseCommandBus(400, 'Bad Request');
    }

    /**
     * Esta función permite obtiene los datos de las habitaciones y sus servicios
     * Incluyendo todas las vinculaciones
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
     *
     * @param String $slug
     * @return Array
     */
    public function getDataProperty($slug)
    {
        $rpProperty = $this->rf->get('Property');
        $response = array();

        $property = $rpProperty->findBySlug($slug);

        if ($property) {
            $rooms = $property->getRooms($property);
            if (!$this->command->isApiRequest()) { 
                $response['rateType'] = $property->getRateType();
                $response['tax'] = $property->getTax();
            }
            $response['rooms'] = array();

            //Se consulta las habitaciones
            foreach ($rooms as $currentRoom) {

                if ($currentRoom->getIsActive()) {
                    $roomType = $currentRoom->getType();
                    $roomName = $currentRoom->getName();
                    
                    $roomAux['idRoom'] = $this->command->isApiRequest() ? $currentRoom->getSlug() : $currentRoom->getId();
                    if (!$this->command->isApiRequest()) {
                        $roomAux['nameRoom'] = CoreTranslator::getTranslator($roomName);
                        $roomAux['baseAvailability'] = $currentRoom->getBaseAvailability();
                    }
                    $roomId = $currentRoom->getId();
                    //Se obtiene las vinculaciones entre habitaciones
                    //$roomAux['roomLinkages'] = $this->serviceRoomLinkage()->getByIdChild($roomId);
    
                    $roomAux['packages'] = array();
                    $packages = $currentRoom->getPackages();
    
                    //Se consulta todos los servicios de las habitación
                    foreach ($packages as $currentPack) {
    
                        $auxPack['idPack'] = $this->command->isApiRequest() ? $currentPack->getSlug() : $currentPack->getId();
                        if (!$this->command->isApiRequest()) {
                            $auxPack['namePack'] = CoreTranslator::getTranslator($currentPack->getType()->getCode());
                            $packId = $currentPack->getId();
                            $policities = $currentPack->getPackCancellationPolicies();
                            $auxPack['cancellationPolicy'] = $this->modelPolicies($policities);
                        }
                        //Se obtiene las vinculaciones entre servicios
                        //$auxPack['packLinkages'] = $this->servicePackLinkage()->getByIdChild($packId);
                        //Se obtiene las vinculaciones entre habitaciones y servicios
                        //$auxPack['roomPackLinkages'] = $this->serviceRoomPackLinkage()->getByIdPack($packId);
    
                        array_push($roomAux['packages'], $auxPack);
                    }
                    array_push($response['rooms'], $roomAux);
                }
            }
        }
        return $response;
    }

    /**
     * Esta función crea la estructura del array que envia las politicas de cancelación del servicio (pack)
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
     *
     * @param Array Object PropertyCancellationPolicy
     * @return Array
     */
    public function modelPolicies($policities)
    {
        $responsePolicies = array();

        foreach ($policities as $currentPolicy) {
            $auxPolicy = array();
            $policy = $currentPolicy->getCancellationPolicy();
            $aux['name'] = $policy->getType()->getTitle();
            $aux['variationAmount'] = $policy->getVariationAmount();
            $aux['variationType'] = $policy->getVariationType();
            array_push($responsePolicies, $aux);
        }
        return $responsePolicies;
    }
}