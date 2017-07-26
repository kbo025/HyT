<?php

namespace Navicu\Core\Domain\Repository;

/**
*	RoomFeatureTypeRepository define los metodos que el repositorio de tipos de caracteristicas de habitaciones puede contener
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*/
interface RoomFeatureTypeRepository
{
	/**
	*	retorna una lista de todos las caracteristicas de tipo espacio fisico que se registraron
	*	@return array
	*/
	public function getSpacesList();

	/**
	*	retorna una lista de todos las caracteristicas de tipo servicio que se registraron
	*	@return array
	*/
	public function getServicesList();

	/**
	*	retorna una array de objetos en el que cada indice es el id del mismo
	*	@return array
	*/
	public function getAllWithKeys();

	/**
	 * Busca una caracteristica del habitación por un conjunto de campos
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  Array
	 */
	public function findOneByArray($array);
}