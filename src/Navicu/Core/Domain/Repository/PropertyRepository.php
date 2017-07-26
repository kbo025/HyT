<?php 

namespace Navicu\Core\Domain\Repository;

/**
* 	Interfaz de la PropertyRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 21-05-2015
*/
interface PropertyRepository
{
	/**
	 * Busca un establecimiento por su id
	 *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param  Integer $id
	 * @return Object Property
	 */
	public function findById($id);

	/**
	 * Retorna un establecimiento dado un slug y códgio del país (VE)
	 *
	 * @param $slug
	 * @param $countryCode
	 * @return mixed
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 */
	public function findBySlugCountryCode($slug, $countryCode);

	/**
	 * Obtener los establecimiento dado un comercial
	 *
	 * @param $commercialId
	 * @return array
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @version 06/04/2016
	 */
	public function findByCommercialId($commercialId);

    /**
     * Funcion encargada de realizar la busqueda y/o filtrado del listado de establecimiento
     *
     * @param $param array, array con la solicitud de la busqueda
     * @param string $searchVector, nombre del vector de busqueda
     * @return mixed
     * @version 19/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function affiliatePropertyByFilter($param, $searchVector);
}
