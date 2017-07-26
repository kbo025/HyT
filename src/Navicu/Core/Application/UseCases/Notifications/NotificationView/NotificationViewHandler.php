<?php
namespace Navicu\Core\Application\UseCases\Notifications\NotificationView;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Clase para dar por visto las notificaciones del usuario,
 * en referencia a las reservas.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class NotificationViewHandler implements Handler
{
	/**
	 *
     * @param SlugSecurity $command Objeto Command contenedor
     * @param $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		/*
		 * $request["type"]
		 * 0 : vista las pre-reservas
		 * 1 : vista las reservas aceptadas
		 * 2 : vista las reservas canceladas
		 * */

		$request = $command->getRequest();
		$fosUser = $request["user"]->getUser();
		$type = $request["type"];

		$notifications = $fosUser->getNotificationInput();

		foreach ($notifications as $notification) {
			switch ($type) {
				case 0: // vista las pre-reserva
					if ($notification->getMessage() == "reservation.per-confirm") {
						$notification->setView(true);
						$rf->get("Notification")->save($notification);
					}
					break;
				case 1: // vista las reservas aceptadas
					if ($notification->getMessage() == "reservation.accepted") {
						$notification->setView(true);
						$rf->get("Notification")->save($notification);
					}
					break;
				case 2: // vista las reservas canceladas
					if ($notification->getMessage() == "reservation.canceled") {
						$notification->setView(true);
						$rf->get("Notification")->save($notification);
					}
					break;
			}
		}
	}
}