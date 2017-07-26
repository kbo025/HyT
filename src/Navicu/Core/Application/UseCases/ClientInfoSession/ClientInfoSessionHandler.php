<?php
namespace Navicu\Core\Application\UseCases\ClientInfoSession;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Clase para incluir dentro de la session de usuario ciertos
 * parametros del establecimiento.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ClientInfoSessionHandler implements Handler
{
	/**
     * Esta función es usada para incluir dentro de la session de usuario
     * ciertos parametros del establecimiento.
	 *
     * @param SlugSecurity $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();
		$user = $request["user"];

		$notificationsEntities = $rf->get("Notification")->findByRecient($user->getId());

		if ($notificationsEntities) {
			$auxN = $this->pagination($notificationsEntities, $request["page"]);

			for ($n = 0; $n < count($auxN["data"]); $n++) {
				if (isset($notific["data"][$auxN["data"][$n]->getMessage()])) {
					// si ya existe, le añadimos uno
					$notific["data"][$auxN["data"][$n]->getMessage()] += 1;
				} else {
					// si no existe lo añadimos al array
					$notific["data"][$auxN["data"][$n]->getMessage()] = 1;
				}
			}
			if (!empty($notific)) {
				$response["page"] = $auxN["page"];

				$auxN = null;
				$response["data"] = [];
				foreach ($notific["data"] as $key => $value) {

					$auxN["message"] = CoreTranslator::getTransChoice(
						"share.message." . $key,
						$value == 1 ? 0 : 1,
						['%count%' => $value]

					);

					switch ($key) {
						case "reservation.accepted":
							$auxN["type"] = 0;
							break;
						case "reservation.per-confirm":
							$auxN["type"] = 1;
							break;
						case "reservation.canceled":
							$auxN["type"] = 2;
							break;
					}
					$auxN["count"] = $value;
					array_push($response["data"], $auxN);
				}
				CoreSession::set("notifications", $response);
			}
		} else {
			CoreSession::set("notifications", null);
		}


		CoreSession::set("userName", $user->getClientProfile()->getFullName());

	}

	/**
	 * Función usada para el manejo de paginación dado una colección de
	 * notificaciones.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $notifications
	 * @param Array $page
	 * @return Array
	 */
	public function pagination($notifications, $page)
	{

		// Uso de Paginación.
		$resultPerPage = 5; // Numero de resultado por pagina
		$lowerLimit = ($page - 1) * $resultPerPage;// limite inferior
		$upperLimit = (($page - 1 ) * $resultPerPage ) + $resultPerPage; // limite superior

		//si limite inferior es inferior a la cantidad de notificaciones encontrado
		if ($lowerLimit > count($notifications)) { 
			return 0;
		}

		//si limite inferior es superior a la cantidad de notificaciones encontrado
		if ($upperLimit > count($notifications)) {
			$upperLimit = count($notifications);
		}

		$response["data"] = [];

		// Contrucción de las notificaciones para la paginación especificada.	
		for ($i = $lowerLimit; $i < $upperLimit; $i++) {
			array_push($response["data"], $notifications[$i]);
		}

		$pageTotal = ceil(count($notifications)/$resultPerPage);
		$response["page"]["total"] = (int)$pageTotal;
		$response["page"]["next"] = $page + 1 <= $pageTotal ? $page + 1 : null;
		$response["page"]["previous"] = $page - 1 >= 1 ? $page - 1 : null;
		$response["page"]["current"] = $page;

		return $response;
	}
}