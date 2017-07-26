<?php
/**
 * Created by Isabel Nieto.
 * Date: 19/07/16
 * Time: 05:01 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Navicu\Core\Domain\Repository\ExchangeRateHistory;

class DbExchangeRateHistoryRepository extends EntityRepository implements ExchangeRateHistory
{
    public function getById($id) {
        return $this->find($id); 
    }

    public function save($newExchangeRate)
    {
        $this->getEntityManager()->persist($newExchangeRate);
        $this->getEntityManager()->flush();
    }
    
    public function persistDb($newExchangeRate) {
        $this->getEntityManager()->persist($newExchangeRate);
    }

    public function flushDb() {
        $this->getEntityManager()->flush();
    }

    /**
     * Funcion para traer los valores almacenados por un arreglo de dias y devolverlos
     *
     * @param array $arrayOfDays , arreglo con las fechas a consultar
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21-07-2016
     */
    public function getByArrayOfDates($arrayOfDays){
        return $this->createQueryBuilder('e')
            ->where('
            e.date IN (:arrayOfDays) 
        ')
            ->orderBy('e.date', 'asc')
            ->setParameters(array(
                'arrayOfDays' => $arrayOfDays
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para buscar desde el inicio del mes ingresado hasta los dos siguientes
     *
     * @param string $requestDate fecha inicial
     * @param string $twoMothLater fecha final
     * @return array
     */
    public function getByIniDate($requestDate, $twoMothLater) {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.currency_type','ct')
            ->where('
                ct.alfa3 = \'USD\' and
                e.date >= :requestDate and
                e.date < :twoMonthLater
            ')
            ->orderBy('e.date', 'asc')
            ->setParameters(array(
                'requestDate' => $requestDate,
                'twoMonthLater' =>$twoMothLater
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para devolver la primera entrada de la entidad
     *
     * @author Isabel Nieto
     * @version 25/07/2016
     * @return array
     */
    public function getFirstDate()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para retornar la ultima fecha en la que se ejecuto la api
     *
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 25/08/2016
     */
    public function getLastDate()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para buscar dado un rago de fechas toda la informacion ordenada por currency_type
     * y ordenado por fecha, siendo el 'currency_type' el orden predominante
     *
     * @param string $firstDate, fecha inicio a consultar
     * @param string $lastDate, fecha fin a consultar
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @return array
     */
    public function getByDateRange($firstDate, $lastDate)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :firstDate and 
            e.date <= :lastDate')
            ->orderBy('e.currency_type, e.date', 'asc')
            ->setParameters(array(
                'firstDate' => $firstDate,
                'lastDate' => $lastDate
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para buscar dado un rago de fechas toda la informacion ordenada por fecha
     * y currency_type, siendo el 'date' el orden predominante
     *
     * @param string $firstDate, fecha inicio a consultar
     * @param string $lastDate, fecha fin a consultar (maximo hoy)
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @return array
     */
    public function getByDateRangeInBs($firstDate, $lastDate, $orderAscOrDesc)
    {
        if ($firstDate) {
            return $this->createQueryBuilder('e')
                ->where('e.date >= :firstDate and 
                e.date <= :lastDate
                and (e.currency_type = 145
                or e.currency_type = 47)
                ')
                ->orderBy('e.date, e.currency_type', $orderAscOrDesc)
                ->setParameters(array(
                    'firstDate' => $firstDate,
                    'lastDate' => $lastDate
                ))
                ->getQuery()
                ->getResult();
        }
        else {
            return $this->createQueryBuilder('e')
                ->where('e.date <= :lastDate
                and (e.currency_type = 145
                or e.currency_type = 47)
                ')
                ->orderBy('e.date, e.currency_type', $orderAscOrDesc)
                ->setParameters(array(
                    'lastDate' => $lastDate
                ))
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Funcion para buscar dado un rago de fechas toda la informacion ordenada por fecha
     * y currency_type, siendo el 'date' el orden predominante
     *
     * @param string $firstDate, fecha inicio a consultar
     * @param string $lastDate, fecha fin a consultar (maximo hoy)
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @return array
     */
    public function getByDateRangeDescNotBs($firstDate, $lastDate)
    {
        if ($firstDate) {
            return $this->createQueryBuilder('e')
                ->where('e.date >= :firstDate and 
                e.date <= :lastDate 
                and e.currency_type <> 145
                and e.currency_type <> 47 
                ')
                ->orderBy('e.date, e.currency_type', 'desc')
                ->setParameters(array(
                    'firstDate' => $firstDate,
                    'lastDate' => $lastDate
                ))
                ->getQuery()
                ->getResult();
        }
        else {
            return $this->createQueryBuilder('e')
                ->where('e.date <= :lastDate
                and e.currency_type <> 145
                and e.currency_type <> 47
                ')
                ->orderBy('e.date, e.currency_type', 'desc')
                ->setParameters(array(
                    'lastDate' => $lastDate
                ))
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Funcion encargada de buscar en la fecha solicitada si existe un registro en dolar o euro
     *
     * @param $today
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function findByDate($today)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.currency_type','ct')
            ->where('
                e.date = :today and
                (ct.alfa3 = \'USD\' or
                ct.alfa3 = \'EUR\') 
                ')
            ->setParameters(array(
                'today' => $today
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion encargada de devolver los cambios de todas las monedas
     * dada la fecha de hoy donde se cargo la informacion de las apis
     *
     * @param string $today, fecha actual que se hace la reserva
     * @version 19/08/2016
     * @author Isable Nieto <isabelnieto@gmail.com>
     * @return array
     */
    public function findByLastDate($today)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.currency_type','ct')
            ->where('ct.active = true and
                e.date <= :today
            ')
            ->setParameters(array(
                'today' => $today
            ))
            ->orderBy('e.date',"desc")
            ->getQuery()
            ->getResult();
    }

    /**
     * Funcion para obtener el ultimo registro existente de rate_navicu
     *
     * @param $lastDate
     * @param $order
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 19/08/2016
     * @return array
     */
    public function getLastPriceInBs($lastDate, $order)
    {
        return $this->createQueryBuilder('e')
            ->join('e.currency_type','ct')
            ->where('e.date <= :lastDate
            and (ct.alfa3 = \'USD\'
            or ct.alfa3 = \'EUR\')
            and e.rate_navicu <> 0
            ')
            ->orderBy('e.date', $order)
            ->setParameters(array(
                'lastDate' => $lastDate
            ))
            ->getQuery()
            ->getResult();

    }

    /**
     * Funcion encargada de calcular el rate_navicu en base al dia que se esta
     * realizando la reserva para tomar el porcentaje de descuento correspondiente
     *
     * @param object $dateToLookUp, fecha que se esta buscando la reserva
     * @param $today
     * @param int $coinId, id de la moneda que se busca (145,47,etc)
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 06/09/2016
     * @return array con el valor del rate_navicu calculado por el today
     */
    public function getLastRateNavicuInBs($dateToLookUp, $today, $coinId)
    {
        // Consulta SQL en string
        $query = "
            select  
                case 
                when ( consult_1.date = :today and consult_1.rate_api is not null and consult_1.percentage_navicu is not null and consult_1.currency_type = ct.id)
                    then              
                    case when consult_1.rate_navicu is null 
                    then
                        consult_1.rate_api - ((consult_1.percentage_navicu * consult_1.rate_api) /100)
                    else 
                        consult_1.rate_navicu
                    end           
                /* Cuando la fecha ingresada es mayor al ultimo registro donde existio el rate_api*/
                else 
                    (select
                        case 
                        when :date_to_look_up > consult_1.date
                            then ( 
                                select consult_2.rate_api - ((consult_2.percentage_navicu * consult_2.rate_api) /100)
                                from exchange_rate_history as consult_2
                                where consult_2.rate_api <> 0 and consult_2.currency_type = ct.id
                                order by consult_2.date desc
                                limit 1)
                        else /*Caso que la fecha consultada este dentro de los datos de la BD*/
                            (
                            select consult_3.rate_api - ((consult_1.percentage_navicu * consult_3.rate_api) /100)
                            from exchange_rate_history as consult_3
                            where consult_3.rate_api <> 0 and consult_3.currency_type = ct.id
                            order by consult_3.date desc
                            limit 1)
                        end
                    )
                end as new_rate_navicu
            from exchange_rate_history as consult_1
            join currency_type as ct
            on ct.id = consult_1.currency_type
            where consult_1.date <= :date_to_look_up and ct.id = :coinId
            order by consult_1.date desc
            limit 1";
        // Aplicando el mapeo del Select de SQL
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('NavicuDomain:ExchangeRateHistory','consult_1');

        $rsm->addScalarResult(/*nombre de la columna del select*/'new_rate_navicu',
                              /*nombre de la columna del resultado(Salida)*/'new_rate_navicu','float');

        $naviteQuery = $this->getEntityManager()->createNativeQuery($query,$rsm);

        $naviteQuery->setParameter('date_to_look_up', $dateToLookUp);
        $naviteQuery->setParameter('coinId', $coinId);
        $naviteQuery->setParameter('today', $today);

        return $naviteQuery->getResult();
    }
}