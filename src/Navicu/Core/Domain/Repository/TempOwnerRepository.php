<?php 

namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\TempOwner;

/**
* 	TempOwnerRepositoryInterface es la interfaz que obliga a la infraestructura a implementar los metodos que manipulan los datos de
*	un usario propietario temporal en la BD
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*	@version 05-05-2015
*/
interface TempOwnerRepository
{
	/**
	*	Metodo registerUser es el encargado de persistir un usuario tipo tempowner
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@param TempOwner $user 	usuario que se debe registrar
	*	@return void
	*/
	public function registerUser($user);

	/**
	 * verifica si existe un usuario por su username
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param String $data
	 */
	public function usernameExist($data);

	/**
	 * verifica si existe un usuarios por su email
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param String $data
	 */
	public function emailExist($data);

	 /**
	 * Busca a un usuario temporal por su username
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param  String
	 */
	public function findOneByUsername($Username);

	 /**
	 * Busca a un usuario temporal por su username
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param  Array
	 */
	public function findOneByArray($array);

	/**
	 * Obtener los establecimiento temporales dado un comercial
	 *
	 * @param $commercialId
	 * @return array
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @version 06/04/2016
	 */
	public function findByCommercialId($commercialId);
}