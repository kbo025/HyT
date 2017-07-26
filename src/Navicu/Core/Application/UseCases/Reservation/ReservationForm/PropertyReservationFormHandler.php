<?php

namespace Navicu\Core\Application\UseCases\Reservation\ReservationForm;

use Herrera\Phar\Update\Exception\Exception;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\LockedAvailabilityService;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\NotAvailableException;
use Navicu\Core\Domain\Model\Entity\ChildrenAge;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationPack;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Application\Services\InventoryService;
use Navicu\Core\Application\Services\RateExteriorCalculator;

/**
 * Clase para ejecutar el caso de uso ReservationForm
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 24/08/2015
 */

class PropertyReservationFormHandler implements Handler
{

    /**
    * instancia del comando que ejecuta la acción
    */
    protected $command;

    /**
    * instancia del RepositoryFactory
    */
    protected $rf;
    
    /**
     * Ejecuta el caso de uso 'Procesar y verificar los datos de la reserva mediante el formulario'
     *
     * @param Command $command Objeto Command contenedor de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->command = $command;
        $this->rf = $rf;

        $reservation = new Reservation();
        $response = $this->getBasicData($tax, $reservation);
	    //die(var_dump(json_decode($command->get('rooms')[0]['packages'][0]['childrenAges'],true)));
        $error = $this->getRoomsData($command->get('rooms'), $response, $tax, $reservation);

        if (!empty($response) and !$error) {
            return new ResponseCommandBus(201, null, $response);
        }

        return new ResponseCommandBus(404, 'Bad Request',$error);
    }

    /**
     * La siguiente función retorna los datos básicos de la busqueda
     * referentes a los datos del establecimiento.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/09/2015
     * @return array
     */
    private function getBasicData(&$tax, $reservation)
    {
        $response = array();
        $rpProperty = $this->rf->get('Property');

        $property = $rpProperty->findOneBy(array('slug' => $this->command->get('slug')));
        if (!is_null($property)) {
            $reservation->setPropertyId($property);
            $tax = $property->getTax();
            $response['slug'] = $property->getSlug();
            $response['propertyName'] = $property->getName();
            $response['propertyAddress'] = $property->getAddress();
            $response['propertyStar'] = $property->getStar();
            $response['propertyCheckIn'] = $property->getCheckIn()->format('h:i A');
            $response['propertyCheckOut'] = $property->getCheckOut()->format('h:i A');
            $response['reservedNights'] =  $this->command->get('checkIn')
                ->diff($this->command->get('checkOut'))->days;

            $coordinates = $property->getCoordinates();
            $response['latitude'] = $coordinates['latitude'];
            $response['longitude'] = $coordinates['longitude'];
            $response['tax'] = $property->getTaxRate();

            $response['checkinReservation'] = $this->command->get('checkIn')->format('d-m-Y');
            $response['checkoutReservation'] = $this->command->get('checkOut')->format('d-m-Y');
            $response['maxAdvance'] = InventoryService::maxAdvance(
                $this->rf,
                $this->command->get('rooms'),
                $response['checkinReservation'],
                $response['checkoutReservation']
            );

            $response['numberChildren'] = 0;
            $response['numberAdults'] = 0;

            $rooms = $this->command->get('rooms');
            for ($i=0; $i < count($rooms); $i++) {

            }

            foreach ($this->command->get('rooms') as $currentRoom) {
                foreach ($currentRoom['packages'] as $currentPack) {
                    $response['numberAdults'] =
                        $response['numberAdults'] +
                        ($currentPack['numberRoom'] * $currentPack['numberPeople']);
                    $response['numberChildren'] =
                        $response['numberChildren'] +
                        ($currentPack['numberRoom'] * $currentPack['numberChildren']);
                }
            }
			$reservation->setAdultNumber($response['numberAdults']);
	        $reservation->setChildNumber($response['numberChildren']);

            $location = $property->getLocation();
            if (!is_null($location->getCityId())){
                $response['propertyCity'] = $location->getCityId()->getTitle();
                $response['propertyTown'] = $location->getParent()->getTitle();
            }else{
                $response['propertyCity'] = null;
                $response['propertyTown'] = $location->getParent()->getTitle();
            }
            $reservation
                ->setDateCheckIn($this->command->get('checkIn'))
                ->setDateCheckOut($this->command->get('checkOut'))
                ->setReservationDate(new \Datetime('now'))
                ->setClientId($this->command->get('client'))
                ->setIpAddress($this->command->get('clientIp'));

            $response["services"] = array();
            $services = $property->getServices();
            for ($s = 0; $s < count($services); $s++) {
                if ($services[$s]->getType()->getLvl() == 0) {
                    $root = $services[$s]->getType()->getTitle();
                } else {
                    $root = $services[$s]->getType()->getRoot()->getTitle();
                }
                array_push(
                    $response["services"],
                    array(
                        "name"=>$services[$s]->getType()->getTitle(),
                        "root"=>$root,
                        "priority"=>$services[$s]->getType()->getPriority()
                    )
                );
            }

            usort($response["services"], function($a, $b) {
                return $b['priority'] - $a['priority'];
            });
        }

        return $response;
    }

    /**
     * La siguiente función se encarga de obtener los datos de las habitaciones y servicios
     * dada la busqueda para la reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @param $rooms
     * @param $response
     * @param $tax
     * @return boolean
     * @version 01/09/2015
     */
    private function getRoomsData($rooms, &$response, $tax, $reservation)
    {
        $error = false;
        $response['rooms'] = array();

        $rpReservation = $this->rf->get('Reservation');
	    $rpReservationPack = $this->rf->get('ReservationPack');
	    $rpChildrenAge = $this->rf->get('ChildrenAge');
        $rpBedRoom = $this->rf->get('Bedroom');
        $rpRoom = $this->rf->get('Room');
        $rpPack = $this->rf->get('Pack');
        $rpDailyPack = $this->rf->get('DailyPack');
        foreach ($rooms as $currentRoom) {

            $auxRoom = array();

            //Se obtiene la habitación
            $room = $rpRoom->findOneBy(array('id' => $currentRoom['idRoom']));
            $parentRoom = $room->getType()->getParent();

            if (is_null($parentRoom))
                $nameRoom = $room->getType()->getTitle();
            else
                $nameRoom = $parentRoom->getTitle().' - '. $room->getType()->getTitle();

            $auxRoom['roomName'] = $nameRoom;

            $auxRoom['idRoom'] = $room->getId();
            $auxRoom['roomImages'] = array();

            //Se almacena el conjunto de imagenes
            foreach ( $room->getImagesGallery() as $currentImage) {
                array_push($auxRoom['roomImages'], $currentImage->getImage()->getFileName());
            }

            if ($currentRoom['idBedsRooms'])
                $bedRooms = $rpBedRoom->findOneBy(array('id' => $currentRoom['idBedsRooms']));
            else
                $bedRooms = false;

            $auxRoom['bedsType'] = '';

            if ($bedRooms) {
                $maxBed = count($bedRooms->getBeds());
                $i = 1;
                foreach ($bedRooms->getBeds() as $currentBed) {
                    if ($i != $maxBed)
                        $auxRoom['bedsType'] = $auxRoom['bedsType'].$currentBed->getTypeString().' + ';
                    else
                        $auxRoom['bedsType'] = $auxRoom['bedsType'].$currentBed->getTypeString();
                    $i++;
                }
                $auxRoom['idBedsType'] = $bedRooms->getId();
            }
            else {
                $auxRoom['bedsType'] = null;
                $auxRoom['idBedsType'] = null;
            }

            $auxRoom['packages'] = array();

            foreach ( $currentRoom['packages'] as $currentPack) {
            	//die(var_dump(json_decode($currentPack['childrenAges'],true)));
                $pack = $rpPack->findOneBy(['id' => $currentPack['idPack']]);
                $auxPack = [];
                $policyCancellation = null;
                foreach ($reservation->getPropertyId()->getPropertyCancellationPolicies() as $pcp) {
                    if($pcp->getCancellationPolicy()->getId()==$currentPack['idCancellationPolicy']) {
                        $policyCancellation = $pcp;
                    }
                }

                $reservationPack = new ReservationPack();
                $reservationPack
                    ->setPackId($pack)
                    ->setBedroomId($bedRooms ? $bedRooms : null)
                    ->setBedroom($bedRooms ? $bedRooms->toArray() : null)
                    ->setTypeRoom($room->getType())
                    ->setTypePack($pack->getType())
                    ->setNumberRooms($currentPack['numberRoom'])
                    ->setNumberAdults($currentPack['numberPeople'])
                    ->setNumberKids($currentPack['numberChildren'])
                    ->setPropertyCancellationPolicyId($policyCancellation)
                    ->setTypeCancellationPolicy(
                        $policyCancellation
                            ->getCancellationPolicy()
                            ->getType()
                        )
                    ->setCancellationPolicy(
                        $policyCancellation
                            ->getCancellationPolicy()
                            ->toArray()
                        )
                    ->setReservationId($reservation);
	            $childrenAges = json_decode($currentPack['childrenAges'],true);
	            if (!empty($childrenAges)) {
		            foreach ( $childrenAges as $currentAge ) {
			            $childrenAgeInstance = new ChildrenAge( $currentAge );
			            $childrenAgeInstance->setReservationPackage( $reservationPack );
			            //$rpChildrenAge->save($childrenAgeInstance);
			            $reservationPack->addChildrenAge( $childrenAgeInstance );
			            //$rpChildrenAge->save($childrenAgeInstance);
		            }
	            }
	            //$rpReservationPack->save($reservationPack);
                $reservation->addReservationPackage($reservationPack);

                $auxPack['idCancellationPolicy'] = $currentPack['idCancellationPolicy'];
                $auxPack['nameCancellationPolicy'] = CoreTranslator::getTranslator(
                    $policyCancellation
                        ->getCancellationPolicy()
                        ->getType()
                        ->getCode()
                );
                $auxPack['idPack'] = $pack->getId();
                $auxPack['namePack'] = CoreTranslator::getTranslator($pack->getType()->getCode());
                $auxPack['numberPack'] = $currentPack['numberRoom'];
                $auxPack['numberPeople'] = $currentPack['numberPeople'];
                $auxPack['numberChildren'] = $currentPack['numberChildren'];
	            $auxPack['childrenAges'] = json_decode($currentPack['childrenAges'],true);

                array_push($auxRoom['packages'], $auxPack);
            }

            array_push($response['rooms'], $auxRoom);
        }
        $respack = $reservation->getReservationPackages();
        $promotionArray = array();
        foreach ($respack as $currentReservationPack) {
            
            $dailyPackages = $rpDailyPack->findByDatesRoomId(
                $currentReservationPack->getPackId()->getId(),
                $reservation->getDateCheckIn()->format('Y-m-d'),
                $reservation->getDateCheckOut()->format('Y-m-d')
            );
            foreach ($dailyPackages as $currentDaily) {
                $promotionArray[] = $currentDaily->getPromotion();
            }
        }

        $promotion = in_array('true', $promotionArray);
        try {
            new RateCalculator($this->rf);
            new LockedAvailabilityService($this->rf);

            RateCalculator::calculateRateReservation($this->rf, $reservation);
            LockedAvailabilityService::lockAvailability($this->rf, $reservation);

            $currency = $this->command->get('alphaCurrency');
            if ( empty($currency) || $currency == 'VEF' )
                $reservation->roundReservation();
            else {
                $reservation->setAlphaCurrency($currency);
                new RateExteriorCalculator($this->rf,$currency, $reservation->getDateCheckIn());
                $reservation->setCurrencyPrice(RateExteriorCalculator::calculateRateChange($reservation->getTotalToPay()));
                new RateExteriorCalculator($this->rf,'USD', $reservation->getDateCheckIn());
                $reservation->setDollarPrice(RateExteriorCalculator::calculateRateChange($reservation->getTotalToPay()));
            }

            
            $rpReservation->save($reservation);

        } catch (NotAvailableException $e) {
            $error = [
                'error' => 'not_available',
                'message' => $e->getMessage(),
                'date' => $e->getDate()
            ];
        } catch (\Exception $e) {
            $error = [
                'error' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        } ;
        
        new RateExteriorCalculator($this->rf,null,$reservation->getDateCheckIn());
        $client = $this->command->get('client');
        $da = isset($client) ? $client->getUser()->getDisableAdvance() : null;
        $response['advance'] = empty($da);
        $response['subTotal'] = RateExteriorCalculator::calculateRateChange($reservation->getTotalToPay(false));
        $response['tax'] = RateExteriorCalculator::calculateRateChange($reservation->getTaxPay());
        $response['total'] = RateExteriorCalculator::calculateRateChange($reservation->getTotalToPay());
        $response['idReservation'] = $reservation->getPublicId();
        $response['promotion'] = $promotion;

        // Retorna si los datos de la BD son iguales a los del formulario
        return $error;
    }
}