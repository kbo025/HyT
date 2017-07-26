<?php

namespace Navicu\Core\Application\UseCases\Web\FormatFlightData;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class FormatFlightDataHandler implements Handler {

	private $command;

	private $service;

	private $rf;

	/**
	 *  Ejecuta las tareas solicitadas
	 *
	 * @param Command $command
	 * @param RepositoryFactoryInterface $rf
	 *
	 * @return ResponseCommandBus
	 */
	public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
		$this->command = $command;

		$searchData = $command->get('search');
		$this->service = $command->get('flightService');
		$this->session = $command->get('sessionService');
		$errors = $this->getErrors($searchData);
		if ( !$errors ) {
			$responseService = [
				'httpCode' => null,
				'message' => null,
				'data' => [
					'oneWay' => [],
					'return' => [],
				]
			];
			if(!$searchData['calendar'])
				$responseService = $this->service->getAvailabilityFlightItinerary($searchData);
			if ($searchData['calendar'] || $responseService['httpCode'] == 200 || $responseService['httpCode'] == 201) {
				if ($searchData['roundTrip']) {
					if ($searchData['calendar'] || (empty($responseService['data']['oneWay']) || empty($responseService['data']['return']))) {
						$response = $this->formatResponseSheduled($responseService,$searchData);
					} else {
						$response = $this->formatResponseItinerary($responseService,$searchData);
					}
				} else {
					if ($searchData['calendar'] || empty($responseService['data']['oneWay'])) {
						$response = $this->formatResponseSheduled($responseService,$searchData);
					} else {
						$response = $this->formatResponseItinerary($responseService,$searchData);
					}
				}
				$response = array_merge(
					$response,
					[
						'search' => $searchData,
						'isoCurrency' => $this->session->get('alphaCurrency'),
					]
				);
				return new ResponseCommandBus(
					$responseService['httpCode'],
					$responseService['message'],
					$response
				);
			}
			return new ResponseCommandBus($responseService['httpCode'],$responseService['message'],$responseService['data']);
		}
		return new ResponseCommandBus(400,'Bad Request',$errors);
	}

	private function getErrors($data)
	{
		$errors = [];

		if ( empty($data['from']) || !is_string($data['from']) || strlen($data['from']) != 3 )
			$errors[] = 'invalid_from';

		if ( empty($data['to']) || !is_string($data['to']) || strlen($data['to']) != 3 )
			$errors[] = 'invalid_to';

		if ( empty($data['adult']) || !is_integer($data['adult']) || $data['adult'] <= 0 )
			$errors[] = 'invalid_adult';

		if (!is_integer($data['kid']) || $data['kid'] < 0 )
			$errors[] = 'invalid_kid';

		if ( empty($data['startDate']) || !is_string($data['startDate']) || !$this->validateDate($data['startDate'],!$data['calendar']) )
			$errors[] = 'invalid_startDate';

		if( !empty($data['roundTrip']) )
			if ( empty($data['endDate']) || !is_string($data['endDate']) || !$this->validateDate($data['endDate'],!$data['calendar']) )
				$errors[] = 'invalid_endDate';

		return empty($errors) ? false : $errors;
	}

	private function formatResponseSheduled($data,$search)
	{
		$range = $this->getRangeDate($search);
		$response = [
			'sheduled' => $range,
		];
		if (empty($data['data']['oneWay'])) {
			$search['startDate'] = $range['startDate'];
			$search['endDate'] = $range['endDate'];
			$responseService = $this->service->getAvailabilityFlightItineraryByRangeDate($search);
			if (!empty($responseService['data']['oneWay'])) {
				foreach ($responseService['data']['oneWay'] as $flight) {
					$flight['price'] = (float)$flight['price'];
					if (is_null($range['dates'][$flight['date']]['lowestPrice']) || ($range['dates'][$flight['date']]['lowestPrice'] > $flight['price']))
						$range['dates'][$flight['date']]['lowestPrice'] = $flight['price'];
					$range['dates'][$flight['date']]['flights'][] = $this->formatDataFlight($flight);
				}
			}
			$newDates = [];
			foreach ($range['dates'] as $newDate) {
				$newDates[] = $newDate;
			}
			$response['sheduled']['dates'] = $newDates;
		}
		return $response;
	}

    private function getRangeDate($data)
    {
		$response = ['dates' => []];

        $now = new \DateTime('now');
		if($data['calendar']) {
			$beinDate = new \DateTime($data['startDate']);
        	$endDate = new \DateTime($data['startDate']);
			$endDate->modify('+6 day');
		} else {
			if($data['direction']) {
				$beinDate = new \DateTime($data['endDate']);
        		$endDate = new \DateTime($data['endDate']);
			} else {
				$beinDate = new \DateTime($data['startDate']);
        		$endDate = new \DateTime($data['startDate']);
			}
			$beinDate->modify('-3 day');
			$endDate->modify('+3 day');
		}
        if ($beinDate < $now) {
            $diffDays = $now->diff($beinDate)->d;
            $beindDate = $now;
            $endDate->modify('+'.$diffDays.' day');
        }

		$currentDate = $beinDate;
		$response['startDate'] = $currentDate->format('d-m-Y');
		while ($currentDate <= $endDate) {
			$response['dates'][$currentDate->format('d-m-Y')] = [
				'lowestPrice' => null,
				'day' => $currentDate->format('d'),
				'month' => $currentDate->format('M'),
				'date' => $currentDate->format('d-m-Y'),
				'flights' => []
			];
			$response['endDate'] = $currentDate->format('d-m-Y');
			$currentDate->modify('+1 day');
		}

        return $response;
    }

	private function formatResponseItinerary($data,$search)
	{
		$response = [];
		if(!empty($data['data']['oneWay']))
			$response['oneWay'] = [];
		foreach ($data['data']['oneWay'] as $flight) {
			$flight['price'] = (float)$flight['price'];
			$response['oneWay'][] = $this->formatDataFlight($flight);
		}
		if (!empty($search['roundTrip'])) {
			if(!empty($data['data']['return']))
				$response['return'] = [];
			foreach ($data['data']['return'] as $flight) {
				$flight['price'] = (float)$flight['price'];
				$response['return'][] = $this->formatDataFlight($flight);
			}
		}
		return $response;
	}

	private function validateDate($date, $todayValidate = true)
	{
		$valores = explode('-', $date);
		$validate = (count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2]));

		if ($validate) {
			$date = new \DateTime($date);
			$now = new \DateTime(date('d-m-Y'));

			$validate = $validate && ($todayValidate && ($date >= $now));
		}

		return $validate;
	}

	private function formatDataFlight(&$data)
	{
		$departure = new \DateTime($data['departure']);
		$arrival = new \DateTime($data['arrival']);
		$data['arrivalDate'] = $arrival->format('l j F');
		$data['arrivalTime'] = $arrival->format('g:i a');
		$data['departureDate'] = $departure->format('l j F');
		$data['departureTime'] = $departure->format('g:i a');
		$data['duration'] = $this->formaterTime($data['duration']);
        return $data;
	}

	private function formaterTime($time)
	{
		$valores = explode(':', $time);
		$response = '';

		if ($valores[0] != '00')
			$response = trim($valores[0].'H ','0');

		if ($valores[1] != '00')
			$response = trim($valores[1].'M ','0');

		if ($valores[2] != '00')
			$response = trim($valores[1].'S ','0');

		return trim($response);
	}
}
