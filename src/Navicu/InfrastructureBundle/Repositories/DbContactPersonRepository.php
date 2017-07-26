<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ContactPersonRepository;

/**
 * DbContactPersonRepository
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Juan Pablo Osorio V
 */
class DbContactPersonRepository extends EntityRepository implements ContactPersonRepository
{
	private $list_type = [
		['id' => 0, 'name' => 'Contabilidad', 'required' => true],
		['id' => 1, 'name' => 'Reservas', 'required' => true],
		['id' => 2, 'name' => 'Gerencia', 'required' => true],
		['id' => 3, 'name' => 'Operaciones', 'required' => false],
		['id' => 4, 'name' => 'Atención al cliente', 'required' => false]
	];

	public function getListType()
	{
		return $this->list_type;
	}

	public function getIdType($string)
	{
		$res = false;
		foreach($this->list_type as $type) {
			if($string == $type['name']) {
				$res = $type['id'];
			}
		}

		return $res;
	}

	public function getNameType($id)
	{
		$res = false;
		foreach($this->list_type as $type) {
			if($id == $type['id']) {
				$res = $type['name'];
			}
		}

		return $res;
	}

	public function getRequiredType($id)
	{
		$res = false;
		foreach ($this->list_type as $type) {
			if($id == $type['id']) {
				$res = $type['required'];
			}
		}

		return $res;
	}

    /**
     * Busca RoomFeatureType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return ContactPerson
     * @version 26/06/2015
     */
    public function findOneByArray($array)
    {
        $temp = $this->findOneBy($array);

		return !empty($temp) ? $temp : null;
    }
}