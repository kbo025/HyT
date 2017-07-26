<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\AAVVInvoicePaymentsRepository;


class DbAAVVInvoicePaymentsRepository extends DbBaseRepository implements AAVVInvoicePaymentsRepository
{
    public function findInAavv($id)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.allocation', 'a')
            ->innerJoin('a.invoices', 'i','WITH', 'i.aavv = :id')
            ->setParameters(
                array(
                    'id' => $id
                )
            )->getQuery()->getResult();
    }

    /**
     * funcion que retorna los pagos filtrados
     * @param $filters
     * @return array
     */
    public function findFilteredPayments($filters)
    {
        $qb = $this->createQueryBuilder('p');
        $sql = 'p.amount > 0';
        $parameters = [];

        if(!empty($filters['word'])) {

            if ($this->isDateFormat($filters['word'])) {
                $parameters['word'] = $filters['word'];
                $sql = $sql . ' AND p.date = :word';
            } elseif (is_numeric($filters['word'])) {
                $parameters['word'] = $filters['word'];
                $sql = $sql . ' AND p.amount = :word';
            } else {
                $parameters['word'] = '%' . $filters['word'] . '%';
                $sql = $sql . ' AND (' . $qb->expr()->like('lower(UNACCENT(aavv.public_id))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(aavv.commercial_name))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(p.number_reference))', 'lower(UNACCENT(:word))')
                    . ' OR ' . $qb->expr()->like('lower(UNACCENT(invoice.number))', 'lower(UNACCENT(:word))')
                    /*. ' OR (' . $qb->expr()->like('lower(UNACCENT(location.title))', 'lower(UNACCENT(:word))')*/ . ')';

            }
        }

        if (isset($filters['order'])) {
            switch ($filters['orderBy']) {
                case 'agencyId':
                    $qb->orderBy('aavv.public_id',$filters['order']);
                    break;
                case 'agencyName':
                    $qb->orderBy('aavv.commercial_name', $filters['order']);
                    break;
                case 'reference':
                    $qb->orderBy('p.number_reference',$filters['order']);
                    break;
                case 'amount':
                    $qb->orderBy('p.amount',$filters['order']);
                    break;
                case 'invoiceNumber':
                    $qb->orderBy('invoice.number',$filters['order']);
                    break;
            }
        } else {
            $qb->orderBy('p.date','DESC');
        }

        $qb->leftJoin('p.allocation', 'allocation')
            ->leftJoin('allocation.invoice', 'invoice')
            ->leftJoin('invoice.aavv', 'aavv');

        return $qb
            ->where($sql)
            ->getQuery()
            ->setParameters($parameters)
            ->getResult();
    }

    private function isDateFormat($str)
    {
        if (preg_match('/\d{1,2}-\d{1,2}-\d{4}/',$str)) {
            $dateArray = explode('-',$str);
            return checkdate($dateArray[1], $dateArray[0], $dateArray[2]);
        }
        return false;
    }
}