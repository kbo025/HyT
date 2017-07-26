<?php
namespace Navicu\Core\Application\UseCases\AutoCompleteDestinationSearch;

use Navicu\Core\Application\Contract\Command;

/**
* Comando para buscar posibles destinos por medio del motor de busqueda.
* 
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
* @version 22/06/2015
*/
class AutoCompleteDestinationSearchCommand implements Command
{
	/**
	 * @var string $password  		Palabra del buscador
	 */
	private $word;

	/**
	 *	Constructor de la clase
	 *
	 * 	@param string $word Palabra
	 */
	public function __construct($word)
	{
		$this->word = $word;
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
}
