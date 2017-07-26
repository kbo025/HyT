<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\RoomFeatureTypeRepository;
use Navicu\Core\Domain\Model\Entity\RoomFeatureType;

/**
 * RoomFeatureTypeRepository  implementa los metodos de manipulacion de datos y comunicacion con BD de la clase RoomFeatureType
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class DbRoomFeatureTypeRepository extends EntityRepository implements
	RoomFeatureTypeRepository
{
	/**
	 *	retorna una lista de todos las caracteristicas de tipo espacio fisico que se registraron
	 *	@return array
	 */
	public function getSpacesList()
	{
		$res=array();
		$spaces = $this->findBy(array('type'=>0),array('title'=>'ASC'));
		foreach ($spaces as $space) {
			array_push($res,array(
					'id'=>$space->getId(),
					'name'=>$space->getTitle()
					)
			);
		}
		return $res;
	}

	/**
	 *	retorna una lista de todos las caracteristicas de tipo servicio que se registraron
	 *	@return array
	 */
	public function getServicesList($type = null)
	{
		$res=array();
		if (!isset($type)){
			$services = $this->findBy(array('type'=>1),array('title'=>'ASC'));
		} else {
			$parent = $this->findOneBy(array('title'=>$type));
			$services = isset($parent) ? $parent->getChildren() : array();
		}
		foreach ($services as $service) {
			array_push($res,array(
					'id'=>$service->getId(),
					'name'=>$service->getTitle()
				)
			);
		}
		return $res;
	}

	/**
	 *	retorna una array de objetos en el que cada indice es el id del mismo
	 *	@return array
	 */
	public function getAllWithKeys()
	{
		$res=array();
		$fets = $this->findAll();
		foreach ($fets as $fet) {
			$res[$fet->getId()] = $fet;
		}
		return $res;	
	}

	/**
     * Busca RoomFeatureType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return RoomFeatureType
     * @version 26/06/2015
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
     * devuelve todos los tipos de caracteristicas de habitaciones en un array donde la clave es el id del tipo
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