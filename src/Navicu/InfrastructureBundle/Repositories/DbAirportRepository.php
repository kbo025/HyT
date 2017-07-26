<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Airport;
use Navicu\Core\Domain\Repository\AirportRepository;

/**
* se declaran los metodos y funciones que implementan
* el repositorio de la entidad Agreement
*
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
* @version 26/06/17
*/
class DbAirportRepository extends DbBaseRepository implements AirportRepository
{
     /**
     * La siguiente función retorna un listado de reservas
     * dado un conjunto de parametros y el uso de una vistaSQL.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $data
     * @return array
     */
    public function findByWords($data)
    {

        // Separamos las palabras en:
        // - las que van a la clausula "where" sin pasar por el vector y
        // - las que van a la clausula del where pero al "ts_query"
        $separatedWords = $this->separateByType($data['word'], []);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], "vector");
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], "vector");

        // Agregamos a la normalizacion de las palabras la busqueda para incluir
        // un array que contiene los nombres de las columnas de la BD donde buscar
        //$additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);
        $additionalCriteria = "country_code = '".$data['country']."'";

        //die(var_dump($additionalCriteria));
        

        return $this
            ->select('
                iata,
                name,
                country_name as country,
                location_name as city
                '
            )
            ->from('web_fligths_autocompleted_view')
            ->where($tsQuery,$additionalCriteria)
            ->order(null, '', $tsRank)
            ->paginate(1,5)
            ->getResults();
    }

	public function findByLocation($id)
	{
		return $this->createQueryBuilder('u')
		            ->where('
                u.location = :id
				AND u.lat is not null
                ')
		            ->setParameters(
			            array(
				            'id' => $id
			            )
		            )->getQuery()->getResult();
	}
}