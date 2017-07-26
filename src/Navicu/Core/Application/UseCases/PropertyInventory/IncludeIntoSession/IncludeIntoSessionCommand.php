<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\IncludeIntoSession;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para incluir dentro de la session de usuario ciertos
 * parametros del establecimiento.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class IncludeIntoSessionCommand implements Command
{
	/**
	 * @var string $slug		Variable para el manejo de un slug.
	 */
	private $slug;

	/**
	 * @var string $slug		Variable para el manejo de un slug.
	 */
	private $properties;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->slug = isset($data["slug"]) ? $data["slug"] : null;
		$this->properties = isset($data["properties"]) ? $data["properties"] : null;
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'slug' => $this->slug,
			'properties' => $this->properties
        );
	}
}
