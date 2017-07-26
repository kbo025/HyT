<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\Grid\InventoryData;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Clase el manejo de la petición de busqueda de información
 * para la grilla.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class InventoryDataHandler implements Handler
{
	/**
     * Esta función retorna un la información necesaria para la grilla.
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();

		$startDate = $request["startDate"];
		$endDate = $request["endDate"];
		$property = $rf->get('Property')->findBySlug($request["slug"]);

		$response = array(
            'startDate' =>  $startDate,
            'endDate' => $endDate,
            'basePolicy' => $property->getBasePolicy()->getId()
        );

		$response["rulesRate"] = array(
            'discountRate' => $property->getDiscountRate(),
            'tax' => $property->getTax(),
            'taxRate' => $property->getTaxRate(),
            'rateType' => $property->getRateType(),
            'currency' => $property->getCurrency()->getSimbol()
        );

		$response['acceptsChild'] = $property->getChild();

        $response['rooms'] = array();
		$rooms = $property->getRooms();
		for ($r = 0; $r < count($rooms); $r++) {
			if ($rooms[$r]->getIsActive()) {
				$aux['idRoom'] = $rooms[$r]->getId();
				$aux['name'] = $rooms[$r]->getName();
				$aux['baseAvailability'] = $rooms[$r]->getBaseAvailability();
				$aux['variationTypePeople'] = $rooms[$r]->getVariationTypePeople();
				$aux['variationTypeKids'] = $rooms[$r]->getVariationTypeKids();
				$aux['increment'] = $rooms[$r]->getIncrement();
				$aux['incrementKid'] = $rooms[$r]->getIncrementKid();
				$aux['kidPayAsAdult'] = $rooms[$r]->getKidPayAsAdult();
				$aux['numPeople'] = $rooms[$r]->getMaxPeople();
                $aux['minPeople'] = $rooms[$r]->getMinPeople();

				$aux['dailyRooms']= array();
				$dailyRoom = $rf->get('DailyRoom')->findByDatesRoomId($aux['idRoom'], $startDate, $endDate);
				for ($dr = 0; $dr < count($dailyRoom); $dr++) {
					array_push($aux['dailyRooms'], $dailyRoom[$dr]->getArray());
				}

				$ratesByPeoples = $rooms[$r]->getRatesByPeoples();
				$aux['rateByPeople'] = array();
				for ($rp = 0; $rp < count($ratesByPeoples); $rp++) {
                    // para devolver solo el listado de personas desde el minimo que la habitacion establece
				    if ($rp+1 >= $rooms[$r]->getMinPeople()) {
                        $auxRatePeople["number"] = $ratesByPeoples[$rp]->getNumberPeople();
                        $auxRatePeople["amount"] = $ratesByPeoples[$rp]->getAmountRate();
                        array_push($aux['rateByPeople'], $auxRatePeople);
                    }
				}

				usort($aux['rateByPeople'], function($a, $b) {
					return $a["number"] - $b["number"];
				});

				$ratesByKids = $rooms[$r]->getRatesByKids();
				$aux['rateByKids'] = [];
				$auxRateKid = [];

				for ($rp = 0; $rp < count($ratesByKids); $rp++) {
					$auxRateKid['number'] = $ratesByKids[$rp]->getNumberKid();
					$auxRateKid['amount'] = $ratesByKids[$rp]->getAmountRate();
					$aux['rateByKids'][] = $auxRateKid;
				}

				usort($aux['rateByKids'], function($a, $b) {
					return $a["number"] - $b["number"];
				});

				$auxPack = array();
				$aux['packages'] = array();

				$packages = $rooms[$r]->getPackages();

				for ($p = 0; $p < count($packages); $p++) {
					$idPack = $packages[$p]->getId();

					$auxPack['idPack'] = $idPack;
					$auxPack['name'] = CoreTranslator::getTranslator($packages[$p]->getType()->getCode());;

					$auxRule = array();
					$auxPack['cancellationPolicy'] = array();
					$packCancellationPolicies = $packages[$p]->getPackCancellationPolicies();

					for ($pcp = 0; $pcp < count($packCancellationPolicies); $pcp++) {
						$currentPolicy = $packCancellationPolicies[$pcp]->getCancellationPolicy();

						$auxPolicy['idPackCancellationPolicy'] =  $packCancellationPolicies[$pcp]->getId();
						$auxPolicy['name'] = CoreTranslator::getTranslator($currentPolicy->getType()->getCode());;
						$auxPolicy['variationType'] = $currentPolicy->getVariationType();
						$auxPolicy['variationAmount'] = $currentPolicy->getVariationAmount();

						array_push($auxPack['cancellationPolicy'], $auxPolicy);
					}

					$auxPack['dailyPackages'] = array();
					$dailyPackages = $rf->get('DailyPack')->findByDatesPackId($idPack, $startDate, $endDate);
					for ($dp = 0; $dp < count($dailyPackages); $dp++) {
						array_push($auxPack['dailyPackages'], $dailyPackages[$dp]->getArray());
					}

					array_push($aux['packages'], $auxPack);
				}
				array_push($response['rooms'], $aux);
			}
		}

		return new ResponseCommandBus(200, 'OK', $response);
	}
}
