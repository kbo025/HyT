<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\Helper;

/**
 * SearchEngineRepository implementa una serie de funciones para el
 * manejo de las consultas SphinxQL.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SearchEngineRepository
{
    private $conn = null;

	/**
	 * Se hace uso de un constructor para crear y configurar la conexión
	 * con el motor de busqueda.
	 *
	 * @return void
	 */
    public function __construct()
    {
        global $kernel;
        $searchEngineConfig = $kernel->getContainer()->getParameter('search_engine');

        $this->conn = new Connection();
        $this->conn->setParams(array(
            'host' => $searchEngineConfig['host'],
            'port' => $searchEngineConfig['port']
        ));
    }

	/**
	 * La siguiente función retorna una lista de establecimientos
	 * dado el slug del establecimiento.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param $slug
	 * @return array
	 */
	public function resultPropertiesBySlug($slug)
	{
		return SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					property
				where
					slug = '$slug'
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();
	}

	/**
	 * La siguiente función retorna el lista de establecimientos
	 * dado el codigo de un pais ($countryCode), el id de una localidad ($idLocation) y el nivel ($lvl)
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $countryCode
	 * @param $idLocation
	 * @param $lvl
	 * @return array
	 * @version 11/02/2016
	 */
	public function resultPropertiesByDestiny($data)
	{
		$byStar = isset($data["star"]) ? "star = ".$data["star"]." and" : null;
		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					property
				where
					$byStar
					alfa = '".$data["countryCode"]."' AND
					dependency.".$data["lvlDestiny"].".id = ".$data["idDestiny"]." 
					".$data["acceptsChild"]."
					ORDER BY rating DESC
					LIMIT 0,1000
				")->execute();

		// Si el establecimiento no pertenece a la division del país
		// Se busca en las depedencias
		if (!$result) {
			$result = SphinxQL::create($this->conn)
				->Query(
					"select
					*
				from
					property
				where
					$byStar
					alfa = '".$data["countryCode"]."' AND
					division.".$data["lvlDestiny"].".id = ".$data["idDestiny"]." 
					".$data["acceptsChild"]."
					ORDER BY rating DESC
					LIMIT 0,1000
				")->execute();
		}

		return $result;
	}

    /**
	 * Consulta en SphinxQL para buscar las coincidencia por id de destinos,
	 * la fecha inicial y la cantidad de dias dentro del index de dailyRoom.
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $data
	 * @return Array
	 */
	public function resultDailyRoomByRooms($data)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					dailyRoom
				where
					date = ".$data["startDate"]." and
					idRoom in ".$data["idRooms"]." and
					continuousRoomAvailability >= ".$data["dayCount"]." and
					cutOff <= ".$data["availabilityCutOff"]."
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

    /**
	 * Consulta en SphinxQL para buscar las coincidencia por id de destinos,
	 * la fecha inicial y la cantidad de dias dentro del index de dailyRoom.
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Integer $id
	 * @return Array
	 */
	public function resultRoomById($id)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					room
				where
					id = $id 
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

    /**
	 * Consulta en SphinxQL para buscar las coincidencia por id de la
	 * habitación un rango de fecha dentro del index de dailyRoom.
	 * Busqueda dailyRooms para un room dentro de un rango de fecha.
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $data
	 * @param Integer $id
	 * @return Array
	 */
	public function resultDailyRoomByDateRank($data, $idRoom)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					dailyRoom
				where
					date >= ".$data["startDate"]." and
					date < ".$data["endDate"]." and
					idRoom = $idRoom 
					ORDER BY date ASC
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

    /**
	 * Consulta en SphinxQL para buscar las coincidencia por id de
	 * habitación, fecha inicial, cantidad de dias, minimo y maximo
	 * de noche dentro del index de dailyPack. Busqueda de los dailyPack
	 * especificos para una fecha inicial de una habitación.
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $data
	 * @param Integer $idRoom
	 * @return Array
	 */
	public function resultDailyPackByPackages($data)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					dailyPack
				where
					date = ".$data["startDate"]." and
					idPack in ".$data["idPackages"]." and
					continuousPackAvailability >= ".$data["dayCount"]." and
					minNight <= ".$data["dayCount"]."  and
					maxNight >= ".$data["dayCount"]." and
					cta = 'f' and
					closeOut = 'f'
					ORDER BY idPack desc
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

    /**
	 * Consulta en SphinxQL para buscar las coincidencia por id de servicio
	 * un rango de fecha dentro del index de dailyPack. Busqueda dailyPackages
	 * para un pack dentro de un rango de fecha.
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $data
	 * @param Integer $idPack
	 * @return Array
	 */
	public function resultDailyPackByDateRank($data, $idPack)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					dailyPack
				where
					date >= ".$data["startDate"]." and
					date <= ".$data["endDate"]." and
					idPack = $idPack 
					ORDER BY date ASC
					LIMIT 0, 1000
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

    /**
	 * Consulta en SphinxQL los precios de los pack dada un
	 * conjunto de idPAck
	 *
	 * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Array $idPackages
	 * @return Array
	 */
	public function resultPricebyPackages($idPackages)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					packPriceRange
				where
					idPack in $idPackages 
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

	/**
	 * Consulta en SphinxQL para buscar el menor de los precio
	 * dentro de un pack
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param Integer $idPack
	 * @return Array
	 */
	public function resultPriceRangebyPack($idPack)
	{
        $result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					packPriceRange
				where
					idPack = $idPack 
					OPTION ranker=bm25
				")->execute();

		return $result;
	}

	/**
	 * La siguiente función retorna el lista de establecimientos
	 * dado un cojunto de coordenadas.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param $coordenate
	 * @return array
	 */
	public function propertiesByCoordenate($coordenate)
	{
		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*, CONTAINS(GEOPOLY2D($coordenate), latitude, longitude) AS inside
				from
					property
				where
					inside=1
					ORDER BY rating DESC
					LIMIT 0,1000
				")->execute();

		return $result;
	}

	/**
	 * La siguiente función retorna el listado de destinos habilitados
	 * por los establecimientos dentro de la BD.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @return array
	 */
	public function destinationsList()
	{
		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					destinations
				LIMIT 0,1000
				")->execute();

		return $result;
	}

	/**
	 * La siguiente función retorna el listado de reservas
	 * para la Agencias de Viajes
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @return array
	 */
	public function getListReservationAAVV($data)
	{
		$status = $data["status"] ? "status = '".$data["status"]."' and" : null;
		$search = $data["search"] ? "MATCH('@* ".$data["search"]."') and" : null;
		$order = $data["orderBy"] ? "ORDER BY ".$data["orderBy"]." ".$data["orderType"] : null;
		$endDate = $data["endDate"] ? $data["dateType"]." <= ".$data["endDate"]." and" : null;

		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					aavvListReservation
				where
					aavvId = ".$data["aavvId"]." and
					$search
					$status
					$endDate
					".$data["dateType"]." >= ".$data["startDate"]."
					$order
					LIMIT 0,1000
				")->execute();

		return $result;
	}

//	"@* cas* @* bus*"
//
    /**
     * Funcion encargada de realizar un filtro por los parametros de busqueda, concatenando por
     * cada palabra un '@*' y finalizando con un '*'. El array ingresado es de este tipo:
     *
     * 'search' : {'bla', 'bla', 'bla'}
     * orderBy: funciona con estos campos <name>,<joindate>,<adminemail>,<commercialname>,<numberrsrv>,<totaltopay>,<city>
     * 'orderBy' : 'name'
     * orderType: funciona con estos campos <asc>,<desc>
     * 'orderType': 'asc'
     *
     * @return array
     */
    public function listPropertiesByFilter($filters)
    {
        $search = "MATCH('";
        $concat = "";
        $entry = false;

        // Si se esta solicitando una busqueda por palabras
        if (!empty($filters['search'])) {
            foreach ($filters["search"] as $searchByTokens) {
                $concat = "@* " . $searchByTokens . "* " . $concat;
                $entry = true;
            }
            $search = $search . $concat . "')";
        }

        // Si 'entry' no cambio a true es porque no consiguio un parametro con el cual realizar la busqueda
        if ($entry)
            $searchWhere = "WHERE ".$search;
        else
            $searchWhere = null;

        $order = $filters["orderBy"] ? "ORDER BY ".$filters["orderBy"]." ".$filters["orderType"] : null;

        $result = SphinxQL::create($this->conn)
            ->Query(
                "SELECT
					*
				FROM
					propertiesFilter
				"
				.
                $searchWhere
                .
                $order
                ."
				LIMIT 0,1000
				")->execute();
        return $result;
    }
	/**
	 * La siguiente función retorna el listado de destinos dentro de
     * navicu dado un conjunto de palabras.
	 *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
	 * @return array
	 */
	public function getLocationByWords($data)
	{
		if (!$data["words"])
			return null;

		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*
				from
					location
				WHERE 
					MATCH('@name ".$data["words"]."') and
					lvl <> '0' and
					alfa = '".$data["alfa"]."'and
					countProperty > 0 
					order by lvl ASC
					LIMIT 0,1000 
				")->execute();

		return $result;
	}

	/**
	 * La siguiente función retorna el listado de establecimiento
     * dentro de navicu dado un conjunto de palabras.
	 *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return array
	 */
	public function getPropertyByWords($data)
	{
		if (!$data["words"])
			return null;

		$result = SphinxQL::create($this->conn)
			->Query(
				"select
					*,
					100 as lvl
				from
					property
				WHERE 
					MATCH('@name ".$data["words"]."') and
					alfa = '".$data["alfa"]."' 
					LIMIT 0,1000 
				")->execute();

		return $result;
	}
}
