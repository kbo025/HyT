<?php

namespace Navicu\Core\Domain\Repository;

/**
* 	UserRepositoryInterface es la interfaz que obliga a la infraestructura a implementar los metodos que manipulan los datos
*	de los usuarios en general en la BD
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*/
interface ServiceTypeRepository
{
	/**
	*	Esta funcion retorna un array que contiene la estructura de los servicios
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*
	*	@return array
	*/
	public function getServicesStructure();

	/**
	*	Esta funcion retorna un array con todos los servicios con los id's de los servicios como claves
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*
	*	@return array
	*/
	public function findAllwithKeys();

	/**
	 * Busca ServiceType por sus atributos
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  Array
	 * @return ServiceType
	 */
	public function findOneByArray($array);

    /**
     *  Busca una instancia de ServiceType Dado un ID
     *
     * @param integer $id
     * @retur PropertyService | null
     */
    public function getById($id);
	
}
