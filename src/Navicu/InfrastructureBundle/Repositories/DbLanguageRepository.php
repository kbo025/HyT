<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\LanguageRepository;

/**
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 09-06-2015
 */
class DbLanguageRepository extends EntityRepository implements LanguageRepository
{

	/**
	*	Metodo que devuelve un array Clave Valor en donde la clave es el id del registro y el valor es el titulo del servicio
	*	@return array
	*/
	public function findAllWithKeys()
	{
		$res = array();
		$all = $this->findAll();

		foreach($all as $lan) {
			$res[$lan->getId()] = $lan;
		}

		return $res;
	}

	public function getLanguagesList()
	{
		$res = array();
		$all = $this->findBy(array(),array('native' => 'ASC'));

		foreach($all as $lan) {
			if($lan->getNative() != 'español') {
				array_push($res, array('id'=>$lan->getId(),'name'=>$lan->getNative()));
			} else {
				array_unshift($res, array('id'=>$lan->getId(),'name'=>$lan->getNative()));
			}
		}

		return $res;
	}

    /**
     * Busca RoomFeatureType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return Language
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
}