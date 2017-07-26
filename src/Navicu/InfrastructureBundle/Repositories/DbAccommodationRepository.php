<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\AccommodationRepository;

/**
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 09-06-2015
 */
class DbAccommodationRepository extends EntityRepository implements AccommodationRepository
{

	/**
	*	Metodo que devuelve un array Clave Valor en donde la clave es el id del registro y el valor es el titulo del servicio
	*	@return array
	*/
	public function getAllWithKeys()
	{
		$res = array();
		$all = $this->findAll();

		foreach ($all as $accommodation) {
			$res[$accommodation->getId()] = $accommodation;
		}

		return $res;
	}

	public function getAccommodationList()
	{
		$res = array();
		$all = $this->findBy(array(),array('title' => 'ASC'));

		foreach ($all as $accommodation) {
			array_push($res, array(
                'id' => $accommodation->getId(),
                'name' => $accommodation->getTitle()
            ));
		}

		return $res;		
	}

    /**
     * Busca RoomFeatureType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return Accommodation
     * @version 26/06/2015
     */
    public function findOneByArray($array)
    {
        $temp = $this->findOneBy($array);

        return !empty($temp) ? $temp : null;
    }

    public function getById($id)
    {
        $temp = $this->findOneBy(['id' => $id]);

        return !empty($temp) ? $temp : null;
    }

    public function getByName($name)
    {
        $temp = $this->findOneBy(['title' => $name]);

        return !empty($temp) ? $temp : null;
    }
}

/* End of file */