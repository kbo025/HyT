<?php

namespace Navicu\Core\Domain\Repository;

/**
* se declaran los metodos y funciones que seran implementadas por el repositorio de category
*
* @author Gabriel Camacho <kbo025@gmailcom>
* @author Currently Working: Gabriel Camacho <kbo025@gmailcom>
* @version 01/06/15
*/
interface CategoryRepository
{
	/**
	*	Metodo que devuelve un objeto category por su id
	*	@param Integer
	*	@return Category
	*/
	public function find($id);

	/**
	*	Metodo que devuelve Array de objetos category que representan un ICAO
	*	@return Array
	*/
	public function getAllICAO();

	/**
	*	Metodo que devuelve Array de objetos category que representan un Coin
	*	@return Array
	*/
	public function getAllCurrency();

	/**
	*	Metodo que devuelve un objeto tipo category que cumpla con las condiciones
	*	@return Category
	*/
	public function findOneBy(array $criteria);
}