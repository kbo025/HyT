<?php

namespace Navicu\InfrastructureBundle\Controller\Web;


use Navicu\Core\Application\UseCases\Web\BookFlights\BookFlightsCommand;
use Navicu\Core\Application\UseCases\Web\FormatFlightData\FormatFlightDataCommand;
use Navicu\Core\Application\UseCases\Web\CreateFlightReservation\CreateFlightReservationCommand;
use Navicu\Core\Application\UseCases\Web\GetFlightResume\GetFlightResumeCommand;
use Navicu\Core\Application\UseCases\Web\PayFlightReservation\PayFlightReservationCommand;
use Navicu\Core\Application\UseCases\Web\AutocompleteListForFlight\AutocompleteListForFlightCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FlightReservationController extends Controller
{

    /**
    * Autocompletado para la bsuqueda de aeropuertos
    */
    public function autocompletedAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);
            if(!isset($data['country']))
                $data['country'] = 'VE';
            $command = new AutocompleteListForFlightCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        } else {
            return new Response('Not Found',400);
        }
    }

    /**
    * listado de itinerarios de vuelo
    */
    public function flightItineraryListAction(Request $request, $from, $to)
    {
		if(!empty($request->query->get("calendar"))) {
			$startDate = $request->query->get("startDate");
			$prevDate = new \DateTime($startDate);
			$nextDate = new \DateTime($startDate);
			$prevDate->modify('-7 days');
			$nextDate->modify('+7 days');
		} else {
			if (!empty($request->query->get('roundTrip')) && !empty($request->query->get('direction')) && !empty($request->query->get('endDate'))) {
				$startDate = $request->query->get("endDate");
			} else {
				$startDate = $request->query->get("startDate");
			}
			$prevDate = new \DateTime($startDate);
			$nextDate = new \DateTime($startDate);
			$prevDate->modify('-10 days');
			$nextDate->modify('+10 days');
		}

        $data = [
            "startDate" => $request->query->get("startDate"),
            "endDate" => $request->query->get("endDate"),
            "adult" => empty($request->query->get('adult')) ? 1 : (integer)$request->query->get("adult"),
            "kid" => empty($request->query->get('kid')) ? 0 : (integer)$request->query->get("kid"),
            "roundTrip" => !empty($request->query->get('roundTrip')),
            "calendar" => !empty($request->query->get("calendar")),
            "direction" => !empty($request->query->get('direction')),
            "from" => $from,
            "to" => $to,
			"prevDate" => $prevDate->format('d-m-Y'),
			"nextDate" => $nextDate->format('d-m-Y'),
        ];

		if ($request->isXmlHttpRequest()) {

			$command = new FormatFlightDataCommand([
				'search' => $data,
				'flightService' => $this->get('FlightBookingService'),
				'sessionService' => $this->get('session'),
			]);
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData(),$response->getStatusCode());
		}

		return $this->render('NavicuInfrastructureBundle:Web/Flights:list.html.twig',['data' => json_encode(['search' => $data])]);
	}

	/**
	* Persistencia de los datos de un vuelo
	*/
	public function flightReservationAction(Request $request)
	{
        $command = new CreateFlightReservationCommand([
            'flights' => json_decode($request->request->get('flights'),true),
            'passengers' => [
                'adults' => $request->request->get('adult'),
                'kids' => $request->request->get('kid')
            ]
        ]);

        $command->set('currency',$request->getSession()->get('alphaCurrency'));
        $response = $this->get('CommandBus')->execute($command);
        return $this->render('NavicuInfrastructureBundle:Web/Flights:reservation.html.twig',
            array('response' => json_encode($response->getData())));
	}

	public function flightPreReservationAction(Request $request)
	{
		$data 			= json_decode($request->getContent(), true);
		$apiResponse 	= $this->get('FlightBookingService')->reserveFlightItinerary($data);
		$command 		= new BookFlightsCommand($apiResponse);
		$response 		= $this->get('CommandBus')->execute($command);
		if ($response->isOk()) {
			$paymentGateway = $this->get( 'PaymentGatewayService' )->getPaymentGateway( $data['payments']['paymentType'] );
			$currency 		= $request->getSession()->get('alphaCurrency');

			$command = new PayFlightReservationCommand($data['payments']);
			$command
				->set('flightBookingService',$this->get('FlightBookingService'))
				->set('paymentGateway',$paymentGateway)
				->set('ip',$request->getClientIp())
				->set('payments', $data['card'])
				->set('currency',$currency);

			$response = $this->get('CommandBus')->execute($command);
		}

		return new JsonResponse($response->getData(),$response->getStatusCode());
	}

	public function flightResumeAction ($publicId, Request $request)
    {
        $data = array('publicId' => $publicId);

        //$command = new GetFlightResumeCommand($data);

        //$response = $this->get('CommandBus')->execute($command);
        return $this->render('NavicuInfrastructureBundle:Web/Flights:resume.html.twig'
            );
    }
}
