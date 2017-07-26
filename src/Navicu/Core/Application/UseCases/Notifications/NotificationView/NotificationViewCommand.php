<?php
namespace Navicu\Core\Application\UseCases\Notifications\NotificationView;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para dar por visto las notificaciones del usuario,
 * en referencia a las reservas.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class NotificationViewCommand implements Command
{
	/**
	 * @var string $slug		Variable para el manejo de un objeto usuario.
	 */
	private $user;

	/**
	 * @var string $slug		Variable para el manejo del tipo de notificación.
	 */
	private $type;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->user = $data["user"];
		$this->type = isset($data["type"]) ? $data["type"] : 1;
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'user' => $this->user,
			'type' => $this->type
        );
	}
}
