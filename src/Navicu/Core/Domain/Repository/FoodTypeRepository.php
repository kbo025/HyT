<?php

namespace Navicu\Core\Domain\Repository;

/**
* 	UserRepositoryInterface es la interfaz que obliga a la infraestructura a implementar los metodos que manipulan los datos
*	de los usuarios en general en la BD
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*/
interface FoodTypeRepository
{

	/**
	*	Metodo que devuelve un array Clave Valor en donde la clave es el id del registro y el valor es el titulo del servicio
	*	@return array
	*/
	public function findAllWithKeys();

	public function findListAll();

    /**
     * Devuelve una instancia dado un id
     *
     * @param integer $id
     * @return Object
     */
    public function getById($id);
}