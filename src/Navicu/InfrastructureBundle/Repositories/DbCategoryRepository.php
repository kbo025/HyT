<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Category;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Navicu\Core\Domain\Repository\CategoryRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Category
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbCategoryRepository extends NestedTreeRepository implements 
	CategoryRepository
{
	/**
	*	Metodo que devuelve un objeto category por su id
	*	@param Integer
	*	@return Category
	*/
	public function find($id)
	{
		return parent::find($id);
	}

	/**
	*	Metodo que devuelve Array de objetos category que representan un ICAO
	*	@return Array
	*/
	public function getAllICAO()
	{
		$request = array();
		$type = $this->findOneBy(array('title'=>'Airport Code Type'));
		$icaos = $this->findBy(array('root' => $type->getId()));

		foreach ($icaos as $icao) {
			array_push($request,array(
					'id' => $icao->getId(),
					'name' => $icao->getTitle()
			));
		}

		return $request;
	}

	/**
	*	Metodo que devuelve Array de objetos category que representan un Currency
	*	@return Array
	*/
	public function getAllCurrency()
	{
		/*$request = array();
		$type = $this->findOneBy(array('title' => 'Currency Type'));
		foreach ($type->getChildren() as $currency) {
			if($currency->getTitle() != 'Bolívar' ){
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
		}*/
		//$currencys = $this->findBy(array('root' => $type->getId()),array('title' => 'ASC'));

		/*foreach ($currencys as $currency) {
			if($currency->getTitle() != 'Bolívar' ){
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

		return $request;*/
	}

    /**
     * Metodo que devuelve un objeto tipo category que cumpla con las condiciones
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
     * Busca Category por sus atributos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param  Array
     * @return Category
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