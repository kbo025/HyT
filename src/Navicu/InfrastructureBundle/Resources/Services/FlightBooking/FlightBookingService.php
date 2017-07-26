<?php
namespace Navicu\InfrastructureBundle\Resources\Services\FlightBooking;

/**
 * Servicio encargado de establecer comunicación con el web service de reservas de vuelos
 */
class FlightBookingService
{

    private $container;

    private $parameters;

    private $translator = [
        'request' => [
            'get_availability_one_way' => [
                'adult' => 'adt',
                'kid' => 'ccn',
                'startDate' => 'date',
                'country' => 'pais',
                'iso_currency' => 'isomoneda',
                'from' => 'source',
                'to' => 'dest'
            ],
            'get_availability_round_trip' => [
                'adult' => 'adt',
                'kid' => 'ccn',
                'startDate' => 'date',
                'endDate' => 'dateseld',
                'country' => 'pais',
                'iso_currency' => 'isomoneda',
                'from' => 'source',
                'to' => 'dest'
            ],
            'get_availability_by_range_dates' => [
                'adult' => 'adt',
                'kid' => 'ccn',
                'country' => 'pais',
                'iso_currency' => 'isomoneda',
                'from' => 'source',
                'to' => 'dest',
                'startDate' => 'fechaida',
                'endDate' => 'fechavuelta',
            ],
            'reserve' => [],
            'cancel' => [],
            'confirm' => [],
        ],
        'response' => [
            'get_availability_one_way' => [
                "oneWay" => "oneWay",
                "origin" => "from",
                "origin_name" => "fromName",
                "origin_city_name" => "fromCityName",
                "destination" => "to",
                "destination_name" => "toName",
                "destination_city_name" => "toCityName",
                "airline" => "airline",
                "airline_name" => "airlineName",
                "flight" => "number_flight",
                "departure" => "departure",
                "arrival" => "arrival",
                "duration" => "duration",
                "stops" => "stops",
                "airplane" => "airplane_code",
                "meal" => "meal",
                "price" => "price"
            ],
            'get_availability_round_trip' => [
                "oneWay" => "oneWay",
                "return" => "return",
                "origin" => "from",
                "origin_name" => "fromName",
                "origin_city_name" => "fromCityName",
                "destination" => "to",
                "destination_name" => "toName",
                "destination_city_name" => "toCityName",
                "airline" => "airline",
                "airline_name" => "airlineName",
                "flight" => "number_flight",
                "departure" => "departure",
                "arrival" => "arrival",
                "duration" => "duration",
                "stops" => "stops",
                "airplane" => "airplane_code",
                "meal" => "meal",
                "price" => "price"
            ],
            'get_availability_by_range_dates' => [
                "date" => "date",
                "oneWay" => "oneWay",
                "return" => "return",
                "origin" => "from",
                "origin_name" => "fromName",
                "origin_city_name" => "fromCityName",
                "destination" => "to",
                "destination_name" => "toName",
                "destination_city_name" => "toCityName",
                "airline" => "airline",
                "airline_name" => "airlineName",
                "flight" => "number_flight",
                "departure" => "departure",
                "arrival" => "arrival",
                "duration" => "duration",
                "stops" => "stops",
                "airplane" => "airplane_code",
                "meal" => "meal",
                "price" => "price"
            ],
            'reserve' => [],
            'cancel' => [],
            'confirm' => [],
        ],
    ];

	/**
	 *	constructor de la clase
	 */
	public function __construct($container)
	{
        $this->container = $container;
        $this->parameters = $this->container->getParameter('flight_booking');
	}

    private function send($data = null, $section = '', $method = 'GET', $postData = null)
    {
        global $kernel;

        if ($kernel->getEnvironment() == 'prod')
            $url = $this->parameters['prod_url_base'].$this->parameters['actions'][$section];
        else
            $url = $this->parameters['dev_url_base'].$this->parameters['actions'][$section];

        $petition = curl_init();
        if(isset($data)) {
            $data = $this->formatData($data,$section,'request');

            if (is_null($data))
                $data = [];

            $data['token'] = $this->parameters['security_token'];
            $data['nusuario'] = $this->parameters['username'];
            $url = $url.'?'.http_build_query($data);

            if ($method != 'GET') {
                curl_setopt($petition, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($postData)) {
                    curl_setopt($petition, CURLOPT_POSTFIELDS,json_encode($postData));
                }
            }
        }
        curl_setopt($petition, CURLOPT_URL, $url);
        curl_setopt($petition, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(utf8_decode(curl_exec($petition)),true);
        $httpcode = is_null($response) ? 500 : curl_getinfo($petition, CURLINFO_HTTP_CODE);
        return [
            'success' => ($httpcode >= 200 || $httpcode < 300),
            'httpCode' => $httpcode,
            'message' => is_null($response) ? 'error_api_response' : null,
           'data' => $this->formatData($response,$section, 'response')
        ];
    }

    public function getAvailabilityFlightItinerary($data)
    {
        try {
            $response = [];

            $data['startDate'] = \DateTime::createFromFormat('d-m-Y', $data['startDate']);
            $data['startDate'] = $data['startDate']->format('Y-m-d');
            $data['country'] = 'VE';
            $data['iso_currency'] = $this->container->get('session')->get('alphaCurrency');
            if(!empty($data['roundTrip'])) {
                $section = 'get_availability_round_trip';
                $data['endDate'] = \DateTime::createFromFormat('d-m-Y', $data['endDate']);
                $data['endDate'] = $data['endDate']->format('Y-m-d');
            } else {
                $section = 'get_availability_one_way';
            }
            return $this->send($data,$section);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'httpCode' => 500,
                'message' => 'server_error',
                'data' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }
    }

    public function getAvailabilityFlightItineraryByRangeDate($data)
    {
        try {
            $from = $data['from'];
            $to = $data['to'];
            $data['from'] = $to;
            $data['to'] = $from;

            $data['country'] = 'VE';
            $data['iso_currency'] = $this->container->get('session')->get('alphaCurrency');

            return $this->send($data,'get_availability_by_range_dates');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'httpCode' => 500,
                'message' => 'server_error',
                'data' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }
    }

    public function reserveFlightItinerary($data)
    {
        try {
            $get = [

            ];
            return $this->send($data, 'reserve', 'POST', $data);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'httpCode' => 500,
                'message' => 'server_error',
                'data' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }
    }

    public function cancelFlightItinerary($data)
    {
        try {
            return $this->send($data, 'cancel');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'httpCode' => 500,
                'message' => 'server_error',
                'data' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }
    }

    public function confirmFlightItinerary($data)
    {
        try {
            return $this->send($data, 'confirm');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'httpCode' => 500,
                'message' => 'server_error',
                'data' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }
    }

    private function formatData($data, $section, $origin)
    {
        $dictionary = $this->translator[$origin][$section];
        return $this->format($data, $dictionary);
    }

    private function format($data, $dictionary)
    {
        $response = [];
        foreach ($data as $index => $value) {
            if (array_key_exists($index,$dictionary)) {
                if (is_array($value)) {
                    $response[$dictionary[$index]] = $this->format($value, $dictionary);
                } else {
                    $response[$dictionary[$index]] = $value;
                }
            } else {
                if (is_integer($index))
                    $response[$index] = $this->format($value, $dictionary);
            }
        }
        return $response;
    }
}
