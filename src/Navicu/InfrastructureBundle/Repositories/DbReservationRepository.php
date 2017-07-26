<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ReservationRepository;
use Navicu\Core\Domain\Model\Entity\Reservation;

/**
 * La clase se declaran los métodos y funciones que implementan
 * el repositorio de la entidad Reservation.php
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 20/08/15
 */
class DbReservationRepository extends DbBaseRepository implements ReservationRepository
{
    /**
     * Obtiene todas las reservas
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 22/01/2016
     */
    public function findAllReservations($reservationStatus)
    {
        return $this->findAll(array('status'=>$reservationStatus));
    }

    /**
     * Obtiene las reservaciones segun su status
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 22/01/2016
     */
    public function findAllByStatus($reservationStatus)
    {
        $condition = isset($reservationStatus) ? ['status' => $reservationStatus] : [];
        return $this->findBy($condition);
    }

    public function findAllReservationToAdmin($array)
    {
        $parameters = [];
        $reservationDateSql = '';
        $checkInSql = '';
        $checkOutSql = '';
        $wordSql = '';
        $nvcProfile = '';

        $qb = $this->createQueryBuilder('p')->join('p.property_id', 'property');

        $statusSql = $array['reservationStatus']!=4 ? 'p.status = :status' : 'p.status < :status';
        $parameters['status'] = $array['reservationStatus'];

        if (isset($array['checkIn'])) {
            $checkInSql = ' AND p.date_check_in = :checkIn';
            $parameters['checkIn'] = $array['checkIn'];
        }
        if (isset($array['checkOut'])) {
            $checkOutSql = ' AND p.date_check_out = :checkOut';
            $parameters['checkOut'] = $array['checkOut'];
        }
        if (isset($array['reservationDate'])) {
            $reservationDateSql = ' AND p.reservation_date = :reservationDate';
            $parameters['reservationDate'] = $array['reservationDate'];
        }

        if (isset($array['word'])) {
            $propertyWordSql = $qb->expr()->like('lower(UNACCENT(property.name))', 'lower(UNACCENT(:word))');
            $clientWordSql = $qb->expr()->like('lower(UNACCENT(client.full_name))', 'lower(UNACCENT(:word))');
            $idReservationSql = $qb->expr()->like('lower(UNACCENT(p.public_id))', 'lower(UNACCENT(:word))');
            $parameters['word'] = '%'.$array['word'].'%';
            $wordSql = ' AND (' . $propertyWordSql . ' OR ' . $clientWordSql . ' OR ' . $idReservationSql . ')';
        }

        if ($array['user']->getNvcProfile() and $array['user']->hasRole('ROLE_SALES_EXEC')) {
            $nvcProfile = ' AND property.nvc_profile = :user';
            $parameters['user'] = $array['user']->getNvcProfile();
        }

        $sql = $statusSql.$checkInSql.$checkOutSql.$reservationDateSql.$wordSql.$nvcProfile;

        return  $qb
            ->where ($sql)
            ->join('p.client_id','client')
            ->setParameters ($parameters)
            ->getQuery()
            ->getResult();

    }

    /**
     * Busca una reserva segun el publicId
     *
     * @author Jose Agraz <jaagraz@navicu.com>
     * @param  Array
     * @return TempOwner
     */
    public function findByPublicId($idReservation)
    {
        $temp = $this->findOneBy(['public_id'=>$idReservation]);

        if (!empty($temp)) {
            return $temp;
        }

        return null;
    }

    /**
    * Busca una reserva dado un conjuto de parametros
    * en un arreglo.
    *
    * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
    * @param  Array $data
    * @return Array
    */
    public function findOneByArray($data)
    {
        return $this->findOneBy($data);
    }

    /**
     * La siguiente función retorna el conjunto de reservas
     * con status de Pre-Reserva cuyo tiempo supere las
     * 48 horas.
     *
    * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param string $dateNow
     * @return Array
     */
    public function findByTransferReservationExpiration($dateNow)
    {
        return $this->createQueryBuilder('r')
            ->where("
                :dateNow - r.reservation_date >=  '1 day' and
                (r.status = 0 or r.state = 0)
                ")
            ->setParameters(
                array(
                    'dateNow' => $dateNow
                )
            )
            ->getQuery()->getResult();
    }

    /**
     * La siguiente función retorna el conjunto de reservas
     * con states null cuyo tiempo supere las
     * 24 horas.
     *
    * @author Gabriel Camacho <kbo025@gmail.com>
     * @param string $dateNow
     * @return Array
     */
    public function findExpiredReservations($dateNow)
    {
        return $this->createQueryBuilder('r')
            ->where("
                :dateNow - r.reservation_date >=  '1 day' and
                (r.state is null or r.status is null )
                ")
            ->setParameters(
                array(
                    'dateNow' => $dateNow
                )
            )
            ->getQuery()->getResult();
    }

    /**
     *  encuentra todas las reservaciones de un hotels
     *
     * @param array $filters
     * @return array
     */
    public function findReservationsForProperty($filters = [])
    {
        $parameters = [];
        $reservationDateSql = '';
        $checkInSql = '';
        $checkOutSql = '';
        $idReservationSql = '';
        $clientWordSql = '';

        $qb = $this->createQueryBuilder('p');

        $propertySql = 'property.slug = :slug';
        $parameters['slug'] = $filters['slug'];

        if(isset($filters['status'])) {
            $statusSql = 'p.status = :status';
            $parameters['status'] = $filters['status'];
        } else {
            $statusSql = '(p.status = 2 OR p.status = 3)';
        }

        if (isset($filters['checkIn'])) {
            $checkInSql = ' AND (p.date_check_in >= :checkIn OR p.date_check_out >= :checkIn )';
            $parameters['checkIn'] = $filters['checkIn'];
        }

        if (isset($filters['checkOut'])) {
            $checkOutSql = ' AND (p.date_check_in <= :checkOut OR p.date_check_out <= :checkOut )';
            $parameters['checkOut'] = $filters['checkOut'];
        }

        if (isset($filters['date'])) {
            $reservationDateSql = ' AND p.reservation_date = :date';
            $parameters['date'] = $filters['date'];
        }

        if (isset($filters['name'])) {
            $clientWordSql = ' AND '.$qb->expr()->like('lower(UNACCENT(client.full_name))', 'lower(UNACCENT(:name))');
            $parameters['name'] = '%'.$filters['name'].'%';
        }

        if (isset($filters['id'])) {
            $idReservationSql = ' AND '.$qb->expr()->like('lower(UNACCENT(p.public_id))', 'lower(UNACCENT(:id))');
            $parameters['id'] = '%'.$filters['id'].'%';
        }

        $sql = $propertySql.' AND ('.$statusSql.$checkInSql.$checkOutSql.$reservationDateSql.$clientWordSql.$idReservationSql.')';
        return  $qb
            ->where ($sql)
            ->join('p.property_id', 'property')
            ->join('p.client_id','client')
            ->setParameters ($parameters)
            ->getQuery()
            ->getResult();
    }

    /**
     *  encuentra todas las reservaciones de un cliente
     *
     * @param array $filters
     * @return array
     */
    public function findReservationsForClient($filters = [])
    {
        $parameters = [];
        $qb = $this
            ->createQueryBuilder('p')
            ->join('p.property_id', 'property')
            ->join('p.client_id','client')
            ->where ('client.id = :idClient');

        $parameters['idClient'] = $filters['idClient'];

        if (isset($filters['slug'])) {
            $qb->andWhere('property.slug = :slug');
            $parameters['slug'] = $filters['slug'];
        }

        //verifico si el status llega como nulo
        $filters['status'] = is_null($filters['status']) ? 0 : $filters['status'];

        //El valor 4 es para hacer una consulta de todos las reservar sin importar el estado
        if($filters['status']!=4){
            $qb->andWhere('p.status = :status');
            $parameters['status'] = $filters['status'];
        }

        if (isset($filters['checkIn'])) {
            $qb->andWhere('p.date_check_in = :checkIn');
            $parameters['checkIn'] = $filters['checkIn'];
        }

        if (isset($filters['checkOut'])) {
            $qb->andWhere('p.date_check_out = :checkOut');
            $parameters['checkOut'] = $filters['checkOut'];
        }

        if (isset($filters['date'])) {
            $qb->andWhere('p.reservation_date = :date');
            $parameters['date'] = $filters['date'];
        }

        if (isset($filters['id'])) {
            $qb->andWhere($qb->expr()->like('lower(UNACCENT(p.public_id))', 'lower(UNACCENT(:id))'));
            $parameters['id'] = '%'.$filters['id'].'%';
        }

        if (isset($filters['orderBy'])) {
            $order = 'p.public_id';
            switch ($filters['orderBy']) {
                case 'date': $order = 'p.reservation_date'; break;
                case 'checkIn': $order = 'p.date_check_in'; break;
                case 'checkOut': $order = 'p.date_check_out'; break;
                case 'slug': $order = 'p.slug'; break;
                case 'status': $order = 'p.status'; break;
            }
            $qb->orderBy($filters['orderBy'],$filters['orderType']);
        }

        $qb->setParameters($parameters);

        return $this->getPaginatedData($qb,1,1000,false);
    }

    /**
     *  encuentra las reservaciones de un cliente con checkin mayor al día actual
     *
     * @param array $data
     * @return array
     */
    public function findUpcomingReservations($data)
    {
        $qb = $this->createQueryBuilder('r');

        $clientSql = 'client.id = :idClient';
        $parameters['idClient'] = $data['idClient'];

        //obtengo el día actual
        $today = date("Y-m-d H:i:s");

        $parameters['date'] = $today;
        $sql = $clientSql.' AND r.date_check_in >= :date AND r.status = 2';
        return $qb
            ->where($sql)
            ->join('r.client_id','client')
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
    }

    /**
     * devuelve una lista de possibles estados de una reserva
     */
    public function getStatesList()
    {
        $reservation = new Reservation();
        $list = $reservation->getStatesList();
        $response = [];
        foreach($list as $key => $state)
        {
            $response[] = [
                'id' => $key,
                'title' => $state
            ];
        }
        return $response;
    }

    /**
     * Devuelve una lista de estados traducidos de una reserva
     */
    public function getStatesListName()
    {
        $reservation = new Reservation();
        $stateList = $reservation->getStatesList();
        return $reservation->getStateNames($stateList);
    }

    public function findAllReservationToCommercial()
    {

    }

    public function countReservation()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

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
    public function findReservationByWords($data)
    {
        $arrayOfMatch = [
            "create_date",
            "check_in",
            "check_out",
            "due_date",
            "status_date"
        ];

        // Separamos las palabras en:
        // - las que van a la clausula "where" sin pasar por el vector y
        // - las que van a la clausula del where pero al "ts_query"
        $separatedWords = $this->separateByType($data['search'], $arrayOfMatch);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], "search_vector");
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], "search_vector");

        // Agregamos a la normalizacion de las palabras la busqueda para incluir
        // un array que contiene los nombres de las columnas de la BD donde buscar
        // las fechas
        // Ej: check_in:2017-01-01:2017-03-01 (check_in desde 2017-01-01 hasta 2017-03-01)
        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);
        $additionalCriteria = !is_null($additionalCriteria) ? $additionalCriteria." and " : null;

        return $this
            ->select('
                id,
                ceco,
                type,
                name_property,
                city,
                public_id,
                create_date,
                check_in,
                check_out,
                client_name,
                mp,
                currency,
                total_to_pay,
                status
                '.
                $data["select"]
            )
            ->from('admin_reservation_list_view')
            ->where($tsQuery, $additionalCriteria." ".$data["where"])
            ->paginate(
                $data["page"],
                isset($data["itemsPerPage"]) ? $data["itemsPerPage"] : 50)
            ->order($data['orderBy'], $data['orderType'], $tsRank)
            ->getResults();
    }


    /*public function testReservation()
    {
        $qb2 = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select([
                'payment.id',
                'transmitter.title',
                'receiver.title'
            ])
            //->where('p.reservation = :')
            ->from('NavicuDomain:payment','payment')
            ->leftJoin('p.bank', 'transmitter')
            ->leftJoin('p.receiverBank', 'receiver')
            ;


        //$parameters = [];
        $qb = $this
            ->createQueryBuilder('p')
            ->select([
                'p.public_id idReserve',
                'p.reservation_date date',
                'p.date_check_in checkIn',
                'p.date_check_out checkOut',
                'p.total_to_pay amount',
                'client.full_name names',
                'property.name propertyName',
                'property.slug propertySlug',
                //$qb2->getDql().' payments'
            ])
            ->join('p.property_id', 'property')
            ->join('p.client_id','client')
            //->where ('client.id = :idClient');
            ;
        /*$parameters['idClient'] = $filters['idClient'];

        if (isset($filters['slug'])) {
            $qb->andWhere('property.slug = :slug');
            $parameters['slug'] = $filters['slug'];
        }

        //verifico si el status llega como nulo
        $filters['status'] = is_null($filters['status']) ? 0 : $filters['status'];

        //El valor 4 es para hacer una consulta de todos las reservar sin importar el estado
        if($filters['status']!=4){
            $qb->andWhere('p.status = :status');
            $parameters['status'] = $filters['status'];
        }


        if (isset($filters['checkIn'])) {
            $qb->andWhere('p.date_check_in = :checkIn');
            $parameters['checkIn'] = $filters['checkIn'];
        }

        if (isset($filters['checkOut'])) {
            $qb->andWhere('p.date_check_out = :checkOut');
            $parameters['checkOut'] = $filters['checkOut'];
        }

        if (isset($filters['date'])) {
            $qb->andWhere('p.reservation_date = :date');
            $parameters['date'] = $filters['date'];
        }

        if (isset($filters['id'])) {
            $qb->andWhere($qb->expr()->like('lower(UNACCENT(p.public_id))', 'lower(UNACCENT(:id))'));
            $parameters['id'] = '%'.$filters['id'].'%';
        }

        $qb->setParameters($parameters);*/

        //return $this->getPaginatedData($qb,1,20);
        //return $qb->getQuery()->getArrayResult();
        //return get_class_methods($qb);
    //}

}
