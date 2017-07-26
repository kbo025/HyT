<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\SetDataMassLoad;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\Core\Domain\Model\Entity\LogsOwner;

/**
 * La clase procesa los datos de la carga masiva de un establecimiento
 *
 * Class SetDataMassLoadHandler
 * @package Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\SetDataMassLoad
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 13/10/2015
 */
class SetDataMassLoadHandler implements Handler
{
    /**
     * @var atributo que maneja el repositoriofactory
     */
    private $rf;

    /**
     * @var manejador de base de datos del sistema
     */
    private $managerBD;

    private $command;

    private $idFieldRoom;

    private $idFieldPack;

    /**
     * Función set del atributo $managerBD
     * @param $managerBD
     */
    public function setManagerBD($managerBD)
    {
        $this->managerBD = $managerBD;
    }

    /**
     *  Ejecuta las tareas solicitadas del caso de uso "SetDataMassLoad"
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $this->command = $command;
        if ($command->isApiRequest()) {
            $this->idFieldRoom = 'slug';
            $this->idFieldPack = 'slug';
        } else {
            $this->idFieldRoom = 'id';
            $this->idFieldPack = 'id';
        }
        $data = $command->get('data');
        if ($this->checkData($data)) {
            if ($this->authenticationData($data, $command->getUserSession())) {
                $responseData = $this->constructData($data);

                $logsOwner = new LogsOwner;
                if ($logsOwner->saveLogsMassLoad($command, $rf)) {
                    $rf->get('LogsOwner')->save($logsOwner);
                }

                $this->persistData($responseData);
                return new ResponseCommandBus(201, 'Ok');
            }

            return new ResponseCommandBus(403, 'Forbidden');
        }
        $msg = $command->isApiRequest() ?
            'only_number_positive' :
            CoreTranslator::getTranslator('share.validation.only_number_positive');
        return new ResponseCommandBus(400, 'Bad Request',$msg);
    }

    private function checkData($data)
    {
        $response = true;

        foreach ($data['rooms'] as $currentRoom) {
            foreach ($currentRoom['packages'] as $currentData) {
                if (isset($currentData['price']) and $currentData['price'] < 1) {

                    $response = false;
                    break;
                }
            }
        }
        return $response;
    }

    /**
     * Esta función valida la pertenencia de datos con el usuario
     * retornando verdadero o falso si los datos pertenecen al usuario actual
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Array $slug
     * @return boolean
     * @version 13/10/2015
     */
    private function authenticationData(&$data, $userSession)
    {
        $authentication = true;
        $rpRoom = $this->rf->get('Room');
        $rpPack = $this->rf->get('Pack');


        if ($userSession instanceof User and (
            $userSession->hasRole('ROLE_ADMIN') or
            CoreSession::havePermissons('extranet_inventory', 'update') or
            $userSession->hasRole('ROLE_SALES_EXEC')))
            return true;
        else if(isset($data['rooms']) and ($data['rooms'] != null)) {
            foreach ($data['rooms'] as &$currentRoom) {
                //Se validan las habitaciones
                if (!is_null($currentRoom)) {
                    $room = $rpRoom->findOneBy(array($this->idFieldRoom => $currentRoom['idRoom']));
                    $flag = false;
                    if(!is_null($room)) {
                        $currentRoom['idRoom'] = $room->getId(); 
                        foreach ($room->getProperty()->getOwnersProfiles() as $currentOwner) {
                            if ($currentOwner == $userSession) {
                                $flag = true;
                                break;
                            }
                        }
                    }
                    
                    if(!$authentication or !$flag) {
                        $authentication = false;
                        break;
                    }
                }

                if(isset($currentRoom['packages']) and ($currentRoom['packages'] != null)) {
                    foreach ($currentRoom['packages'] as &$currentPack) {
                        //Se validan las vinculaciones los servicios
                        $pack = $rpPack->findOneBy(array($this->idFieldPack => $currentPack['idPack']));
                        $flag = false;
                        if(!is_null($pack)) {
                            $currentPack['idPack'] = $pack->getId();
                            $idRoom = $pack->getRoom()->getId();
                            foreach ($pack->getRoom()->getProperty()->getOwnersProfiles() as $currentOwner) {
                                if ($currentOwner == $userSession) {
                                    $flag = true;
                                    break;
                                }
                            }
                        }

                        if(!$authentication or
                            !$flag or
                            $currentRoom['idRoom'] != $idRoom) {

                            $authentication = false;
                            break;
                        }
                    }
                }
            }
        } else
            $authentication = false;

        return $authentication;
    }

    /**
     * La función procesa los datos enviados en el Json de la Carga Másiva
     * realizando un modelado de los datos según las vinculaciones en las distintas fechas cargadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param Array $data
     * @return Array $respnseData
     * @version 13/10/2015
     */
    private function constructData($data)
    {
        $responseData = array('dependencyData' => array(), 'processData' => array());

        //Se procesan los datos por el conjunto de fechas
        foreach ($data['dates'] as $currentDate) {
            $auxResponse['date'] = $currentDate;
            $flagError = true;
            //Se inicializa $vectorData, con los dailyPack/DailyRoom de la fecha actual
            $vectorData = $this->constructDataVector($data['rooms'], $currentDate, $flagError);

            if (!$flagError)
                break;

            $auxResponse['data'] = $vectorData;
            array_push($responseData['processData'], $auxResponse);
        }
        if (!$flagError)
            return false;

        return $responseData;
    }

    /**
     * La función construye un arreglo, el cual es la estructura base
     * para procesar los datos para la carga másiva
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param Array $data
     * @param Data $date
     * @param boolean $flagError
     * @return Array $vector
     */
    private function constructDataVector($data, $date, &$flagError)
    {
        $vector = array();
        $rpDailyRoom = $this->rf->get('DailyRoom');
        $rpDailyPack = $this->rf->get('DailyPack');
        $rpRoom = $this->rf->get('Room');
        $rpPack = $this->rf->get('Pack');

        foreach ($data as $currentRoom) {

            $maxAvailability = 0;

            if (!is_null($currentRoom['data'])) {

                $aux = array('check' => false);
                $aux['type'] = 'room';
                $aux['id'] = $currentRoom['idRoom'];
                $aux['linkage'] = null;

                $dataDailyRoom = $rpDailyRoom->findOneByDateRoomId(
                    $currentRoom['idRoom'],
                    $date);

                if (!$dataDailyRoom) {
                    $room = $rpRoom->findOneBy(array('id' => $currentRoom['idRoom'] ));
                    $dataDailyRoom = new DailyRoom();
                    $dataDailyRoom->setDate(new \DateTime($date));
                    $dataDailyRoom->setRoom($room);
                }

                $currentRoom['data']['idRoom'] = $currentRoom['idRoom'];

                $dataDailyRoom->updateObject($currentRoom['data']);

                if (is_null($dataDailyRoom->getMinNight()) and !is_null($dataDailyRoom->getAvailability()))
                    $dataDailyRoom->setMinNight(1);

                if (is_null($dataDailyRoom->getMaxNight()) and !is_null($dataDailyRoom->getAvailability()))
                    $dataDailyRoom->setMaxNight(365);

                $aux['dataDaily'] = $dataDailyRoom;
                array_push($vector, $aux);

            } else
                $dataDailyRoom = $rpDailyRoom->findOneByDateRoomId(
                    $currentRoom['idRoom'],
                    $date);


            if (is_null($dataDailyRoom))
                $dataDailyRoom = false;
            else
                $maxAvailability = $dataDailyRoom->getAvailability();

            //La variable contará la disponibilidad de los packs
            $counterPackAvailability = 0;

            $flagAvailability = false;

            if (!is_null($currentRoom['packages'])) {
                //Se consultas los DailyPack asociados al DailyRoom Actual
                $dailyPackages = $rpDailyPack
                    ->findPackagesByRoomIdDate($currentRoom['idRoom'], $date);

                foreach ($currentRoom['packages'] as $currentPack) {
                    $aux2 = array('check' => false);
                    $aux2['type'] = 'pack';
                    $aux2['id'] = $currentPack['idPack'];
                    $aux2['linkage'] = null;

                    $dataDailyPack = $rpDailyPack->findOneByPackIdDate(
                        $currentPack['idPack'], $date);

                    //Si existe el DailyPack en la BD
                    if ($dataDailyPack) {

                        //Se debe buscar si el dailyPack se encuentra en el conjunto de dailys perteneciente a la dailyRoom
                        $key = array_search($dataDailyPack ,$dailyPackages);

                        //Debe eliminarse ese DailyPack
                        unset($dailyPackages[$key]);

                    } else {

                        //Si no existe Servicio se crea uno nuevo
                        $dataDailyPack = new DailyPack();
                        //Se agrega al dailyPack Nuevo la fecha actual
                        $dataDailyPack->setDate(new \DateTime($date));
                        $pack = $rpPack->findOneBy(array('id' => $currentPack['idPack'] ));
                        $dataDailyPack->setPack($pack);
                    }

                    if (!isset($currentPack['specificAvailability']))
                        $flagAvailability = true;

                    /*if (isset($currentPack['specificAvailability']) and
                        is_null($currentPack['specificAvailability']) and
                        isset($currentRoom['data']['availability']))
                        $currentPack['specificAvailability'] = $currentRoom['data']['availability'];*/

                    $dataDailyPack->updateObject($currentPack);

                    if ($dataDailyRoom)
                        $this->updateDailyPack($dataDailyPack, $dataDailyRoom, true);

                    //if (!is_null($dataDailyPack->getSpecificAvailability()))
                      //  $counterPackAvailability = $counterPackAvailability + $dataDailyPack->getSpecificAvailability();

                    $aux2['dataDaily'] = $dataDailyPack;
                    array_push($vector, $aux2);
                }

                if ($dataDailyRoom) {
                    foreach ($dailyPackages as $currentValue) {

                        if ($flagAvailability or !isset($currentRoom['packages']) or !$currentRoom['packages'])
                            $currentValue->setSpecificAvailability($dataDailyRoom->getAvailability());

                        //Se valida si el dailyPack sus valores sobre pasan al DailyRoom
                        $updatedDailyPack = $this->updateDailyPack($currentValue, $dataDailyRoom, false);

                        if (!is_null($currentValue->getSpecificAvailability()))
                            $counterPackAvailability = $counterPackAvailability + $currentValue->getSpecificAvailability();

                        if ($updatedDailyPack) {
                            //Se deben agregar al conjunto de datos que se deben actualizar

                            $aux3 = array('check' => false);
                            $aux3['type'] = 'pack';
                            $aux3['id'] = $currentValue->getId()    ;
                            $aux3['linkage'] = null;
                            $aux3['dataDaily'] = $currentValue;
                            array_push($vector, $aux3);
                        }
                    }
                }
            }
        }

        return $vector;
    }

    /**
     * La función actualiza los datos de DailyPack respecto de DailyRoom
     * cuando los datos del DailyPack sobrepasan al DailyRoom, además retorna
     * booleano si los datos del DailyPack son modificados
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param Object DailyPack $dailyPack
     * @param Object DailyRoom $dailyRoom
     * @return Boolean $flag
     */
    private function updateDailyPack(&$dailyPack, $dailyRoom)
    {
        $minNightPack = $dailyPack->getMinNight($dailyPack);
        $minNightRoom = $dailyRoom->getMinNight($dailyRoom);

        $maxNightPack = $dailyPack->getMaxNight($dailyPack);
        $maxNightRoom = $dailyRoom->getMaxNight($dailyRoom);

        $availabilityPack = $dailyPack->getSpecificAvailability($dailyPack);
        $availabilityRoom = $dailyRoom->getAvailability($dailyRoom);

        $flag = false;

        if ($minNightPack < $minNightRoom) {
            $minNightPack = $minNightRoom;
            $flag = true;
        }

        if ($maxNightPack > $maxNightRoom) {
            $maxNightPack = $maxNightRoom;
            $flag = true;
        }


        if (is_null($availabilityPack))
            $dailyPack->setSpecificAvailability($availabilityRoom);

        //Si hubo inconsistencia del DailyPack respecto al DailyRoom
        if ($flag) {
            if ($minNightPack > $maxNightPack) {
                $dailyPack->setMinNight($minNightRoom);
                $dailyPack->setMaxNight($maxNightRoom);
            } else {
                $dailyPack->setMinNight($minNightPack);
                $dailyPack->setMaxNight($maxNightPack);
            }
        }

        if (is_null($dailyPack->getCloseOut()))
            $dailyPack->setCloseOut(false);

        if (is_null($dailyPack->getClosedToArrival()))
            $dailyPack->setClosedToArrival(false);

        if (is_null($dailyPack->getClosedToDeparture()))
            $dailyPack->setClosedToDeparture(false);

        return $flag;
    }

    /**
     * La funcion se encarga de persistir todos los datos de la carga másiva.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param Array $data
     * @return Void
     *
     */
    public function persistData($data)
    {
        $dependencyData = $data['dependencyData'];
        $dataUser = $data['processData'];
        foreach ($dependencyData as $currentTree) {
            foreach ($currentTree as $currentValue) {                
                $this->managerBD->persist($currentValue['dataDaily']);
            }
        }

        foreach ($dataUser as $currentDate) {
            foreach ($currentDate['data'] as $currentValue) {
                $this->managerBD->persist($currentValue['dataDaily']);
            }
        }

        $this->managerBD->save();
    }
}