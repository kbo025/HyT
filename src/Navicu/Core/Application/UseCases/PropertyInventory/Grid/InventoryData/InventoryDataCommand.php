<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\Grid\InventoryData;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Services\SecurityACL;

/**
 * Comando para el manejo de la información que se manejara
 * en la grilla.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class InventoryDataCommand implements Command
{
	/**
	 * @var string $slug		Slug del establecimiento.
	 */
	private $slug;

	/**
	 * @var date $startDate		Fecha de inicio.
	 */
	private $startDate;

	/**
	 * @var date $endDate		Fecha de inicio.
	 */
	private $endDate;

	/**
	 * @var date $session		Manejo de los recurso de session por medio de profileOwner.
	 */
	private $session;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->slug = $data["slug"];
		$this->startDate = isset($data["startDate"]) ? $data["startDate"] : date("Y-m-d");

		if (!$data["userSession"]->getOwnerProfile()) {
			$this->session = true;
		} else {
			$this->session = SecurityACL::isSlugOwner($data["slug"], $data["userSession"]->getOwnerProfile());
			if ($this->startDate < date("Y-m-d"))
				$this->startDate = date("Y-m-d");
		}
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		$this->endDate = date_modify( new \DateTime($this->startDate),'+20 day')->format('Y-m-d');

		return array(
			'slug' => $this->slug,
			'endDate' => $this->endDate,
			'startDate' => $this->startDate
        );
	}
}
