<?php
namespace Navicu\Core\Application\UseCases\ClientInfoSession;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para incluir dentro de la session de usuario ciertos
 * parametros del establecimiento.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ClientInfoSessionCommand implements Command
{
	/**
	 * @var string $slug		Variable para el manejo de un objeto usuario.
	 */
	private $user;

	/**
	 * @var string $slug		Variable para el manejo de paginación.
	 */
	private $page;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->user = $data["user"];
		$this->page = isset($data["page"]) ? $data["page"] : 1;
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
			'page' => $this->page
        );
	}
}
