<?php
namespace Navicu\Core\Application\UseCases\Extranet\FindRoomWithoutAvailability;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Clase para ejecutar el caso de uso FindRoomWithoutAvailability
 * @author Jose Agraz <jaagraz@navicu.com>
 * @version 08/01/2016
 */
class FindRoomWithoutAvailabilityHandler implements Handler
{
    private $command;

    private $rf;
    /**
     *
     * @param Command $command Objeto Command contenedor
     *                                             de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     *
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->command = $command;
        $this->rf = $rf;

        $rpPropertyRepository  = $this->rf->get('Property');

        //Busco el establecimiento
        $property = $rpPropertyRepository->findBySlug(
            array('slug' => $this->command->getSlug())
        );

        //Si existe el establecimiento
        if ($property) {

            $response = []; //respuesta
            $response['nameProperty'] = $property->getName();
            $response['inventory'] = [];
            $startDate = new \DateTime(); // fecha actual
            $endDate = new \DateTime();
            $endDate = $endDate->modify('+1 month'); // fecha mes siguiente

            $arrayDayMonth = []; //arary de fechas 30 dias
            $dayMonth = new \DateTime(); //armando el array de fechas 30 dias
            for ($i = 0; $i < 30; $i++) {
                array_push($arrayDayMonth, $dayMonth->format('d-m-Y'));
                $dayMonth->modify('+1 day');
            }

            $rooms = $property->getRooms();
            $aux = [];
            foreach($rooms as $room){
                $dailypacks = $rf->get('DailyPack')->findByRoomDateRange($room->getId(), $startDate, $endDate);
                $dailyRooms = $rf->get('DailyRoom')->findByDatesRoomId($room->getId(), $startDate, $endDate);
                foreach ($arrayDayMonth as $currentDate) {
                    $currentDailyRoom = $this->getCurrentDailyRoom($currentDate,$dailyRooms);
                    if (is_null($currentDailyRoom) or ($currentDailyRoom->getAvailability() < 1) or ($currentDailyRoom->getIsCompleted() < 1)) {
                        if(!isset($aux[$currentDate]))
                            $aux[$currentDate] = [];
                        $aux[$currentDate][] = $room->getName();
                    } else {
                       $currentDailyPacks = $this->getCurrentDailyPacks($currentDate,$dailypacks);
                        $completed = false;
                        foreach ($currentDailyPacks as $cdp) {
                            $completed = $cdp->getIsCompleted() and ($cdp->getSpecificAvailability()>0);

                        }
                        if (!$completed) {
                            if(!isset($aux[$currentDate]))
                                $aux[$currentDate] = [];
                            $aux[$currentDate][] = $room->getName();

                        }
                    }
                }
            }
            $auxResponse = [];

            foreach($aux as $date => $current)
            {

                $auxResponse[] = [
                    'date' => $date,
                    'rooms' => $current
                ];
            }
                $response['inventory'] = $auxResponse;
                return new ResponseCommandBus(201, 'Ok', $response);
        } else {
            return new ResponseCommandBus(404,'Not Found');
        }
    }

    private function getCurrentDailyRoom($currentDate,$dailyRooms)
    {
        $response = null;
        foreach ($dailyRooms as $cdr) {
            if ($cdr->getDate()->format('d-m-Y') == $currentDate) {
                $response = $cdr;
                break;
            }
        }
        return $response;
    }

    private function getCurrentDailyPacks($currentDate,$dailyPacks)
    {
        $response = [];
        foreach ($dailyPacks as $cdp) {
            if ($cdp->getDate()->format('d-m-Y') == $currentDate) {
                $response[] = $cdp;
            }
        }
        return $response;
    }
}




