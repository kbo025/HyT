<?php

namespace Navicu\Core\Application\UseCases\Web\BookFlights;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\Passenger;

class BookFlightsHandler implements Handler {


	/**
	 *  Ejecuta las tareas solicitadas
	 *
	 * @param Command $command
	 * @param RepositoryFactoryInterface $rf
	 *
	 * @return ResponseCommandBus
	 */
	public function handle( Command $command, RepositoryFactoryInterface $rf ) {

		$request = $command->getRequest();
		$flightReservationRepo = $rf->get('FlightReservation');
		try {
			$reservation = $flightReservationRepo->findByPublicId($request['passengers']['publicId']);

			$passengers = $request['passengers']['passengers'];

			foreach ($passengers as $currentPassenger) {
				$reservation->addPassenger($this->createPassengerFromData($currentPassenger));
			}
			$reservation->setCode($request['reservationCode']);

				$flightReservationRepo->save($reservation);
				return new ResponseCommandBus(201, 'OK');
		}catch (\Exception $e) {

			return new ResponseCommandBus(
				400,
				'Bad Request',
				[
					'errorStatus' => 1,
					'internal' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
				]);
		}

	}

	private function createPassengerFromData($passengerData) {

		$passenger = new Passenger();
		$passengerNames = explode(' ', $passengerData['fullName']);
		$passenger
					->setName($passengerNames[0])
					->setLastname($passengerNames[1])
					->setDocumentType($passengerData['docType'])
					->setDocumentNumber($passengerData['documentNumber'])
					->setEmail($passengerData['email'])
					->setPhone($passengerData['phone']);

		return $passenger;

	}
}