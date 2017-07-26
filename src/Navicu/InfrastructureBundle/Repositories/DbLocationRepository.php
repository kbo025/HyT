<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Repository\LocationRepository;
use Navicu\Core\Domain\Repository\PropertyRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Location
*
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbLocationRepository extends EntityRepository implements LocationRepository
{
	/**
	*	Este metodo devuelve una estructura de todos los paises con sus estados y a la ves estos con sus ciudades
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*	@version 25/05/2015
	*	@return Array
	*/
	public function getAll($idparent = null)
	{
		$request = array();
        if (!isset($idparent)) {
            $result = $this->findBy(['lvl' => 0, 'visible'=>true], ['title' => 'ASC']);
        } else {
            $result = $this->createQueryBuilder('l')
                ->join('l.parent', 'p')
                ->where('
                    p.id = :idParent and
                    l.visible = true
                ')
                ->setParameters(['idParent' => $idparent])
                ->getQuery()
                ->getResult();
        }

        if (!empty($result)) {
            foreach ($result as $loc) {
                if ($loc->getLvl()==0 and $loc->getTitle() == 'Venezuela') {
                    array_unshift(
                        $request,
                        array(
                            'id' => $loc->getId(),
                            'name' => $loc->getTitle(),
                        )
                    );
                } else {
                    $city = $loc->getCityId();
                    if ($loc->getLvl() == 3 and isset($city))
                        $name = $loc->getTitle().'('.$city->getTitle().')';
                    else
                        $name = $loc->getTitle();
                    if ($loc->getLvl() != 4) {
                        array_push(
                            $request,
                            array(
                                'id' => $loc->getId(),
                                'name' => $name,
                            )
                        );
                    }
                }
            }
        }
		return $request;
	}

	/**
	*	Este metodo Busca y devuelve un Location por su id
	*	@author Gabriel Camacho <kbo025@gmail.com>
	*	@author Currently Working: Gabriel Camacho
	*	@version 28/05/15
	*	@return Location
	*/
	public function find($id)
	{
		return parent::find($id);
	}

    /**
     * Busca RoomFeatureType por sus atributos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Array
     * @return Location
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
     * Busca una localidad dado un codigo de pais un slug y un tipo de localidad
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $codeCountry
     * @param $slug
     * @param $type
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCountrySlugType($codeCountry, $slug, $type)
    {
        return $this->createQueryBuilder('l')
            ->join('l.root','root')
            ->join('l.location_type','type')
            ->where('
                    root.alfa2 = :codeCountry and
                    l.slug = :slug and
                    type.code = :type and
                    l.visible = true
                ')
            ->setParameters(
                array(
                    'codeCountry' => $codeCountry,
                    'slug' => $slug,
                    'type' => $type
                )
            )
            ->getQuery()->getOneOrNullResult();
    }
}