<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ServiceTypeRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * FoodTypeRepository implementa las operaciones de manipulacion de los datos de la clase FoodType
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 09-06-2015
 */
class DbServiceTypeRepository extends EntityRepository implements ServiceTypeRepository
{

	/**
	*	Esta funcion retorna un array que contiene la estructura de todos los servicios
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*
	*	@return array
	*/
	public function getServicesStructure()
	{
		$all = $this->findBy(array('lvl' => 0));
		return $this->getServiceStructure($all);
	}

	/**
	*	Esta funcion retorna un array que contiene la estructura del servicio pasado por parametro
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*
	*	@return array
	*/
	protected function getServiceStructure($services)
    {
		$res = array();
		foreach ($services as $service) {
			$res[$service->getTitle()] = array();
			$res[$service->getTitle()]['id'] = $service->getId();
			$res[$service->getTitle()]['type'] = $service->getType();
			$res[$service->getTitle()]['subservices'] = $this->getServiceStructure($service->getChildren());
		}
		return $res;
	}

	/**
	*	Esta funcion retorna un array con todos los servicios con los id's de los servicios como claves
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*
	*	@return array
	*/
	public function findAllwithKeys()
	{
		$res = array();
		$all = $this->findAll();

		foreach ($all as $service) {
			$res[$service->getId()] = $service;
		}

		return $res;
	}

	/**
	 * Busca ServiceType por sus atributos
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  Array
	 * @return ServiceType
	 */
	public function findOneByArray($array)
	{
		$temp = $this->findOneBy($array);
		if(!empty($temp)){
			return $temp;
		}else{
			return null;
		}
	}

    /**
     *  Busca una instancia de ServiceType Dado un ID
     *
     * @param integer $id
     * @retur PropertyService | null
     */
    public function getById($id)
    {
        return $this->find($id);
    }

}