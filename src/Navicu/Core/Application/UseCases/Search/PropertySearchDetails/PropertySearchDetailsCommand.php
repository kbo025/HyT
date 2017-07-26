<?php
namespace Navicu\Core\Application\UseCases\Search\PropertySearchDetails;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando usado para la busqueda de la información de un establecimiento
 * necesaria para llenar la ficha del establecimiento en Busqueda de destino.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PropertySearchDetailsCommand extends CommandBase implements Command
{
	/**
	 * @var string $slug	El slug de un establecimiento.
	 */
	protected $slug;

	/**
	 * @var string $countryCode representa el código del país donde se encuentra el establecimiento
	 */
	protected $countryCode;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param string $word Palabra
	 */
	/*public function __construct($data)
	{
		$this->slug = $data["slug"];
		$this->countryCode = $data["countryCode"];
	}*/

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	/*public function getRequest()
	{
		return array(
			'slug'=>$this->slug,
			'countryCode' => $this->countryCode
        );
	}*/

	/**
	 * Devuelve el slug del establecimiento.
	 *
	 * @return Integer
	 */
	/*public function getSlug()
	{
		return $this->slug;
	}*/

	/**
	 * Obtiene el slug del establecimiento.
	 *
	 * @return void
	 */
	/*public function setSlug($slug)
	{
		$this->slug = $slug;
	}*/

	/**
	 * @return mixed
	 */
	/*public function getCountryCode()
	{
		return $this->countryCode;
	}*/

	/**
	 * @param mixed $countryCode
	 */
	/*public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
	}*/

}
