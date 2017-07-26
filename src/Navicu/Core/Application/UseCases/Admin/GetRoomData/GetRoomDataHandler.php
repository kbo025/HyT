<?php
namespace Navicu\Core\Application\UseCases\Admin\GetRoomData;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Bed;

/**
 * El siguiente handler busca la información de una habitacion de un establecimiento.
 *  *
 * Class GetRoomDataHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetRoomDataHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
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
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
		$request = $command->getRequest();

        $idRoom = $request["id"];
        $response = array();

        $response['typeRoom'] = $rf
            ->get('RoomType')
            ->getRoomsTypesStructure();

        $response['beds'] = Bed::getBedsTypes();

        $response['sections'] = $rf
            ->get('RoomFeatureType')
            ->getSpacesList();

        $response['services'] = $rf
            ->get('RoomFeatureType')
            ->getServicesList();

        $response['servicesBedroom'] = $rf
            ->get('RoomFeatureType')
            ->getServicesList('Habitación');

        $response['servicesBath'] = $rf
            ->get('RoomFeatureType')
            ->getServicesList('Baño');

        $response['servicesOthers'] = $rf
            ->get('RoomFeatureType')
            ->getServicesList('otro');

        $repoRoom = $rf->get('Room');
        $room = $repoRoom->findOneBy(array('id'=>$idRoom));

        $repoProperty = $rf->get('Property');
        $property = $repoProperty->findBySlug($request['slug']);

        if ($property) {
            $response['rules'] = [
                'discountRate' => $property->getDiscountRate(),
                'rateType' => $property->getRateType(),
                'tax' => $property->getTax(),
                'taxRate' => $property->getTaxRate(),
                'acceptsChild' => $property->getChild()
            ];

            $response['agePolicy'] = $property->getAgePolicy();
        }

        if (!$room) {
        	$response['form'] = null;
			return $response;
        }



		$auxRoom['id'] = $room->getId();
		$auxRoom['numPeople'] = $room->getMaxPeople();
		$auxRoom['minPeople'] = $room->getMinPeople();
		$auxRoom['numRooms'] = $room->getAmountRooms();
		$auxRoom['baseAvailability'] = $room->getBaseAvailability();
		$auxRoom['sizeRoom'] = $room->getSize();
		$auxRoom['smoking'] = $room->getSmokingPolicy();

		$roomFeature = $room->getFeatures();
		$auxRoom['featuresRoomBath'] = array();
		$auxRoom['featuresRoomBeedRoom'] = array();
		$auxRoom['featuresRoomOthers'] = array();
		$auxRoom['balcony'] = false;
		$auxRoom['terrace'] = false;
		$auxRoom['cooking'] = false;
		$auxRoom['spa'] = false;
		$auxRoom['pool'] = false;
		$auxRoom['garden'] = false;
		$auxRoom['laundry'] = false;

        for ($rf = 0; $rf < count($roomFeature); $rf++) {
			$idFeature = $roomFeature[$rf]->getFeature()->getId();
			if ($idFeature == "2") //Baño
				$auxRoom['bath'] = $roomFeature[$rf]->getAmount();

			if ($idFeature == "3") //Balcón
				$auxRoom['balcony'] = true;

			if ($idFeature == "4") //Terraza
				$auxRoom['terrace'] = true;

			if ($idFeature == "5") //Vestidor
				$auxRoom['dresser'] = $roomFeature[$rf]->getAmount();

			if ($idFeature == "6") //Comedor
				$auxRoom['diner'] = $roomFeature[$rf]->getAmount();

			if ($idFeature == "7") //Cocina
				$auxRoom['cooking'] = true;

			if ($idFeature == "9") //Spa
				$auxRoom['spa'] = true;

			if ($idFeature == "10") //Piscina
				$auxRoom['pool'] = true;

			if ($idFeature == "11") //Jardin
				$auxRoom['garden'] = true;

			if ($idFeature == "12") //Lavandería
				$auxRoom['laundry'] = true;

			if ($idFeature >= 46 and $idFeature <=54 ) // Caracteristicas del Baño
				$auxRoom['featuresRoomBath'][$idFeature] =  true;

			if (($idFeature >= 14 and $idFeature <=45) or $idFeature ==66 ) // Caracteristicas de la Habitación
				$auxRoom['featuresRoomBeedRoom'][$idFeature] = true;

			if ($idFeature >=55 and $idFeature <=65 ) // Caracteristicas de la Habitación
				$auxRoom['featuresRoomOthers'][$idFeature] = true;

			if ($idFeature == "1") //Dormitorio
				$auxRoom['numBedroom'] = $roomFeature[$rf]->getAmount();

			if ($idFeature == "8") //Dormitorio
				$auxRoom['numLiving'] = $roomFeature[$rf]->getAmount();
		}

		$auxRoom['combinationsBeds'] = [];
		$bedRooms = $room->getBedRooms();
        for ($br = 0; $br < count($bedRooms); $br++) {
			$auxBr['bathBedroom'] = $bedRooms[$br]->getBath();
			$auxBr['numPersonBedroom'] = $bedRooms[$br]->getAmountPeople();
			$bed = $bedRooms[$br]->getBeds();
			$auxBr['beds'] = [];
			for ($b = 0; $b < count($bed); $b++) {
                $auxBr['beds'][] = [
                    'numTypeBed' => $bed[$b]->getAmount(),
				    'typeBed' => $bed[$b]->getType()
                ];
			}
			$auxRoom['combinationsBeds'][] = $auxBr;
		}

		$auxRoom['prices']['increment'] =  $room->getIncrement();
        $auxRoom['prices']['incrementKid'] = $room->getIncrementKid();
        $auxRoom['prices']['kidPayAsAdult'] =  $room->getKidPayAsAdult();
        $auxRoom['prices']['sameIncrementAdult'] =  $room->getSameIncrementAdult();
        $auxRoom['prices']['sameIncrementKid'] =  $room->getSameIncrementKid();
		$auxRoom['prices']['variationTypePeople'] =  (string)($room->getVariationTypePeople()==1 ? 1 : 2);
        $auxRoom['prices']['variationTypeKid'] =  $room->getVariationTypeKids();
		$auxRoom['prices']['ratePeople'] = [];

		$ratesByPeoples = $room->getRatesByPeoples();
		for ($rp = 0; $rp < $room->getMaxPeople(); $rp++) {
			$auxRoom['prices']['ratePeople'][] = [
				'amountRate' => 0,
				'numberPeople' => $rp + 1,
			];
		}

        for ($rp = 0; $rp < count($ratesByPeoples); $rp++) {

			// Solo se muestra la cantidad de persona dependiendo de la cantidad
			// de minimo de persona por habitaciòn y si acepta o no niño.
			if ($property->getChild() || ($room->getMinPeople() <= $ratesByPeoples[$rp]->getNumberPeople())) {
				$typeRate = ($auxRoom['prices']['variationTypePeople'] == 1) ? 100 : 1;
				$auxRoom['prices']['ratePeople'][$ratesByPeoples[$rp]->getNumberPeople() - 1]['amountRate'] = $ratesByPeoples[$rp]->getAmountRate() * $typeRate;
			}
		}

		usort($auxRoom['prices']['ratePeople'], function($a, $b) {
			return $a['numberPeople'] - $b['numberPeople'];
		});

        $auxRoom['prices']['rateKids'] = [];
        $rateByKids = $room->getRatesByKids();
	    if (!is_array($auxRoom['prices']['variationTypeKid'])) {
		    $auxRoom['prices']['variationTypeKid'] = array($auxRoom['prices']['variationTypeKid']);
	    }
        foreach ($auxRoom['prices']['variationTypeKid'] as $element) {
            array_push($auxRoom['prices']['rateKids'], array());
        }
        foreach ($rateByKids as $rate) {
            $tempRate = array();
            if($auxRoom['prices']['variationTypeKid'][$rate->getIndex()] == 1) {
                $tempRate['amountRate'] = $rate->getAmountRate() * 100;
            } else {
                $tempRate['amountRate'] = $rate->getAmountRate();
            }
            $tempRate['numberKid'] = $rate->getNumberKid();
            array_push(
                $auxRoom['prices']['rateKids'][$rate->getIndex()],
                $tempRate
            );
        }
//        for ($rk = 0; $rk < count($rateByKids); $rk++) {
//            $auxRk = [];
//			if ($auxRoom['prices']['variationTypeKid'] == 1) {
//				$auxRk['amountRate'] = $rateByKids[$rk]->getAmountRate() * 100;
//			} else {
//				$auxRk['amountRate'] = $rateByKids[$rk]->getAmountRate();
//			}
//			$auxRk['numberKid'] = $rateByKids[$rk]->getNumberKid();
//			$auxRoom['prices']['rateKids'][] = $auxRk;
//		}
//
//		usort($auxRoom['prices']['rateKids'], function($a, $b) {
//			return $a['numberKid'] - $b['numberKid'];
//		});

		if ($room->getType()->getParent()) {
			$auxRoom['subRoom'] = $room->getType()->getId();
			$auxRoom['typeRoom'] = $room->getType()->getParent()->getId();
		} else {
			$auxRoom['subRoom'] = null;
			$auxRoom['typeRoom'] = $room->getType()->getId();
		}

		$auxRoom["livings"] = [];
		foreach ($room->getLivingrooms() as $living) {
			$l["numCouchRoom"] = $living->getAmountCouch();
			$l["numLivingPerson"] = $living->getAmountPeople();
			array_push($auxRoom["livings"], $l);
		}

		$response['form'] = $auxRoom;
        return $response;
    }
}