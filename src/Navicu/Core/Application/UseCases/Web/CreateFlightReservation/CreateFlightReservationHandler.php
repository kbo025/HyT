<?php

namespace Navicu\Core\Application\UseCases\Web\CreateFlightReservation;


use Herrera\Json\Exception\Exception;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Flight;
use Navicu\Core\Domain\Model\Entity\FlightReservation;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class CreateFlightReservationHandler implements Handler {

	public function handle(Command $command, RepositoryFactoryInterface $rf)
	{
		$flights = $command->get('flights');
		$passengers = $command->get('passengers');
		$flightReservationRepo = $rf->get('FlightReservation');
		$reservation = new FlightReservation();
		$price = 0;
		foreach ($flights as $flight) {
			$price = $price + $flight['price'];
			$reservation->addFlight($this->createFlightFromData($flight,$command->get('currency')));
		}

		$reservation
			->setCurrency($command->get('currency'))
			->setTotalToPay($price)
			->setReservationDate(new \DateTime('now'))
			->setChildNumber($passengers['kids'])
			->setAdultNumber($passengers['adults'])
			->setTax(0.12);

		try {
			$flightReservationRepo->save($reservation);
			$request = $command->getRequest();
			$request['public_id'] = $reservation->getPublicId();
			return new ResponseCommandBus(201, 'OK', $request);
		} catch (\Exception $e) {

			return new ResponseCommandBus(
				400,
				'Bad Request',
				[
					'errorStatus' => 1,
					'internal' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
				]
			);
		}
	}

	private function createFlightFromData($flightData, $currency, $returnFlight = false)
	{
		$flight = new Flight();
		$flight
				->setNumber($flightData['number_flight'])
				->setAirlineCode($flightData['airline'])
				->setDepartureTime(\DateTime::createFromFormat('Y-m-d H:i:s', $flightData['departure']))
				->setOrigin($flightData['from'])
				->setDestination($flightData['to'])
				->setDuration($flightData['duration'])
                ->setReturnFlight($returnFlight)
				->setPrice($flightData['price'])
				->setCurrency($currency);

		return $flight;
	}

}
