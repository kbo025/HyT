<?php

namespace Navicu\Core\Domain\Repository;

/**
*	RoomTypeRepository define los metodos que el repositorio de tipos de habitaciones debe implementar
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*/
interface RoomTypeRepository
{
	/**
	*	devuelve la estructura de arbol de tipos de habitaciones en arrays clave valor de forma $id => $title
	*	@return array 
	*/
	public function getRoomsTypesStructure();

	/**
	 * Busca un tipo habitación por un conjunto de campos
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  Array
	 */
	public function findOneByArray($array);

}