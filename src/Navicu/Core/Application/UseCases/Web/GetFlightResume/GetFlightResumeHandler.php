<?php

namespace Navicu\Core\Application\UseCases\Web\GetFlightResume;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetFlightResumeHandler implements Handler
{

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $publicId = $command->get('publicId');

        $flightReservationRepo = $rf->get('FlightReservation');
        $flightRepo = $rf->get('Flight');

        $reservation = $flightReservationRepo->findByPublicId($publicId);


        $flight = $flightRepo->findByReservationAndDirection($reservation->getId(), false);
        $returnflight = $flightRepo->findByReservationAndDirection($reservation->getId(), true);

        //die(var_dump($flight));

        $response = $this->processStructure($flight, $returnflight, $reservation);

        return new ResponseCommandBus(200, 'Ok', $response);
    }

    private function processStructure($flight, $returnFlight, $reservation) {

        $structure = array();

        $flight = array();
        $flight['date'] = '23 Mayo';
        $flight['departureTime'] = '08:50 AM';
        $flight['origin'] = 'CSS Cararas';
        $flight['originName'] = 'Aeropuerto Int. de Maiquetía';
        $flight['number'] = 345;
        $flight['departureDate'] = '2017-06-23 14:30:00';
        $flight['airlineCode'] = 'LASER';
        $flight['duration'] = 60;

        $flight['arrivalTime'] = '10:30 AM';
        $flight['destination'] = 'VLN Valencia';
        $flight['destinationName'] = 'Aeropuerto Int. Arturo Michelena';

        $returnFlight = array();
        $returnFlight['date'] = '31 Mayo';
        $returnFlight['departureTime'] = '05:50 PM';
        $returnFlight['origin'] = 'VLN Valencia';
        $returnFlight['originName'] = 'Aeropuerto Int. Arturo Michelena';
        $returnFlight['number'] = 754;
        $returnFlight['departureDate'] = '2017-06-23 14:30:00';
        $returnFlight['airlineCode'] = 'Venezolana';
        $returnFlight['duration'] = 60;

        $returnFlight['arrivalTime'] = '08:30 PM';
        $returnFlight['destination'] = 'CSS Cararas';
        $returnFlight['destinationName'] = 'Aeropuerto Int. de Maiquetía';

        $structure['flight'] = $flight;
        $structure['returnFlight'] = $returnFlight;
        $structure['passengers'] = array('adults' => 2, 'children' => 1, 'total' => 3);
        $structure['cancelationPolicy'] = 'No Reembolsable';
        $structure['subTotal'] = 108000;
        $structure['tax'] = 7000;
        $structure['totalToPay'] = 115000;

        return $structure;
    }
}