<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\Query\ResultSetMapping;
use Navicu\Core\Domain\Repository\PropertyRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Property
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbPropertyRepository extends DbBaseRepository implements PropertyRepository
{
    /**
     * Esta función es usada para retornar desde la BD el Establecimiento por su slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras
     *
     * @param String $slug
     * @return Object
     */
    public function findBySlug($slug)
    {
        return $this->findOneBy(array('slug' => $slug));
    }

	/**
	 * Retorna un establecimiento dado un slug y códgio del país (VE)
	 *
	 * @param $slug
	 * @param $countryCode
	 * @return mixed
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 */
	public function findBySlugCountryCode($slug, $countryCode)
	{
		return $this->createQueryBuilder('p')
			->join('p.location', 'location')
			->join('location.root', 'country')
			->where('
                country.alfa2 = :country_code and
                p.slug = :slug
                ')
			->setParameters(array(
					'slug' => $slug,
					'country_code' => $countryCode
				)
			)->getQuery()->getOneOrNullResult();
	}

	/**
	 * Obtener los establecimiento dado un comercial
	 *
	 * @param $commercialId
	 * @return array
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @version 06/04/2016
	 */
	public function findByCommercialId($commercialId)
	{
		return $this->createQueryBuilder('p')
			->where('
                p.nvc_profile = :commercialId
                ')
			->setParameters(array(
					'commercialId' => $commercialId
				)
			)->getQuery()->getResult();
	}

	/**
	 * Busca todos los establecimiento menos los de prueba.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @return array
	 */
	public function findAllProperty()
	{
		return  $this->createQueryBuilder('p')
			->where('
                p.id <> 7
                order by p.id
                ')
			->getQuery()->getResult();

	}

	/**
	 * La siguiente consulta aplica filtros de busquedas del estableciento
	 *
	 * @param $filters
	 * @return array
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 */
	public function findByFilters($filters)
	{
		$qb = $this->createQueryBuilder('p');

		$where = null;
		$parameters = [];

		// Filtros por nombre de establecimiento
		if (isset($filters['propertyName'])) {
			$where = $qb->expr()->like('lower(UNACCENT(p.name))', 'lower(UNACCENT(:propertyName))');
			$parameters['propertyName'] = '%' . $filters['propertyName'] . '%';
		}

		// Filtros por fecha de afiliación
		if (isset($filters['affiliationDate'])) {
			$condition = !empty($where) ? ' AND ' : ' ';

			$startDate = new \DateTime($filters['affiliationDate']);
			$endDate = new \DateTime($filters['affiliationDate']);
			$endDate->modify('+1 day');

			$where = $where.$condition.' p.join_date >= :start_date AND p.join_date <= :end_date';
			$parameters['start_date'] = $startDate;
			$parameters['end_date'] = $endDate;
		}

		// Filtros por ciudad
		if (isset($filters['location'])) {

			// Busqueda por ciudad
			$condition = !empty($where) ? ' AND ' : ' ';
			$where = $where.$condition.'( '.$qb->expr()->like('lower(UNACCENT(city.title))', 'lower(UNACCENT(:location))');

			// Busqueda por estado
			$where = $where.' OR '.$qb->expr()->like('lower(UNACCENT(first_parent.title))', 'lower(UNACCENT(:location))');
			$where = $where.' OR '.$qb->expr()->like('lower(UNACCENT(second_parent.title))', 'lower(UNACCENT(:location))');
			$where = $where.' OR ('.$qb->expr()->like('lower(UNACCENT(location.title))', 'lower(UNACCENT(:location))')."
				AND (location.slug IN('tucacas','choroni','colonia-tovar','ocumare-de-la-costa','los-roques'))))";

			$parameters['location'] = '%'.$filters['location'].'%';

			$qb->join('p.location','location')
				->leftJoin('location.location_type', 'type')
				->leftJoin('location.city_id','city')
				->leftJoin('location.parent', 'first_parent')
				->leftJoin('first_parent.parent','second_parent');
		}

		// Filtros por responsable del establecimiento
		if (isset($filters['emailResponsible'])) {

			$condition = !empty($where) ? ' AND ' : ' ';
			$where = $where.$condition.$qb->expr()->like('lower(UNACCENT(user.email))', 'lower(UNACCENT(:emailContact))');
			$parameters['emailContact'] = '%'.$filters['emailResponsible'].'%';

			$qb->join('p.contacts','contact');
			$qb->join('p.owners_profiles','owners_profiles');
			$qb->join('owners_profiles.user','user');
		}

		// Aplicando los ordenamientos
		if (isset($filters['order'])) {
            switch ($filters['orderBy']) {
                case 'propertyName':
                    $qb->orderBy('p.name',$filters['order']);
                    break;
                case 'affiliationDate':
                    $qb->orderBy('p.join_date', $filters['order']);
                    break;
                case 'commercialProfile':
                    $qb->join('p.nvc_profile','nvc_profile');
                    $qb->orderBy('nvc_profile.full_name',$filters['order']);
                    break;
                case 'emailResponsible':
                    $qb->join('p.contacts','contact');
                    $qb->join('p.owners_profiles','owners_profiles');
                    $qb->join('owners_profiles.user','user');
                    $qb->orderBy('user.email', $filters['order']);
                    break;
                case 'billing': break;
                case 'numberReservation': break;
            }
		}

		// Filtros por ejecutivo de venta
		// Si la sesión es de un ejecutivo de venta
		if (isset($filters['user'])) {
			// Busqueda por ciudad
			$condition = !empty($where) ? ' AND ' : ' ';
			$where = $where.$condition.'p.nvc_profile = :commercialId ';
			$parameters['commercialId'] = $filters['user'];

		} else if (isset($filters['commercialProfile'])){

			$condition = !empty($where) ? ' AND ' : ' ';
			$where = $where.$condition.$qb->expr()->like('lower(UNACCENT(nvc_profile.full_name))', 'lower(UNACCENT(:commercialProfile))');
			$parameters['commercialProfile'] = '%'.$filters['commercialProfile'].'%';

			$qb->join('p.nvc_profile','nvc_profile');
		}

		if ($where)
			$qb->where($where);

		return  $qb
			->setParameters($parameters)
			->getQuery()
			->getResult();
	}

	/**
	 * Búsqueda de establecimientos sin disponibilidades ni tárifas cargadas
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param null $slug
	 * @return array
	 * @version 29/08/2016
	 */
	public function findWithoutAvailabily($startDate, $endDate, $nvcProfileId = null)
	{
		// Consulta SQL en string
		$query = "
		SELECT DISTINCT
			P.id,
			P.slug as slug,
			P.name as name,
			P.join_date as join_date,
			P.location,
			--DP.date as daily_date,
			case
				WHEN L.city_id IS NOT NULL THEN CI.title
				ELSE L.title
			END as city,
			Nvc.full_name as responsible
		FROM property  P
			INNER JOIN room AS R ON R.property_id = P.id
			INNER JOIN pack AS PA ON Pa.room_id = R.id
			LEFT JOIN daily_room AS DR ON (DR.room_id = R.id AND DR.DATE >= :start_date AND DR.DATE <=  :end_date)
			LEFT JOIN daily_pack AS DP ON (DP.pack_id = Pa.id AND DP.DATE >= :start_date AND DP.DATE <=  :end_date)
			LEFT JOIN location as L ON L.id = P.location
			LEFT JOIN location as CI ON CI.id = L.city_id
			LEFT JOIN nvc_profile AS Nvc on Nvc.id = p.nvc_profile_id
		WHERE
			((DP.pack_id is null or DP.is_completed = false) and (DR.room_id is null or DR.is_completed = false)) --OR
			--((DP.pack_id is null or DP.is_completed = false) and DR.is_completed = true)
			AND NOT EXISTS(
				select p1.id, r1.id, pa1.id,dr1.is_completed, dr1.id, dp1.is_completed, dp1.id from property as p1

					inner join room as r1 on (r1.property_id = p1.id)
					inner join pack as pa1 on (pa1.room_id = r1.id)
					right join daily_room as dr1 on (dr1.room_id = r1.id and dr1.DATE >= :start_date AND dr1.DATE <= :end_date)
					right join daily_pack as dp1 on (dp1.pack_id = pa1.id and dp1.DATE >= :start_date AND dp1.DATE <= :end_date)

					where
					P.slug = p1.slug and
					(dp1.is_completed = true) AND
					(dr1.is_completed = true)
				)
		ORDER BY p.join_date ASC
		";

		// Aplicando el mapeo del Select de SQL
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('NavicuDomain:Property','P');
		$rsm->addScalarResult('id','propertyId','integer');
		$rsm->addScalarResult('slug','slug','string');
		$rsm->addScalarResult('name','propertyName','string');
		$rsm->addScalarResult('join_date','join_date','datetime');
		$rsm->addScalarResult('daily_date','daily_date','date');
		$rsm->addScalarResult('city','city','string');
		$rsm->addScalarResult('responsible','responsible','string');

		if ($nvcProfileId)
			$query = $query.' AND Nvc.id = :nvc_profile_id ';

		$naviteQuery = $this->getEntityManager()->createNativeQuery($query,$rsm);

		if ($nvcProfileId)
			$naviteQuery->setParameter('nvc_profile_id',$nvcProfileId);

		$naviteQuery->setParameter('start_date', $startDate);
		$naviteQuery->setParameter('end_date', $endDate);

		return $naviteQuery->getResult();
	}

	/**
	 * Busca el ultimo id
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @return array
	 */
	public function getLastId()
	{
		return  $this->createQueryBuilder('p')
			->select('MAX(p.id)')
			->getQuery()->getOneOrNullResult();
	}

    /**
     * Funcion encargada de obtener y filtrar el detalle de las reservas de un establecimiento
     *
     * @param $param
     * @param $searchVector
     * @return mixed
     * @version 02/02/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function getPropertyDetailFromFilter($param, $searchVector)
    {
        $arrayOfMatch[] = "date_check_in";
        $arrayOfMatch[] = "date_check_out";
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], $searchVector);
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], $searchVector);
        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);
        $additionalCriteria = $additionalCriteria . ' slug = ' . '\'' .$param['slug'] . '\''; //HACK para buscar por slug, debe hacerse mas generico
        return $this
            ->select('
            	id,
	            slug,
	            date_check_in,
	            date_check_out,
	            status,
	            number_rooms,
	            public_id,
	            full_name,
	            net_rate,
	            total_to_pay
            ')
            ->from('admin_property_detail_resevation_view')
            ->where($tsQuery, $additionalCriteria)
            ->paginate($param['page'], $param['number_result'])
            ->order($param['order_by'], $param['order_type'], $tsRank)
            ->getResults();

    }

    /**
     * Funcion encargada de realizar la busqueda y/o filtrado del listado de establecimiento
     *
     * @param $param array, array con la solicitud de la busqueda
     *
     * @param string $searchVector, nombre del vector de busqueda
     * @return mixed
     * @version 19/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function affiliatePropertyByFilter($param, $searchVector)
    {
        $arrayOfMatch[] = "join_date";
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], $searchVector);
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], $searchVector);

        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);

        return $this
            ->select('
            	id,
                name,
                join_date,
                nvc_profile_id,
                nvc_profile_name,
                admin_email,
                total_to_pay,
                number_reservation,
                location,
                discount_rate,
                credit_days,
                slug
            ')
            ->from('admin_property_view')
            ->where($tsQuery, $additionalCriteria)
            ->paginate($param['page'], $param['number_result'])
            ->order($param['order_by'], $param['order_type'], $tsRank)
            ->getResults();
    }

    /**
     * Búsqueda de establecimientos sin disponibilidades ni tárifas cargadas mediante filtros
     *
     * @param $param
     * @param $searchVector
     * @return mixed
     */
    public function findWithoutAvailabilyWithFilter($param, $searchVector)
    {
        $arrayOfMatch[] = "join_date";
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);

        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], $searchVector);
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], $searchVector);
        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);

        return $this
            ->select("
                id,
                slug,
                name,
                join_date,
                city,
                full_name
            ")
            ->from("admin_property_without_availability_view")
            ->where($tsQuery, $additionalCriteria)
            ->paginate($param['page'], $param['number_result'])
            ->order($param['order_by'], $param['order_type'], $tsRank)
            ->getResults();
    }

    /**
     * Funcion de prueba para las pruebas sobre la vista de admin_property_view
     * @param $param
     * @return mixed
     */
    public function propertyFilterTest($param)
    {
        // Incluimos los nombre de las columnas por las cuales se puede realizar la busqueda de fechas
        $arrayOfMatch[] = "join_date";
//        $arrayOfMatch[] = "check_out";

        // Separamos las palabras en:
        // - las que van a la clausula "where" sin pasar por el vector y
        // - las que van a la clausula del where pero al "ts_query"
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], "search_vector");
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], "search_vector");

        // Agregamos a la normalizacion de las palabras la busqueda para incluir
        // un array que contiene los nombres de las columnas de la BD donde buscar
        // las fechas
        // Ej: check_in:2017-01-01:2017-03-01 (check_in desde 2017-01-01 hasta 2017-03-01)
        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);

        return $this
            ->select('*')
            ->from('admin_property_view')
            ->where($tsQuery, $additionalCriteria)
            ->paginate(1,10)
            ->order("id", "desc",$tsRank)
            ->getResults();

        // Para funcionar mediante normalizacion de palabras
        $separatedWords = $this->separateByType($param['search']);
        $this->normalizedWhereClause($separatedWords['tsQuery']);

        return $this
            ->select('*')
            ->from('adminPropertyView')
            ->where("commercialname like '%".$separatedWords['tsQuery']."%'")
            ->paginate(1,10)
            ->order($param['orderBy'], $param['orderType'])
            ->getResults();

        // Para funcionar mediante la busqueda por rango de fechas
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);
        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);

        return $this
            ->select('*')
            ->from('adminPropertyView')
            ->where($additionalCriteria)
            ->paginate(1,10)
            ->order($param['orderBy'], $param['orderType'])
            ->getResults();

        // Para funcionar mediante consulta basica
        $this->normalizedWhereClause($param['search']);

        return $this
            ->select('*')
            ->from('Property')
            ->where("slug like '%".$param['search']."%'")
            ->paginate(1,10)
            ->order($param['orderBy'], $param['orderType'])
            ->getResults();
    }

    public function getInconsistentFolders()
    {
        $sql = "
            select DISTINCT
              p.id,
              p.slug,
              split_part(d.filename, '/',2) as oldSlug
            FROM
              property p
              INNER JOIN property_gallery pg
                on p.id = pg.property
              INNER JOIN property_images_gallery pgi
                on pg.id = pgi.property_gallery_id
              INNER JOIN document d
                ON pgi.image_documet_id = d.id
            where p.slug <> split_part(d.filename, '/',2);

        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeatured()
    {
	    return $this->createQueryBuilder('u')
	                ->where('
                u.featured_home = true

                ')->getQuery()->getResult();

    }

    public function getLowestPrice($id)
    {
        $sql = "select min(sell_rate) 
        from daily_pack where pack_id in (select id from pack where room_id in (select id from room where property_id = :id))
        and date >= date(current_timestamp);";

        $params['id'] = $id;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
