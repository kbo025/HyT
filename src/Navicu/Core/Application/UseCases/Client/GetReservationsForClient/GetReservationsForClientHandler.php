<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 28/03/16
 * Time: 02:47 PM
 */

namespace Navicu\Core\Application\UseCases\Client\GetReservationsForClient;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetReservationsForClientHandler implements Handler
{

    /**
     * Instancia del comando que ejecuta el caso de uso
     */
    private $command;

    /**
     * instancia del RepositoryFactory
     */
    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {

        $response = [];
        $this->command = $command;
        $this->rf = $rf;
        $rpReservation = $this->rf->get('Reservation');
        //se valida el tipo de consulta solicitada desde el controlador
        switch ($command->getRequest()['type']) {
            case 1:
                $data = $rpReservation->findReservationsForClient($command->getRequest());
                $reservations = $data['data'];
                break;
            case 2:
                $reservations = $rpReservation->findUpcomingReservations($command->getRequest());
                break;
        }

        foreach ($reservations as $currentReservation) {
            $date = $currentReservation->getReservationDate();
            $current = [
                'idReserve' => $currentReservation->getPublicId(),
                'date' => isset($date) ? $date->format('Y-m-d H:i:s') : '',
                'checkIn' => $currentReservation->getDateCheckIn()->format('d-m-Y'),
                'checkOut' => $currentReservation->getDateCheckOut()->format('d-m-Y'),
                'names' => $currentReservation->getClientId()->getFullName(),
                'amount' => $currentReservation->isForeignCurrency() ?
                    $currentReservation->getCurrencyPrice() :
                    $currentReservation->getTotalToPay(),
                'state' => $currentReservation->getStatus(),
                'foreignCurrency' => $currentReservation->isForeignCurrency(),
                'alphaCurrency' => $currentReservation->getAlphaCurrency(),
                'propertyName' => $currentReservation->getPropertyId()->getName(),
                'propertySlug' => $currentReservation->getPropertyId()->getSlug()
            ];
            $current['payments'] = [];
            $payments = $currentReservation->getPayments();
            foreach ($payments as $payment) {
                if ( $payment->getState() != 2) {
                    $receiverBank = $payment->getReceiverBank();
                    $bank = $payment->getBank();
                    $current['payments'][] = [
                        //'idPayment' => $payment->getId(),
                        'receivingBank' => [
                            'id' => isset($receiverBank) ? $receiverBank->getId() : null,
                            'title' => isset($receiverBank) ? $receiverBank->getTitle() : null,
                        ],
                        'issuingBank' => [
                            'id' => isset($bank) ? $bank->getId() : null,
                            'title' => isset($bank) ? $bank->getTitle() : null,
                        ],
                        'transferredAmount' => $payment->getAmount(),
                        'referenceCode' => $payment->getReference(),
                    ];
                }
            }
            $response[] = $current;
        }
        return new ResponseCommandBus(201,'OK',$response);
    }
}
