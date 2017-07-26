<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\SlugSecurity;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para el manejo de la seguridad por el uso de slug.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SlugSecurityCommand implements Command
{

	/**
	 * @var array $session		Manejo de los recurso de session por medio de profileOwner.
	 */
	private $session;

	/**
	 * @var string $slug		Variable para el manejo de un slug.
	 */
	private $slug;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->session = $data["userSession"];
		$this->slug = $data["slug"];
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'session' => $this->session,
			'slug' => $this->slug
        );
	}
}
