<?php
namespace Navicu\Core\Application\UseCases\Search\getRoomByProperty;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para buscar los precios de las habitaciones junto
 * con sus servicios de un establecimiento por medio del
 * motor de busqueda.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getRoomByPropertyCommand implements Command
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
	 * @var String $slug		Manejo de slug del establecimiento.
	 */
	private $slug;

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
	 * @var integer $dayCount		Dias de diferencia en el rango de fecha.
	 */
	private $dayCount;

    /**
     * @var string $type  		Si la fecha inicial es enviada nula
     */
    private $nullDate;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param Array $data
	 */
	public function __construct($data)
	{
		$this->slug = $data["slug"];
		$this->adult = $data["adult"] ? $data["adult"] : 2;
		$this->kid = $data["kid"] ? $data["kid"] : 0;
		$this->room = $data["room"] ? $data["room"] : 1;
		$this->nullDate = $data["startDate"] ? false : true;

		$this->setDate($data["startDate"], $data["endDate"]);
	}

	/**
	 * Esta Función es usada para el manejo de un rango
	 * de fecha, empleando el manejo de restricciones utiles
	 * para la implementación correcta de motor de busqueda.
	 *
	 * @param date $startDate
	 * @param date $endDate
	 * @return void
	 */
	public function setDate($startDate, $endDate)
	{
		if ($startDate == "") {
			$this->startDate = strtotime(date("Y-m-d"));
			$this->endDate = strtotime ( '+30 day' , strtotime ( date("Y-m-d") ) ) ;
		} else {
			$this->startDate = strtotime($startDate);
			$this->endDate = strtotime($endDate);
		}

		// Asegura de que el rango este por encima del dia actual
		if ($this->startDate - strtotime(date("Y-m-d")) < 0) {
			$this->setDate(null, null);
			$this->nullDate = true;
		}else
			$this->dayCount = (( ( ($this->endDate - $this->startDate) / 60 ) / 60 ) / 24);
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
			'slug'=>$this->slug,
			'startDate'=>$this->startDate,
			'endDate'=>$this->endDate,
			'adult'=>$this->adult,
			'kid'=>$this->kid,
			'room'=>$this->room,
			'dayCount'=> $this->dayCount,
			'nullDate'=>$this->nullDate
        );
	}
}