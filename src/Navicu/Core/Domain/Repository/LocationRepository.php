<?php

namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\Location;
/**
* 	Interfaz de la LocationRepository
*
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*	@version 25/05/2015
*/
interface LocationRepository
{
	/**
	*	Este metodo devuelve una estructura de todos los paises con sus estados y a la ves estos con sus ciudades
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*	@version 25/05/2015
	*	@return Array
	*/
	public function getAll();

	/**
	*	Este metodo Busca y devuelve un Location por su id
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*	@version 28/05/15
	*	@return Location
	*/
	public function find($id);

	/**
	 * Busca una localidad dado un codigo de pais un slug y un tipo de localidad
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $codeCountry
	 * @param $slug
	 * @param $type
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOneByCountrySlugType($codeCountry, $slug, $lvl);

}