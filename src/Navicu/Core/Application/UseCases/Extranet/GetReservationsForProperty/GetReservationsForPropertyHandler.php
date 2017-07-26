<?php
namespace Navicu\Core\Application\UseCases\Extranet\GetReservationsForProperty;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Clase para ejecutar el caso de uso que devuelve la lista de reservas de un establecimiento
 */
class GetReservationsForPropertyHandler implements Handler
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
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        try {
            $response = [];
            $this->command = $command;
            $this->rf = $rf;

            $rpReservation = $this->rf->get('Reservation');
            $reservations = $rpReservation->findReservationsForProperty($command->getRequest());

            foreach ($reservations as $currentReservation) {
                $currentReservation->roundReservation();
                $date = $currentReservation->getReservationDate();
                $response[] = [
                    'id' => $currentReservation->getPublicId(),
                    'date' => isset($date) ? $date->format('d-m-Y') : '',
                    'checkIn' => $currentReservation->getDateCheckIn()->format('d-m-Y'),
                    'checkOut' => $currentReservation->getDateCheckOut()->format('d-m-Y'),
                    'numRooms' => 0,
                    'names' => $currentReservation->getClientId()->getFullName(),
                    'amount' => $currentReservation->getNetRate(true),
                    'state' => $currentReservation->getStatus()
                ];
            }
        } catch(\Exception $e){
            return new ResponseCommandBus(400,'Bad Request',['message'=>$e->getMessage()]);
        }
        return new ResponseCommandBus(201,'OK',$response);
    }


    /**
     * Función para el manejo de los precio neto
     * que facturara el hotelero.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param $reservation Objeto entidad reservation
     * @return intenger
     */
    public function getNetRate($reservation)
    {
        $sellRate = $reservation->getTotalToPay();
        $discountRate = $reservation->getPropertyId()->getDiscountRate();

        return $sellRate - ($sellRate * $discountRate);
    }
}