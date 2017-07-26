<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\InventoryLogs\SearchLogsFile;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Services\SecurityACL;

/**
 * Comando para el manejo de la información contenida en un archivo logs
 * del establecimiento.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SearchLogsFileCommand implements Command
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
	 * @var string $logFile		Variable para el manejo delñ nombre del archivo logs..
	 */
	private $logFile;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->session = SecurityACL::isSlugOwner($data["slug"], $data["userSession"]);
		$this->slug = $data["slug"];
		$this->logFile = $data["logFile"];
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'logFile' => $this->logFile,
			'slug' => $this->slug
        );
	}
}
