<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Navicu\Core\Domain\Repository\AAVVRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 30/09/2016
 */
class DbAAVVRepository extends DbBaseRepository implements AAVVRepository
{

    public function findAllObjects()
    {
        return $this->findAll();
    }
    
    public function findByFilters($array)
    {
        $qb = $this->createQueryBuilder('p');

        $sql = '1=1';
        $parameters = [];

        if(!empty($array['word'])) {
            $sql = $sql.' AND ('.$qb->expr()->like('lower(UNACCENT(user.username))', 'lower(UNACCENT(:word))');
            $sql = $sql.' OR '.$qb->expr()->like('lower(UNACCENT(user.email))', 'lower(UNACCENT(:word))').')';
            $parameters['word'] = '%'.$array['word'].'%';
        }

        return  $qb
            ->where ($sql)
            ->join('p.user', 'user')
            ->setParameters ($parameters)
            ->getQuery()
            ->getResult();
    }


    public function persistObject($aavv)
    {
        $this->getEntityManager()->persist($aavv);
    }

    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * La siguiente función retorna el conjunto de agencias de viajes
     * afiliadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $data
     * @return array
     */
    public function findAllByAffiliates($data)
    {
        $order = $data["order"] == 1 ?'ASC' : 'DESC';
        return $this->createQueryBuilder('aavv')
            ->where("
                aavv.status_agency = 2 or aavv.status_agency = 3 
                order by aavv.credit_available $order
                ")
            ->getQuery()->getResult();
    }

    /**
     * funcion que retorna las AAVV en proceso de registro o finalizados aplicando filtros y ordenamiento
     *
     * @param $filters
     * @return array
     */
    public function findAllInRegistrationProcess($filters)
    {
        $qb = $this->createQueryBuilder('aavv');
        $sql = 'aavv.status_agency < 2';
        $parameters = [];

        if(!empty($filters['word'])) {

            if ($this->isDateFormat($filters['word'])) {
                $parameters['word'] = $filters['word'];
                $sql = $sql . ' AND aavv.registration_date = :word';
            } else {
                $parameters['word'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND (' . $qb->expr()->like('lower(UNACCENT(aavv.public_id))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(aavv.commercial_name))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(first_parent.title))', 'lower(UNACCENT(:word))')
                    //. ' OR ' . $qb->expr()->like('lower(UNACCENT(second_parent.title))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(city.title))', 'lower(UNACCENT(:word))')
                    /*. ' OR (' . $qb->expr()->like('lower(UNACCENT(location.title))', 'lower(UNACCENT(:word))')*/ . ')';

                $qb->leftJoin('aavv.aavv_address', 'address')
                    ->leftJoin('address.location', 'location')
                    //->leftJoin('location.location_type', 'type')
                    ->leftJoin('location.city_id', 'city')
                    ->leftJoin('location.parent', 'first_parent');
                    //->leftJoin('first_parent.parent', 'second_parent');
            }
        }

        if (isset($filters['order'])) {
            switch ($filters['orderBy']) {
                case 'beginDate':
                    $qb->orderBy('aavv.registration_date',$filters['order']);
                    break;
                case 'idAgency':
                    $qb->orderBy('aavv.public_id', $filters['order']);
                    break;
                case 'nameAgency':
                    $qb->orderBy('aavv.commercial_name',$filters['order']);
                    break;
                case 'percentComplete': break;
                case 'city': break;
            }
        } else {
            $qb->orderBy('aavv.registration_date','DESC');
        }

        return $qb
            ->where($sql)
            ->getQuery()
            ->setParameters($parameters)
            ->getResult();
    }

    /**
     * chequea si el string tiene un formato de fecha valido
     *
     * @param $str
     * @return bool
     */
    private function isDateFormat($str)
    {
        if (preg_match('/\d{1,2}-\d{1,2}-\d{4}/',$str)) {
            $dateArray = explode('-',$str);
            return checkdate($dateArray[1], $dateArray[0], $dateArray[2]);
        }
        return false;
    }

    public function generateInvoices()
    {
        // validación de reservas de aavv pagadas por transferencia
        /*$sql = "
            select rsgrp.id, rsgrp.aavv_id, sum(rs.total_to_pay) as total_amount
            from reservation rs
              inner join reservation_group rsgrp on (rs.aavv_reservation_group_id=rsgrp.id)
              inner join aavv agv on (rsgrp.aavv_id=agv.id)
            where
              rs.reservation_date between date_trunc('month', current_date) and (date_trunc('MONTH', current_date) + INTERVAL '1 MONTH - 1 day')::timestamp
              and rsgrp.aavv_invoice_id is null
              and rs.payment_type = 5
              and rs.state = 2
            group by rsgrp.id;
        ";*/

        $sql = "
            select rsgrp.id, rsgrp.aavv_id, sum(rs.total_to_pay * (1 - rs.discount_rate_aavv)) as total_amount
            from reservation rs
              inner join reservation_group rsgrp on (rs.aavv_reservation_group_id=rsgrp.id)
              inner join aavv agv on (rsgrp.aavv_id=agv.id)
            where
              rs.reservation_date between date_trunc('month', current_date) and (date_trunc('MONTH', current_date) + INTERVAL '1 MONTH - 1 day')::timestamp
              and rsgrp.aavv_invoice_id is null
              and rs.state = 2
            group by rsgrp.id;
        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * La siguiente función retorna un listado de agencia de viaje
     * usando una vista.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $data
     * @return array
     */
    public function findViewAffiliatedAAVV($data)
    {
        $tsQuery = $this->getTsQuery($data['search'], "search_vector");
        $tsRank = $this->getTsRank($data['search'], "search_vector");

        return $this
            ->select('
                slug,
                name,
                join_date,
                public_id,
                availability_credit,
                city,
                invoices_accumulated,
                invoices_month
            ')
            ->from('admin_affiliated_aavv_view')
            ->where($tsQuery)
            ->paginate($data["page"],50)
            ->order($data["orderBy"], $data["orderType"],$tsRank)
            ->getResults();
    }
}