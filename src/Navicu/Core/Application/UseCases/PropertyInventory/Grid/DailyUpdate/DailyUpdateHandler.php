<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\Grid\DailyUpdate;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Model\Entity\LogsOwner;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Application\Services\SecurityACL;
use Navicu\Core\Domain\Adapter\CoreValidator;

/**
 * Clase para el manejo del ingreso y actualización de
 * los dailyRoom y dailyPack
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DailyUpdateHandler implements Handler
{
	/**
     * Esta función es usada para el manejo del ingreso y actualización de
	 * los dailyRoom y dailyPack
	 *
     * @param SuggestionSearch $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$response = array();
		$objDailies = array();
		$dataDaily = $request["data"];
		$repositoryDaily = isset($dataDaily['idPack']) ? $rf->get('DailyPack') : $rf->get('DailyRoom');

		if ($request["idDaily"]) {
			$objDaily = $repositoryDaily->findById($request["idDaily"]);
            if(!$objDaily){
				return new ResponseCommandBus(400, 'Bad Request');
            } else {
				$slug = $objDaily->getSlug();
                $ownerDaily = SecurityACL::isSlugOwner($slug, $request["session"]);
                if (!$ownerDaily) {
					return new ResponseCommandBus(400, 'Bad Request');
                } else {
					$oldData = $objDaily->getArray();
					$objDaily->updateObject($dataDaily);
                }
            }
        } else {
			$objDaily = null;
            if  (isset($dataDaily['idPack'])) {
				if($command->isApiRequest())
					$objPack = $rf->get('Pack')->findOneBy(['slug' => $dataDaily['idPack']]);
				else
					$objPack = $rf->get('Pack')->findById($dataDaily['idPack']);
                if ($objPack) {
					$objDaily = $repositoryDaily->findOneByPackIdDate($objPack->getId(), $dataDaily['date']);
					if (is_null($objDaily)) {
						$objDaily = new DailyPack;
						$objDaily->setPack($objPack);
					}
				}
            }
            if (isset($dataDaily['idRoom'])) {
				if($command->isApiRequest()) {
					$objRoom = $rf->get('Room')->findOneBy(['slug' => $dataDaily['idRoom']]);
				} else {
					$objRoom = $rf->get('Room')->findById($dataDaily['idRoom']);
				}
				if ($objRoom) {
					$objDaily = $repositoryDaily->findOneByDateRoomId($objRoom->getId(), $dataDaily['date']);
					if (is_null($objDaily)) {
						$objDaily = new DailyRoom;
						$objDaily->setRoom($objRoom);
					}
				}
            }
            if (!$objDaily) {
				return new ResponseCommandBus(400, 'Bad Request');
            }
			$oldData = null;
			$objDaily->updateObject($dataDaily);
		}
		$validator = CoreValidator::getValidator($objDaily, array(
			'isData',
			'isPositive',
			'business',
			'businessRankMinNightMaxNight'
		));
		if (count($validator)) {
			return new ResponseCommandBus(400, 'Bad Request', $validator);
		}
		if (get_class($objDaily) == "Navicu\Core\Domain\Model\Entity\DailyPack") {
			$objDailyRoom = $rf->get('DailyRoom')
			->findOneByDateRoomId(
				$objDaily->getPack()->getRoom()->getId(),
				$objDaily->getDate()
			);
			if (!$objDailyRoom) {
				$objDailies = array(
					$objDaily,
					$objDaily->getPack()->createDailyByRoom($objDaily)					
				);
			} else {
				$objDailies = array($objDaily->updateRestriction($objDailyRoom));
				if (!$objDailyRoom->getAvailability() && $objDailies[0]->getSpecificAvailability()) {
					$objDailyRoom->setAvailability($objDailies[0]->getSpecificAvailability());
					array_push($objDailies, $objDailyRoom);
				}
			}
		} else {
			$objDailyPackages = $rf->get('DailyPack')
			->findByRoomDate(
				$objDaily->getRoom()->getId(),
				$objDaily->getDate()
			);
			if (count($objDailyPackages) == 0) {
				$objDailyPackages = $objDaily->getRoom()->createDailyByPack($objDaily);
			}
			$objDailies = $objDaily->updateRestriction($objDailyPackages);
		}
		$validator = CoreValidator::getValidator($objDailies, array(
			'isPositive',
			'business',
			'businessNight',
			'businessAvailability',
			'businessRankMinNightMaxNight'
		));
		if (count($validator)) {
			return new ResponseCommandBus(400, 'Bad Request', $validator);
		}
		for ($r = 0; $r < count($objDailies); $r++) {
			$rf->get('DailyRoom')->save($objDailies[$r]);
			array_push($response, $objDailies[$r]->getArray());
		}
		$logsOwner = new LogsOwner;
		if ($logsOwner->saveLogsDaily($request["session"], $objDailies, $oldData)){
			$rf->get('DailyRoom')->save($logsOwner);
		}
        return new ResponseCommandBus(200, 'Ok', $response);
	}
}