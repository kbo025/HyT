<?php
namespace Navicu\Core\Application\UseCases\AAVV\Billing\ListBilling;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Caso de uso para el listado, filtro y ordenamiento de las facturas
 */
class ListBillingHandler implements Handler
{

    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;

        $rep = $this->rf->get('AAVVInvoice');
        $repAAVV = $this->rf->get('AAVV');

        $aavv = $repAAVV->findOneByArray(['slug' => $command->get('slug')]);

        $invoices = $rep->getInvoicesBySlugAndFilters($command->getRequest());

        $data = [
            'invoices' => $this->getData($invoices),
            'haveCreditZero' => $aavv->getHaveCreditZero(),
        ];

        return new ResponseCommandBus(200,'ok',$data);
    }

    private function getData($invoices)
    {
        $data = [];

        foreach($invoices as $invoice) {

            $groups = $invoice->getAavvReservationGroup();

            $amountReservations = 0;

            foreach($groups as $group)
                $amountReservations = $amountReservations + count($group->getReservation());

            $data[] = [
                'billingDate' => $invoice->getDate()->format('d-m-Y'),
                'numberBilling' => $invoice->getNumber(),
                'amountReservations' => $amountReservations,
                'total' => number_format($invoice->getTotalAmount(), 2, ',', '.'),
                'dueDate' => $invoice->getDueDate()->format('d-m-Y'),
                'status' => $invoice->getStatus(),
            ];
        }

        //aqui se ordene al array si el usuario busca ordenamiento por cantidad de reservas
        if ( isset($filters['orderBy']) && ($filters['orderBy']=='amountReservations'))
            usort($data,self::build_sorter($filters['orderBy'],$filters['order']));

        return $data;
    }

    /**
     * funcion de comparacion para ordenar el array response para los campos calculados fuera de la BD
     */
    public static function build_sorter($key,$order)
    {
        return function ($a, $b) use ($key,$order) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            if(strtolower($order)=='asc')
                return ($a[$key] < $b[$key]) ? -1 : 1;
            else
                return ($a[$key] > $b[$key]) ? -1 : 1;
        };
    }
}
