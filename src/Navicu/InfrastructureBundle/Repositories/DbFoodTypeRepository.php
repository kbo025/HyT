<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\FoodTypeRepository;

/**
 * FoodTypeRepository implementa las operaciones de manipulacion de los datos de la clase FoodType
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 09-06-2015
 */
class DbFoodTypeRepository extends EntityRepository implements FoodTypeRepository
{
	/**
	*	Metodo que devuelve un array Clave Valor en donde la clave es el id del registro y el valor es el titulo del servicio
	*	@return array
	*/
	public function findAllWithKeys()
	{
		$res = array();
		$all = $this->findAll();

		foreach ($all as $food) {
			$res[$food->getId()] = $food;
		}

		return $res;
	}

	public function findListAll()
	{
		$res = array();
		$all = $this->findAll();
		foreach ($all as $food) {
			array_push(
				$res,
				array(
					'id' => $food->getId(),
					'name' => $food->getTitle()
				)
			);
		}

		return $res;
	}

    /**
     * Devuelve una instancia dado un id
     *
     * @param integer $id
     * @return Object
     */
    public function getById($id)
    {
        return $this->find($id);
    }
}