<?php

namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *
 * Se define una clase objeto valor que define una ubicacion en terminos de latitud y longitud
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 21/05/2015
 * 
 */
class Coordinate
{
	/**
	* 	valor longitud
	*	@var Real 
	*/
	private $longitude;

	/**
	* 	valor latitud
	*	@var Real 
	*/
	private $latitude;

	/**
	*	metodo constructor de la clase
	*	@param $lon real
	*	@param $lat real
	*/
	public function __construct($lon, $lat)
	{
		if ((($lon>=-180) && ($lon<=180)) && (($lat>=-90) && ($lat<=90))) {
			$this->longitude = $lon;
			$this->latitude = $lat;
		} else {
			throw new \Exception('not_valid');
		}
	}

	/**
	*	Devuelve el valor de longitude
	*	@return Real
	*/
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	*	Devuelve el valor de latitude
	*	@return Real
	*/
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	*	Devuelve una representacion de si mismo como array
	*	@return Array()
	*/
	public function toArray()
	{
		return array(
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        );
	}
}