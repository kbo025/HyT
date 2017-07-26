<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\RoomTypeRepository;

/**
 * RoomTypeRepository  implementa los metodos de manipulacion de datos y comunicacion con BD de la clase RoomType
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class DbRoomTypeRepository extends EntityRepository implements 
	RoomTypeRepository
{

	/**
	*	devuelve la estructura de arbol de tipos de habitaciones en arrays clave valor de forma $id => $title
	*	@return array 
	*/
	public function getRoomsTypesStructure()
	{
		$types = $this->findBy(array('lvl' => 0),array('title' => 'ASC'));
		$res = array();

		foreach ($types as $type) {
			$subtypes = $this->getRoomTypeStructure($type);

			if (empty($subtypes)) {
				array_push($res,array(
					'id' => $type->getId(),
					'name' => $type->getTitle(),
					'category' => $type->getCategory()
                ));
			} else {
				array_push($res,array(
					'id'=>$type->getId(),
					'name'=>$type->getTitle(),
					'subtypes'=>$subtypes,
					'category' => $type->getCategory()
				));
			}
		}

		return $res;
	}

	/**
	*	Esta funcion retorna un array que contiene la estructura del typo de habitacion pasado por parametro
	*	@return array
	*/
	protected function getRoomTypeStructure($type)
	{
		$res = array();

		foreach ($type->getChildren() as $child) {
			$types = $this->getRoomTypeStructure($child);

			if (empty($types)) {
				array_push($res,array(
					'id'=>$child->getId(),
					'name'=>$child->getTitle()
                ));
			} else {
				array_push($res,array(
						'id'=>$child->getId(),
						'name'=>$child->getTitle(),
						'nameRooms'=>$types
				));
			}
		}

		return $res;
	}

    /**
     * Busca RoomType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return RoomFeatureType
     * @version 26/06/2015
     */
    public function findOneByArray($array)
    {
        $temp = $this->findOneBy($array);

        if (!empty($temp)) {
            return $temp;
        }

        return null;
    }

    /**
     * devuelve todos los tipos de habitaciones en un array donde la clave es el id del tipo
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     * @version 12/08/2015
     */
    public function findAllWithKeys()
    {
        $all = $this->findAll();
        $res = array();
        foreach($all as $type)
        {
            $res[$type->getId()] = $type;
        }
        return $res;
    }
}