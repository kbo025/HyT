<?php
/**
 * Created by Isabel Nieto.
 * User: user03
 * Date: 29/04/16
 * Time: 04:01 PM
 */
namespace Navicu\Core\Application\UseCases\Admin\DropDailyFromRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\DropDaily;

class DropDailyFromRoomHandler implements Handler {

    private $managerBD;

    /**
     * Utilizado para realizar un salvado o borrado masivo al instanciar dicha variable
     *
     * @param $managerBD
     */
    public function setManagerBD($managerBD)
    {
        $this->managerBD = $managerBD;
    }

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 05-05-2016
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf) {
        $request = $command->getRequest();

        $today = new \DateTime();
        $today = $today->format('Y-m-d');

        if ($this->searchDateAvailable($request,$today)) {
            $this->dropDaily($request, $rf, $today);
            return new ResponseCommandBus(200, 'Ok', "Success");
        }
        return new ResponseCommandBus(400, 'Bad Request', "A problem occurred with the given dates, check and try again");
    }

    /**
     * Busca por room_id los dailyRoom que apliquen en cada fecha dada a partir de hoy
     *
     * @param object $request coleccion de datos que contiene dates{}, packages[idPacks] y rooms[idRooms]
     * @param $rf
     * @param string $today fecha del dia actual que se hace la solicitud
     * @return array coleccion de dailyRooms
     */
    public function getDailyRooms($request, $rf, $today) {
        $rpDailyRoom = $rf->get('DailyRoom');

        $response = $rpDailyRoom->findDailyRoomsGivenGroupOfRoomId($request['rooms'], $request['dates'], $today);

        return $response;
    }

    /**
     * Busca por idPacks los dailyPacks asociados a esa habitacion en cada fecha dada a partir de hoy
     *
     * @param object $request coleccion de datos que contiene dates{}, packages[idPacks] y rooms[idRooms]
     * @param object $rf repositorio
     * @param string $today fecha del dia actual que se hace la solicitud
     */
    public function getDailyPacksGivenTheRoomsId($request, $rf, $today) {
        $rpDailyPack = $rf->get('DailyPack');
        
        $response = $rpDailyPack->findDailyPacksGivenGroupOfRoomId($request['rooms'], $request['dates'], $today);

        return $response;
    }

    /**
     * Busca los idPacks en la entidad dailyPack dado un conjunto de fechas a partir de hoy
     *
     * @param object $request coleccion de datos que contiene dates{}, packages[idPacks] y rooms[idRooms]
     * @param $rf
     * @param string $today fecha del dia actual que se hace la solicitud
     * @return mixed
     */
    public function getDailyPacksGivenThePacksId($request, $rf, $today)
    {
        $rpDailyPack = $rf->get('DailyPack');

        $response = $rpDailyPack->findDailyPacksGivenGroupOfPackId($request['packages'], $request['dates'], $today);

        return $response;
    }

    /**
     * Funcion para eliminar los dailys dado un conjunto de fechas en especifico
     *
     * @param object $request valores a consultar
     * @param object $rf
     * @param string $today fecha actual de la consulta
     * @return string
     */
    public function dropDaily($request, $rf, $today) {
        $dailyRooms = $this->getDailyRooms($request, $rf, $today);
        $dailyPacksOfRooms = $this->getDailyPacksGivenTheRoomsId($request, $rf, $today);
        $dailyPacksOfPacks = $this->getDailyPacksGivenThePacksId($request, $rf, $today);

        foreach ($dailyRooms as $dailyRoom) {
            $this->managerBD->delete($dailyRoom);
            $this->setDropDaily($dailyRoom->getId(), "dailyRoom", $dailyRoom->getRoom()->getId(), $rf);
        }

        foreach ($dailyPacksOfRooms as $dailyPack) {
            $this->managerBD->delete($dailyPack);
            $this->setDropDaily($dailyRoom->getId(), "dailyPack", $dailyPack->getPack()->getId(), $rf);
        }

        foreach ($dailyPacksOfPacks as $dailyPack) {
            $this->managerBD->delete($dailyPack);
            $this->setDropDaily($dailyRoom->getId(), "dailyPack", $dailyPack->getPack()->getId(), $rf);
        }

        $this->managerBD->save();
    }

    /**
     * Funcion usada para almacenar los id de un daily
     * dentro de la entidad DropDaily.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Integer $id
     * @param Integer $type
     * @param Integer $idParent
     * @param RepositoryFactoryInterface $rf
     */
    public function setDropDaily($id, $type, $idParent, $rf)
    {
        $dropDaily = new DropDaily();
        $dropDaily->setDailyId($id);
        $dropDaily->setType($type);
        $dropDaily->setParentId($idParent);

        $rf->get('DropDaily')->persistObject($dropDaily);

    }
    /**
     * Funcion para ordenar y determinar si se ejecutara la eliminacion de los dailys
     *
     * @param object $request valores a consultar
     * @param string $today fecha actual de la consulta
     * @return int si no se encontro ninguna fecha menor a la actual se prosigue
     */
    public function searchDateAvailable($request, $today) {
        $dates = $request['dates'];
        $continue = 1;
        $length = count($dates);

        /* Ordenamos de menor a mayor las fechas */
        usort($dates, function($a, $b) {
            if ($a == $b)
                return 0;
            return ($a < $b) ? -1 : 1;
        });

        /* Comprobamos que las fechas no sean menor a la de hoy */
        for ($ii = 0; (($ii < $length) && ($continue)); $ii++) {
            if ($dates[$ii] < $today) {
                $continue = 0;
            }
        }
        return $continue;
    }
}