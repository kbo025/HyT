<?php
namespace Navicu\Core\Application\UseCases\Admin\SetRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\RateByKid;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Bedroom;
use Navicu\Core\Domain\Model\Entity\RoomFeature;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\Entity\RateByPeople;
use Navicu\Core\Domain\Model\Entity\LogsUser;
use Navicu\Core\Domain\Model\Entity\Livingroom;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * El siguiente handler guarda dentro de la BD la habitacion de un establecimiento.
 *
 * Class SetRoomHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SetRoomHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     * @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @throws \Exception
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();
        $data = $request["room"];
        //die(var_dump($data["prices"]["rateKids"]));
        $log["roomCopy"] = null;
        $log["featureC"] = null;
        $log["ratePeoplesC"] = null;
        $log["bedRoomsC"] = null;
        $log["livingsC"] = null;


        if (is_null($data["id"])) {
            $room = new Room;
            $property = $rf->get("Property")->findBySlug($request["slug"]);
            $room->setProperty($property);
        } else {

            $room = $rf->get("Room")->find($data["id"]);
            $log["roomCopy"] = clone $room;

            if (!$room)
                return new ResponseCommandBus(404, 'Not Found', null);

            $bedRooms = $room->getBedRooms();
            $log["bedRoomsC"] = clone $bedRooms;
            foreach($bedRooms as $bedRoom) {
                $room->removeBedRoom($bedRoom);
                $rf->get("Room")->remove($bedRoom);
            }

            $features = $room->getFeatures();
            $log["featureC"] = clone $features;
            foreach($features as $feature) {
                $room->removeFeature($feature);
                $rf->get("Room")->remove($feature);
            }

            $ratePeoples = $room->getRatesByPeoples();
            $log["ratePeoplesC"] = clone $ratePeoples;
            foreach($ratePeoples as $ratePeople) {
                $room->removeRatesByPeople($ratePeople);
                $rf->get("Room")->remove($ratePeople);
            }

            $rateKids = $room->getRatesByKids();
            $log["rateKidsC"] = clone $rateKids;
            foreach($rateKids as $rk) {
                $room->removeRatesByKid($rk);
                $rf->get("Room")->remove($rk);
            }

            $livings = $room->getLivingrooms();
            $log["livingsC"] = clone $livings;
            foreach($livings as $living) {
                $room->removeLivingroom($living);
                $rf->get("Room")->remove($living);
            }

        }

        $room->updateObject($data, $rf);

        $combinationBeds = $data['combinationsBeds'];
        for ($cb = 0; $cb < count($combinationBeds); $cb++) {
            $bedRoom = new Bedroom;
            $bedRoom->updateObject($combinationBeds[$cb]);
            $dataBeds = $combinationBeds[$cb]["beds"];
            for ($b = 0; $b < count($dataBeds); $b++) {
                $bed = new Bed($dataBeds[$b]["typeBed"], $dataBeds[$b]["numTypeBed"]);

                $validator = CoreValidator::getValidator($bed);
                if ($validator)
                    return new ResponseCommandBus(400, 'Ok', $validator);

                $bedRoom->addBed($bed); // Crear relación BD
                $bed->setBedRoom($bedRoom);
            }

            $validator = CoreValidator::getValidator($bedRoom);
            if ($validator)
                return new ResponseCommandBus(400, 'Ok', $validator);

            $room->addBedRoom($bedRoom);  // Crear relación BD
            $bedRoom->setRoom($room);
        }

        $fetureNumBedroom = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>1));
        $fetureBath = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>2));
        $fetureBalcony = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>3));
        $fetureDresser = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>5));
        $fetureTerrace = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>4));
        $fetureDiner = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>6));
        $fetureCooking = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>7));
        $fetureNumLiving = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>8));
        $fetureSpa = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>9));
        $feturePool = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>10));
        $fetureGarden = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>11));
        $fetureLaundry = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>12));


        if (isset($data["bath"]) && $data["bath"]) {
            $bath = new RoomFeature;
            $bath->setFeature($fetureBath);
            $bath->setAmount($data["bath"]);
            $bath->setRoom($room);
            $room->addFeature($bath);
        }

        if (isset($data["diner"]) && $data["diner"]) {
            $diner = new RoomFeature;
            $diner->setFeature($fetureDiner);
            $diner->setAmount($data["diner"]);
            $diner->setRoom($room);
            $room->addFeature($diner);
        }

        if (isset($data["dresser"]) && $data["dresser"]) {
            $dresser = new RoomFeature;
            $dresser->setFeature($fetureDresser);
            $dresser->setAmount($data["dresser"]);
            $dresser->setRoom($room);
            $room->addFeature($dresser);
        }

        if (isset($data["numBedroom"]) && $data["numBedroom"]) {
            $numBedroom = new RoomFeature;
            $numBedroom->setFeature($fetureNumBedroom);
            $numBedroom->setAmount($data["numBedroom"]);
            $numBedroom->setRoom($room);
            $room->addFeature($numBedroom);
        }

        if (isset($data["numLiving"]) && $data["numLiving"]) {
            $numLiving = new RoomFeature;
            $numLiving->setFeature($fetureNumLiving);
            $numLiving->setAmount($data["numLiving"]);
            $numLiving->setRoom($room);
            $room->addFeature($numLiving);
        }

        if (isset($data["balcony"]) && $data["balcony"]) {
            $balcony = new RoomFeature;
            $balcony->setFeature($fetureBalcony);
            $balcony->setRoom($room);
            $room->addFeature($balcony);
        }

        if (isset($data["cooking"]) && $data["cooking"]) {
            $cooking = new RoomFeature;
            $cooking->setFeature($fetureCooking);
            $cooking->setRoom($room);
            $room->addFeature($cooking);
        }

        if (isset($data["terrace"]) && $data["terrace"]) {
            $terrace = new RoomFeature;
            $terrace->setFeature($fetureTerrace);
            $terrace->setRoom($room);
            $room->addFeature($terrace);
        }

        if (isset($data["spa"]) && $data["spa"]) {
            $spa = new RoomFeature;
            $spa->setFeature($fetureSpa);
            $spa->setRoom($room);
            $room->addFeature($spa);
        }

        if (isset($data["pool"]) && $data["pool"]) {
            $pool = new RoomFeature;
            $pool->setFeature($feturePool);
            $pool->setRoom($room);
            $room->addFeature($pool);
        }

        if (isset($data["garden"]) && $data["garden"]) {
            $garden = new RoomFeature;
            $garden->setFeature($fetureGarden);
            $garden->setRoom($room);
            $room->addFeature($garden);
        }

        if (isset($data["laundry"]) && $data["laundry"]) {
            $laundry = new RoomFeature;
            $laundry->setFeature($fetureLaundry);
            $laundry->setRoom($room);
            $room->addFeature($laundry);
        }

        $featureOther =   array_filter($data['featuresRoomBath']) +  array_filter($data['featuresRoomBeedRoom']) +  array_filter($data['featuresRoomOthers']);

        $keyFeature =  array_keys($featureOther);
        for ($f = 0; $f < count($featureOther); $f++) {
            if ($featureOther[$keyFeature [$f]]) {
                $fetureOther = $rf->get("RoomFeatureType")->findOneByArray(array("id"=>$keyFeature [$f]));
                $roomFeature = new RoomFeature;
                $roomFeature->setFeature($fetureOther);
                $roomFeature->setRoom($room);
                $room->addFeature($roomFeature);
            }
        }

        $room->setIncrement(!empty($data["prices"]["increment"]));
        $room->setIncrementKid(!empty($data["prices"]["incrementKid"]));
        $room->setKidPayAsAdult(!empty($data["prices"]["kidPayAsAdult"]));

        $dataRP = $data["prices"]["ratePeople"];
        $dataRK = isset($data['prices']['rateKids']) ? $data["prices"]["rateKids"] : null;

        $typeAmount = $data["prices"]["variationTypePeople"] == 1 ?  100 : 1;
        $room->setVariationTypePeople($data["prices"]["variationTypePeople"] == 1 ? 1 : 0);
        $room->setVariationTypeKids($data["prices"]["variationTypeKid"]);

        if ($room->getIncrement()) {
            $room->setSameIncrementAdult(!empty($data["prices"]["sameIncrementAdult"]));
            for ($i=0; $i<$room->getMaxPeople(); $i++) {
                // Verifica si el numero de persona existe en el arreglo
                $idNumberPeople = array_search($i+1, array_column($dataRP, 'numberPeople'));
                if (is_integer($idNumberPeople)) {
                    $ratebyPeople = new RateByPeople;
                    $ratebyPeople
                        ->setAmountRate($dataRP[$idNumberPeople]["amountRate"] / $typeAmount)
                        ->setNumberPeople($dataRP[$idNumberPeople]["numberPeople"])
                        ->setRoom($room);
                    $room->addRatesByPeople($ratebyPeople);

                    $validator = CoreValidator::getValidator($ratebyPeople);
                    if ($validator)
                        return new ResponseCommandBus(400, 'Ok', $validator);

                } else {
                    if (!$room->getProperty()->getChild() && $room->getMinPeople() != 1) {
                        $ratebyPeople = new RateByPeople;
                        $ratebyPeople
                            ->setAmountRate(0)
                            ->setNumberPeople($i+1)
                            ->setRoom($room);
                        $room->addRatesByPeople($ratebyPeople);
                    }
                }
            }
        } else {
            $room->setSameIncrementAdult(false);
        }

        if ($room->getProperty()->getChild() && $room->getIncrementKid() && !$room->getKidPayAsAdult()) {
            $room->setSameIncrementKid(!empty($data["prices"]["sameIncrementKid"]));
            for ($i = 0; $i < count($dataRK); $i++) {
                $typeAmountKid = $data["prices"]["variationTypeKid"][$i] == 1 ?  100 : 1;
                for ($j = 0; $j < count($dataRK[$i]); $j++) {
                    if (isset($dataRK[$i][$j])) {
                        $ratebyKid = new RateByKid();
                        $ratebyKid
                            ->setAmountRate($dataRK[$i][$j]["amountRate"] / $typeAmountKid)
                            ->setNumberKid($dataRK[$i][$j]["numberKid"])
                            ->setIndex($i)
                            ->setRoom($room);
                        $room->addRatesByKid($ratebyKid);

                        $validator = CoreValidator::getValidator($ratebyKid);
                        if ($validator)
                            return new ResponseCommandBus(400, 'Ok', $validator);
                    } else {
                        throw new \Exception('rate_by_kids_incorrect', 400);
                    }
                }
            }
//
//
//
//            for ($i = 0; $i < $room->getMaxPeople() - 1; $i++) {
//                if (isset($dataRK[$i])) {
//                    $ratebyKid = new RateByKid();
//                    $ratebyKid
//                        ->setAmountRate($dataRK[$i]["amountRate"] / $typeAmountKid)
//                        ->setNumberKid($dataRK[$i]["numberKid"])
//                        ->setRoom($room);
//                    $room->addRatesByKid($ratebyKid);
//
//                    $validator = CoreValidator::getValidator($ratebyKid);
//                    if ($validator)
//                        return new ResponseCommandBus(400, 'Ok', $validator);
//                } else {
//                    throw new \Exception('rate_by_kids_incorrect', 400);
//                }
//            }
        } else {
            $room->setSameIncrementKid(false);
        }

        if (!empty($data["livings"])) {
            $dataLiving = $data["livings"];
            for ($l = 0; $l < count($dataLiving); $l++) {
                $living = new Livingroom;
                $living->setAmountCouch($dataLiving[$l]["numCouchRoom"]);
                $living->setAmountPeople($dataLiving[$l]["numLivingPerson"]);
                $living->setRoom($room);
                $room->addLivingroom($living);
            }
        }

        $name = $room->getName();
        $room->generateName();

        if (($name != $room->getName()) and $room->getId()) {
            $room->changeNameGallery($rf, $name);
        }

        // Validar si la suma de habitaciones de un establecimiento es
        // igual al total del monto de habitaciones del mismo.
        $room->validAmountRooms($room);

        $validator = CoreValidator::getValidator($room);
        if ($validator)
            return new ResponseCommandBus(400, 'Ok', $validator);

        $rf->get("Room")->save($room);

        $this->saveLog($log, $room, $rf, $data["id"]);

        if(isset($log["roomCopy"])) {
            $changeType = ($log["roomCopy"]->getType()->getId() != $room->getProperty()->getId());
            $changeMaxPeople = $log["roomCopy"]->getMaxPeople() != $room->getMaxPeople();  

            //if ( $changeType || $changeMaxPeople ) {
                $this->refactorRoomNamesForProperty(
                    $rf,
                    $room->getProperty()->getId(),
                    $room->getType()->getId()
                );
                $this->refactorRoomNamesForProperty(
                    $rf,
                    $log["roomCopy"]->getProperty()->getId(),
                    $log["roomCopy"]->getType()->getId()
                );
            //}
        }

        return new ResponseCommandBus(200, 'Ok', null);
    }

	/**
	 * esta funcion verifica las habitaciones de un establecimiento y las renombra dependiendo 
     *
     */
	public function refactorRoomNamesForProperty($rf,$propertyId,$typeId)
	{
		$rep = $rf->get('Room');
		$rooms = $rep->findActiveRoomsByIdProperty($propertyId,$typeId);
		if (count($rooms) > 0) {
			$persistRoom = [];
			$unique = true;
			foreach( $rooms as $room ) {
				$room->setUniqueType($unique);
				$room->generateName();
                $room->changeNameGallery($rf);
				$persistRoom[] = $room;
				$unique = false;
			}
			$rep->save($persistRoom);
		}
	}

    /**
     * Esta función es usada para Guardar un log dado las distintas
     * acción que pueden ocurrir dentro del caso de uso.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $room         Objeto Habitación
     *
     * @return boolean
     */
    public function saveLog($log, $room, $rf, $idRoom)
    {
        $response = [];

        if ($idRoom) { // guardado de habitación cuando es editar
            if ($log["roomCopy"]->getName() != $room->getName())
                $response["name"] = $room->getName();

            if ($log["roomCopy"]->getMaxPeople() != $room->getMaxPeople())
                $response["maxPeople"] = $room->getMaxPeople();

            if ($log["roomCopy"]->getAmountRooms() != $room->getAmountRooms())
                $response["numRooms"] = $room->getAmountRooms();

            if ($log["roomCopy"]->getBaseAvailability() != $room->getBaseAvailability())
                $response["baseAvailability"] = $room->getBaseAvailability();

            if ($log["roomCopy"]->getSmokingPolicy() != $room->getSmokingPolicy())
                $response["smokingPolicy"] = $room->getSmokingPolicy();

            if ($log["roomCopy"]->getSize() != $room->getSize())
                $response["size"] = $room->getSize();

        } else { // guardado de habitación cuando es crear
            $response["name"] = $room->getName();
            $response["maxPeople"] = $room->getMaxPeople();
            $response["numRooms"] = $room->getAmountRooms();
            $response["baseAvailability"] = $room->getBaseAvailability();
            $response["smokingPolicy"] = $room->getSmokingPolicy();
            $response["size"] = $room->getSize();
        }

        $response += $this->bedRoomsLog($log, $room);
        $response += $this->servicesLog($log, $room);
        $response += $this->ratePeoplesLog($log, $room);
        $response += $this->livingsLog($log, $room);

        if (empty($response))
            return false;

        $data["action"] = $idRoom ? "update" : "create";
        $data["resource"] = "room";
        $data["idResource"] = $room->getId();
        $data["description"] = $response;
        $data['property'] = $room->getProperty();
        $data['user'] = CoreSession::getUser();

        $logsUser = new LogsUser();
        $logsUser->updateObject($data);
        $rf->get("LogsUser")->save($logsUser);

        return true;
    }

    /**
     * Esta función es usada para buscar el historico de los
     * servicios.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $room         Objeto Habitación
     *
     * @return Array
     */
    public function servicesLog($log, $room)
    {
        $features = $room->getFeatures()->toArray();
        $features = array_map(
            function($e)
            {
                $amount = $e->getAmount() ? "-".$e->getAmount() : "-".null;
                return $e->getFeature()->getId().$amount;
            },
            $features
        );

        if ($log["featureC"]) {
            $featuresC = $log["featureC"]->toArray();
            $featuresC = array_map(
                function ($e) {
                    $amount = $e->getAmount() ? "-" . $e->getAmount() : "-" . null;
                    return $e->getFeature()->getId() . $amount;
                },
                $featuresC
            );
        } else {
            $featuresC = [];
        }

        $deleteFeatures = array_diff($featuresC,$features);
        $addFeatures = array_diff($features,$featuresC);

        $deleteFeatures = array_map(
            function($s)
            {
                $ss = explode( '-',$s);
                $amount = $ss[1] ? ", AmountService : ".$ss[1] : null;
                return "type : ".$ss[0].$amount;
            },
            $deleteFeatures
        );
        $addFeatures = array_map(
            function($s)
            {
                $ss = explode( '-',$s);
                $amount = $ss[1] ? ", AmountService : ".$ss[1] : null;
                return "type : ".$ss[0].$amount;
            },
            $addFeatures
        );

        $response["deleteServices"] = $deleteFeatures;
        $response["addServices"]= $addFeatures;

        return (empty($deleteFeatures) and empty($addFeatures)) ? [] :$response;
    }

    /**
     * Esta función es usada para buscar el historico del
     * aumento por persona.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $room         Objeto Habitación
     *
     * @return Array
     */
    public function ratePeoplesLog($log, $room)
    {
        if ($log["ratePeoplesC"]) {
            $ratePeoplesC = $log["ratePeoplesC"]->toArray();
            $ratePeoplesC = array_map(
                function ($e) {
                    return $e->getNumberPeople() . "-" . $e->getAmountRate();
                },
                $ratePeoplesC
            );
        } else {
            $ratePeoplesC = [];
        }

        $ratePeoples = $room->getRatesByPeoples()->toArray();
        $ratePeoples = array_map(
            function($e)
            {
                return $e->getNumberPeople()."-".$e->getAmountRate();
            },
            $ratePeoples
        );

        $deleteRatePeoples = array_diff($ratePeoplesC,$ratePeoples);
        $addRatePeoples = array_diff($ratePeoples,$ratePeoplesC);

        $deleteRatePeoples = array_map(
            function($e)
            {
                $ap = explode( '-',$e);
                return "NumberPeople : ".$ap[0].", AmountPeople : ".$ap[1];
            },
            $deleteRatePeoples
        );

        $addRatePeoples = array_map(
            function($e)
            {
                $ap = explode( '-',$e);
                return "NumberPeople : ".$ap[0].", AmountPeople : ".$ap[1];
            },
            $addRatePeoples
        );

        $response["deleteRatePeoples"] = $deleteRatePeoples;
        $response["addRatePeoples"] = $addRatePeoples;

        return (empty($deleteRatePeoples) and empty($addRatePeoples)) ? [] :$response;
    }

    /**
     * Esta función es usada para buscar el historico de las
     * combinaciones de camas dentro de una habitación.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $room         Objeto Habitación
     *
     * @return Array
     */
    public function bedRoomsLog($log, $room)
    {
        $bedRooms = $room->getBedRooms()->toArray();
        $bedRooms = array_map(
            function($e)
            {
                $beds = implode(
                    "+",
                    array_map(
                        function($b)
                        {
                            return $b->getType()."#".$b->getAmount();
                        },
                        $e->getBeds()->toArray()));
                return $e->getBath()."-".$e->getAmountPeople()."--".$beds;
            },
            $bedRooms
        );

        if ($log["bedRoomsC"]) {
            $bedRoomsC = $log["bedRoomsC"]->toArray();
            $bedRoomsC = array_map(
                function ($e) {
                    $beds = implode(
                        "+",
                        array_map(
                            function ($b) {
                                return $b->getType() . "#" . $b->getAmount();
                            },
                            $e->getBeds()->toArray()));
                    return $e->getBath() . "-" . $e->getAmountPeople() . "--" . $beds;
                },
                $bedRoomsC
            );
        } else {
            $bedRoomsC = [];
        }

        $deleteBedRooms= array_diff($bedRoomsC,$bedRooms);
        $addBedRooms = array_diff($bedRooms,$bedRoomsC);

        $deleteBedRooms = array_map(
            function($bd)
            {
                $data = explode( '--',$bd);

                $dataBr = explode( '-',$data[0]);
                $resp["bathBedroom"] = $dataBr[0];
                $resp["numPersonBedroom"] = $dataBr[1];
                $dataBed = explode( '+',$data[1]);
                $resp["dataBeds"] =array_map(
                    function($b)
                    {
                        $bed = explode( '#',$b);
                        $resp["type"] = $bed[0];
                        $resp["amount"] = $bed[1];
                        return $resp;
                    },
                    $dataBed
                );
                return $resp;
            },
            $deleteBedRooms
        );

        $addBedRooms = array_map(
            function($bd)
            {
                $data = explode( '--',$bd);

                $dataBr = explode( '-',$data[0]);
                $resp["bathBedroom"] = $dataBr[0];
                $resp["numPersonBedroom"] = $dataBr[1];
                $dataBed = explode( '+',$data[1]);
                $resp["dataBeds"] =array_map(
                    function($b)
                    {
                        $bed = explode( '#',$b);
                        $resp["type"] = $bed[0];
                        $resp["amount"] = $bed[1];
                        return $resp;
                    },
                    $dataBed
                );
                return $resp;
            },
            $addBedRooms
        );


        $response["deleteBedRooms"] = $deleteBedRooms;
        $response["addBedRooms"] = $addBedRooms;

        return (empty($deleteBedRooms) and empty($addBedRooms)) ? [] :$response;
    }

    /**
     * Esta función es usada para buscar el historico de las
     * sala de una habiatacion "livings".
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $room         Objeto Habitación
     *
     * @return Array
     */
    public function livingsLog($log, $room)
    {
        $livings = $room->getLivingrooms()->toArray();
        $livings = array_map(
            function($e)
            {
                return $e->getAmountPeople()."-".$e->getAmountCouch();
            },
            $livings
        );

        if ($log["livingsC"]) {
            $livingsC = $log["livingsC"]->toArray();
            $livingsC = array_map(
                function ($e) {
                    return $e->getAmountPeople() . "-" . $e->getAmountCouch();
                },
                $livingsC
            );
        } else {
            $livingsC = [];
        }

        $deleteLivings = array_diff($livingsC,$livings);
        $addLivings = array_diff($livings,$livingsC);

        $deleteLivings = array_map(
            function($l)
            {
                $ll = explode( '-',$l);
                return "amountPeople : ".$ll[0].", amountCouch: ".$ll[1];
            },
            $deleteLivings
        );
        $addLivings = array_map(
            function($l)
            {
                $ll = explode( '-',$l);
                return "amountPeople : ".$ll[0].", amountCouch: ".$ll[1];
            },
            $addLivings
        );

        $response["deleteLivings"] = $deleteLivings;
        $response["addLivings"]= $addLivings;

        return (empty($deleteLivings) and empty($addLivings)) ? [] : $response;
    }
}