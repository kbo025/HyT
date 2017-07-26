<?php
namespace Navicu\Core\Application\UseCases\Admin\ReservationModule\ReservationList;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Comando es usado para listar las reservas del sistema dado un usuario Admin
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ReservationListHandler implements Handler
{

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();
        $reservationRP = $rf->get("Reservation");
        $request["select"] = null;

        if (is_null($request["status"])) {
            $request["select"] = " ";
            $request["where"] = " status is not null";            
        } else if ($request["status"]==0) {
            $request["select"] = ", due_date";
            $request["where"] = " status = 0 ";
        } else if ($request["status"]==1) {
            $request["where"] = " status = 1 ";
        } else if ($request["status"]==2) {
            $request["where"] = " status = 2 ";
        } else if ($request["status"]==3) {
            $request["select"] = ",status_date";
            $request["where"] = " status = 3 ";
        } else {
            $request["select"] = " ";
            $request["where"] = " status is not null";
        }

        $response = $reservationRP->findReservationByWords(
            $request,
            isset($request["itemsPerPage"]) ? $request["itemsPerPage"] : 50
        );

        // Busqueda de los pagos para las reservas en proceso de confirmaciòn.
        if ($request["status"] == 1)
            $response = $this->getPayment($response, $request, $rf);

        return new ResponseCommandBus(201, 'OK', $response);
    }

    /**
     * Busca los pagos asociados a una reserva en proceso de confirmaciòn.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param  Array $response
     * @param  Array $request
     * @param  RepositoryFactoryInterface $rf
     * @return Array
     */
    public function getPayment($response, $request, $rf)
    {
        $paymentRP = $rf->get("Payment");
        for ($i = 0; $i < count($response["data"]); $i++) {
            $payment = $paymentRP->findBy(["reservation" => $response["data"][$i]["id"]]);

            if($payment) {
                $response["data"][$i]["payment"] = [];
                for ($p = 0; $p < count($payment); $p++) {
                    if ($payment[$p]->getState() != 2) {
                        $auxPayment['sender_bank'] = is_null($payment[$p]->getBank()) ? null : $payment[$p]->getBank()->getTitle();
                        $auxPayment['receiver_bank'] = is_null($payment[$p]->getReceiverBank()) ? null : $payment[$p]->getReceiverBank()->getTitle();
                        $auxPayment['amount_client'] = $payment[$p]->getAmount();
                        $auxPayment['paymentId'] = $payment[$p]->getId();
                        $auxPayment['confirmation_id'] = $payment[$p]->getReference();
                        array_push($response["data"][$i]["payment"], $auxPayment);
                    }
                }
            }
        }
        return $response;
    }
}
