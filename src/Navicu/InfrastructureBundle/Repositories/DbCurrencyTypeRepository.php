<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\CurrencyType;
//use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Navicu\Core\Domain\Repository\CurrencyTypeRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad CurrencyType
*
* @author Gabriel Camcaho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camcaho <kbo025@gmail.com>
* @version 31/07/15
*/
class DbCurrencyTypeRepository extends EntityRepository implements 
	CurrencyTypeRepository
{
	/**
	*	Metodo que devuelve un objeto CurrencyType por su id
	*	@param Integer
	*	@return Category
	*/
	public function find($id)
	{
		return parent::find($id);
	}

	/**
	*	Metodo que devuelve Array de objetos category que representan un Currency
	*	@return Array
	*/
	public function getAllCurrency()
	{
		$request = array();
		$all = $this->findBy(array(),array('title' => 'ASC'));
		foreach ($all as $currency) {
			if ($currency->getTitle() != 'Bolívar' ) {
				array_push($request, array(
						'id' => $currency->getId(),
						'name' => $currency->getTitle()
	            ));
	        } else {
				array_unshift($request, array(
						'id' => $currency->getId(),
						'name' => $currency->getTitle()
	            ));	        	
	        }
		}
		return $request;
	}

    /**
     * Metodo que devuelve un objeto tipo CurrencyType que cumpla con las condiciones
     *
     * @param array $criteria
     *
     * @return Category
     */
	public function findOneBy(array $criteria)
	{
		return parent::findOneBy($criteria);
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
        if(!empty($temp)){
            return $temp;
        }else{
            return null;
        }
    }

	/**
	 * Funcion para retornar todos los objetos currency ordenados por nombre que esten activos
	 * 
	 * @return array
	 * @author Isabel Nieto <isabelcnd@gmail.com>
	 * @version 18/08/2016
	 */
	public function getAllCurrencyActive() {
		return $this->createQueryBuilder('ct')
			->where('
                ct.active = true
                ')
			->orderBy('ct.title','asc')
			->getQuery()->getResult();
	}

	/**
	 * Retorna todos los objetos de tipo currency
	 * @return array
	 */
	public function findAll()
	{
		return parent::findAll();
	}
}