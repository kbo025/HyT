<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\AAVVInvoiceRepository;

class DbAAVVInvoiceRepository extends DbBaseRepository implements AAVVInvoiceRepository
{
    public function findExpiredInvoicesToDate()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.due_date < :date AND
                u.status = 0
                ')
            ->setParameters(
                array(
                    'date' => new \DateTime()
                )
            )->getQuery()->getResult();
    }

    /**
     * Funcion que retorna las facturas segun un slug y filtros enviados
     *
     * @param $filters
     * @return array
     */
    public function getInvoicesBySlugAndFilters($filters)
    {
        $qb = $this->createQueryBuilder('invoice');
        $qb->join('invoice.aavv','aavv');
        $sql = "aavv.slug = :slug";
        $parameters['slug'] = $filters['slug'];

        if (!empty($filters['word'])) {

            if ($this->isDateFormat($filters['word'])) {
                $parameters['word1'] = '%' . $filters['word'] . '%';
                $parameters['word2'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND (invoice.date = :word1 OR invoice.due_date = :word2 )';
            } else {
                $parameters['word'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND (' . $qb->expr()->like('lower(UNACCENT(invoice.number))', 'lower(UNACCENT(:word))') . ')';

            }
        }

        if (isset($filters['order'])) {
            switch ($filters['orderBy']) {
                case 'billingDate':
                    $qb->orderBy('invoice.date',$filters['order']);
                    break;
                case 'numberBilling':
                    $qb->orderBy('invoice.number', $filters['order']);
                    break;
                case 'amountReservations':break;
                case 'total':
                    $qb->orderBy('invoice.total_amount', $filters['order']);
                    break;
                case 'due_date':
                    $qb->orderBy('invoice.due_date', $filters['order']);
                    break;
                case 'status':
                    $qb->orderBy('invoice.status', $filters['order']);
                    break;
            }
        } else {
            $qb->orderBy('invoice.date','DESC');
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
            $dateArray = explode( '-', $str );
            return checkdate($dateArray[1], $dateArray[0], $dateArray[2]);
        }
        return false;
    }

    public function findOneByNumberAndAavv($slug,$number) 
    {
        return $this->createQueryBuilder('invoice')
            ->where('
                aavv.slug = :slug and
                invoice.number = :number
                ')
            ->setParameters(array(
                'slug' => $slug,
                'number' => $number
            ))
            ->join('invoice.aavv','aavv')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Funcion encargada de encontrar las facturas que aun estan vigentes
     *
     * @return array
     */
    public function findInvoicesNotExpired()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.due_date >= :date AND
                u.status = 0
                ')
            ->setParameters(
                array(
                    'date' => new \DateTime()
                )
            )
            ->getQuery()->getResult();
    }

    /**
     * Funcion encargada de buscar las facturas que estan vencidas
     *
     * @return array
     */
    public function findInvoicesExpired()
    {
        return $this->createQueryBuilder('u')
            ->where('
                u.due_date < :date OR
                u.status = 3
                ')
            ->setParameters(
                array(
                    'date' => new \DateTime()
                )
            )
            ->orderBy('u.due_date', 'asc')
            ->getQuery()->getResult();
    }

    /**
     * Funcion que retorna las facturas segun el estado de la factura y filtros enviados
     *
     * @param $filters
     * @return array
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 30/11/2016
     */
    public function getInvoicesByFilters($filters)
    {
        $qb = $this->createQueryBuilder('invoice');
        $qb->join('invoice.aavv','aavv');
        $sql = "invoice.status = " .$filters['status'];
        // si el parametro 'word' no viene definido inicializamos la variable para que no de problemas
        $parameters = [];

        if (!empty($filters['word'])) {
            if ($this->isDateFormat($filters['word'])) {
                $parameters['word1'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND (invoice.due_date = :word1)';
            } elseif (is_numeric($filters['word'])) {
                $parameters['word'] = $filters['word'];
                $sql = $sql . ' AND invoice.total_amount = :word';
            } else {
                // Para que arroje resultados por la accion buscar
                $parameters['word'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND ((' . $qb->expr()->like('lower(UNACCENT(aavv.public_id))', 'lower(UNACCENT(:word))') .
                ') OR ('. $qb->expr()->like('lower(UNACCENT(aavv.commercial_name))', 'lower(UNACCENT(:word))') .') )';
            }
        }

        if (isset($filters['order'])) {
            if (isset($filters['orderBy'])) {
                switch ($filters['orderBy']) {
                    case 'aavvPublicId':
                        $qb->orderBy('aavv.public_id', $filters['order']);
                        break;
                    case 'total':
                        $qb->orderBy('invoice.total_amount', $filters['order']);
                        break;
                    case 'dueDate':
                        $qb->orderBy('invoice.due_date', $filters['order']);
                        break;
                    case 'agencyName':
                        $qb->orderBy('aavv.commercial_name', $filters['order']);
                        break;
                    case 'type':
                        $qb->orderBy('invoice.description', $filters['order']);
                        break;
                    case 'numberServiceOrReservation':
                        $qb->orderBy('invoice.due_date', 'ASC');
                        break;
                    case 'invoiceNumber':
                        $qb->orderBy('invoice.number', $filters['order']);
                        break;
                    default:
                        $qb->orderBy('invoice.due_date', 'ASC');
                }
            }
            else
                $qb->orderBy('invoice.due_date', 'ASC');
        } else {

            $qb->orderBy('invoice.due_date','ASC');
        }

        return $qb
            ->where($sql)
            ->getQuery()
            ->setParameters($parameters)
            ->getResult();
    }
}
