<?php

namespace Navicu\Core\Application\Contract;

/**
*	Interface RepositoryFactory modela un servicio usado por el commandBus y los handler para obtener
*	determinados repositorios en el momento que sean requeridos
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*	@version 19/05/2015
*/
interface RepositoryFactoryInterface
{
	/**
	*	Devuelve el objeto repoisitorio de la entidad que se pasa por parametro
	*	@return TempOwnerRepository
	*	@author Currently Working: Gabriel Camacho
	*	@version 19/05/2015
	*	@param String
	*	@return EntityRepository
	*/
	public function get($entity);
}