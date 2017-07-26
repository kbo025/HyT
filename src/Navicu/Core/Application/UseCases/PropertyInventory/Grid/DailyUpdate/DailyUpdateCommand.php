<?php
namespace Navicu\Core\Application\UseCases\PropertyInventory\Grid\DailyUpdate;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para el manejo del ingreso y actualización de
 * los dailyRoom y dailyPack
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DailyUpdateCommand implements Command
{
	/**
	 * @var integer $id		id asignado al daily.
	 */
	private $idDaily;

	/**
	 * @var array $data		Manejo del conjunto de data.
	 */
	private $data;

	/**
	 * @var array $session		Manejo de los recurso de session por medio de profileOwner.
	 */
	private $session;

	private $apiRequest;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param array $data
	 */
	public function __construct($data)
	{
		$this->session = $data["userSession"];
		$this->idDaily = $data["id"];
		$this->data = isset($data["data"]) ? $data["data"] : null;
		$this->apiRequest = isset($data["apiRequest"]) ? $data["apiRequest"] : false;
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'idDaily' => $this->idDaily,
			'data' => $this->data,
			'session' => $this->session,
			'apiRequest' => $this->apiRequest 
        );
	}

    public function isApiRequest()
    {
        return $this->apiRequest;
    }
}
