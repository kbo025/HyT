<?php
namespace Navicu\Core\Application\UseCases\MoreSuggestionSearch;

use Navicu\Core\Application\Contract\Command;

/**
* Comando para buscar posibles destinos por medio del motor de busqueda.
* 
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
* @version 22/06/2015
*/
class MoreSuggestionSearchCommand implements Command
{
	/**
	 * @var string $word  		Palabra del buscador
	 */
	private $word;
	
	/**
	 * @var string $type  		Tipo de index a usar
	 */
	private $type;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param string $word Palabra
	 */
	public function __construct($data)
	{
		$this->word = $data["word"];
		$this->type = $data["type"];
	}

	/**
	 * Devuelve un array con los datos que encapsula
	 *
	 * @return Array
	 */
	public function getRequest()
	{
		return array(
            'word'=>$this->word,
			'type'=>$this->type
        );
	}

	/**
	 * Devuelve la palabra\s a buscar por medio de sphinx
	 *
	 * @return String
	 */
	public function getWord()
	{
		return $this->word;
	}

	/**
	 * Obtiene la palabra\s a buscar por medio de sphinx
	 *
	 * @return void
	 */
	public function setWord($word)
	{
		$this->word = $word;
	}

	/**
	 * Devuelve el tipo de index a usar.
	 *
	 * @return String
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Obtiene el tipo de index a usar.
	 *
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
}
