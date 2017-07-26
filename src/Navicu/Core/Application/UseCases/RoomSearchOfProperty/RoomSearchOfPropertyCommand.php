<?php
namespace Navicu\Core\Application\UseCases\RoomSearchOfProperty;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para buscar las habitaciones junto con sus servicios
 * de un establecimiento por medio del motor de busqueda.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RoomSearchOfPropertyCommand implements Command
{
	/**
	 * @var date $startDate		Fecha de inicio.
	 */
	private $startDate;

	/**
	 * @var date $endDate  		Fecha Final.
	 */
	private $endDate;

	/**
	 * @var integer $adult 		Numero de adultos.
	 */
	private $adult;

	/**
	 * @var integer $kid  		Numero de niños.
	 */
	private $kid;

	/**
	 * @var integer $room 		Numero de habitaciones.
	 */
	private $room;

	/**
	 * @var string $slug	El slug de un establecimiento.
	 */
	private $slug;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param string $word Palabra
	 */
	public function __construct($data)
	{
		$this->adult = !$data["adult"] ? 2 : $data["adult"];
		$this->kid = !$data["kid"] ? 0 : $data["kid"];
		$this->room = !$data["room"] ? 1 : $data["room"];
		$this->slug = $data["slug"];
		
		$this->startDate = !isset($data["startDate"]) ? null : strtotime($data["startDate"]);
		$this->endDate = !isset($data["endDate"]) ? null : strtotime($data["endDate"]);
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		if ($this->Rules()) {
			return null;
		} else {
			return array(
				'startDate'=>$this->startDate,
				'endDate'=>$this->endDate,
				'adult'=>$this->adult,
				'kid'=>$this->kid,
				'room'=>$this->room,
				'slug'=>$this->slug
			);
		}
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	private function Rules()
	{
		$ban = false;

		if (($this->startDate >= $this->endDate) and $this->startDate != "") {
			$ban = true;
		}

		if ($this->slug == "") {
			$ban = true;
		}

		return $ban;
	}

	/**
	 * Devuelve la fecha de inicio.
	 *
	 * @return String
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * Obtiene la fecha de incio.
	 *
	 * @return void
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * Devuelve la fecha final.
	 *
	 * @return String
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * Obtiene la fecha final.
	 *
	 * @return void
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}

	/**
	 * Devuelve el numero de adulto.
	 *
	 * @return Integer
	 */
	public function getAdult()
	{
		return $this->adult;
	}

	/**
	 * Obtiene el numero de adulto.
	 *
	 * @return void
	 */
	public function setAdult($adult)
	{
		$this->adult = $adult;
	}

	/**
	 * Devuelve el numero de niños.
	 *
	 * @return Integer
	 */
	public function getKid()
	{
		return $this->kid;
	}

	/**
	 * Obtiene el numero de niños.
	 *
	 * @return void
	 */
	public function setKid($kid)
	{
		$this->kid = $kid;
	}

	/**
	 * Devuelve el numero de habitaciones.
	 *
	 * @return Integer
	 */
	public function getRoom()
	{
		return $this->room;
	}

	/**
	 * Obtiene el numero de habitaciones.
	 *
	 * @return void
	 */
	public function setRoom($room)
	{
		$this->room = $room;
	}

	/**
	 * Devuelve el slug del establecimiento.
	 *
	 * @return Integer
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * Obtiene el slug del establecimiento.
	 *
	 * @return void
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}
}
