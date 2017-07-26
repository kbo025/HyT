<?php
namespace Navicu\Core\Application\UseCases\Admin\ListReservation;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Location;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Clase para ejecutar el caso de uso ListReservation
 * @author Jose Agraz <jaagraz@navicu.com>
 * @version 22/01/2016
 */
class ListReservationHandler implements Handler
{
    private $command;

    private $rf;

    /**
     * Manejador del caso de uso
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $response = [];
        $this->command = $command;
        $this->rf = $rf;

        $rpReservation = $this->rf->get('Reservation');
        $reservationStatus = $this->command->getReservationStatus();

        if (CoreSession::isRole('ROLE_SALES_EXEC'))
            $reservation = $rpReservation->findAllReservationToAdmin($command->getRequest());
        if (CoreSession::isRole('ROLE_ADMIN'))
            $reservation = $rpReservation->findAllReservationToAdmin($command->getRequest());
        else {
            $nvcProfile = $command->getRequest()['user']->getNvcProfile();
            if ($nvcProfile->havePermissons('reservation'))
                $reservation = $rpReservation->findAllReservationToAdmin($command->getRequest());
            else
                $reservation = [];
        }

        foreach ($reservation as $dataReservationStatus) {

            $dataResponse = [];

            $dataReservationStatus->roundReservation();

            $dataResponse['propertyName'] = $dataReservationStatus->getPropertyId()->getName();
            $dataResponse['clientName'] = $dataReservationStatus->getClientId()->getFullName();
            $dataResponse['city'] = $this->getCity($dataReservationStatus->getPropertyId()->getLocation());
            $dataResponse['creationDate'] = $dataReservationStatus->getReservationDate() != null ?
                $dataReservationStatus->getReservationDate()->format('d-m-Y') :
                '';
            $dataResponse['amount'] = $dataReservationStatus->isForeignCurrency() ?
                $dataReservationStatus->getCurrencyPrice() . $dataReservationStatus->getAlphaCurrency() :
                $dataReservationStatus->getTotalToPay() . 'Bs.';
            $payment = $dataReservationStatus->getPayments()[0];
            $bank = (isset($payment)) ? $payment->getBank() : null;
            $dataResponse['bank'] = isset($bank) ? $bank->getTitle() : '';
            $dataResponse['idConfirmation'] = (isset($payment)) ? $payment->getReference() : null;
            switch ($reservationStatus) {
                case 0: // 0: pre-reserva
                    $dataResponse['dueDate'] = $dataReservationStatus->getReservationDate()->modify('+24 hour')->format('d-m-Y');
                    $dataResponse['checkIn'] = $dataReservationStatus->getDateCheckIn()->format('d-m-Y');
                    $dataResponse['idReservation'] = $dataReservationStatus->getPublicId();
                    breaK;

                case 1: // 1: por confirmación

                    $dataResponse['idReservation'] = $dataReservationStatus->getPublicId();
                    $dataResponse['status'] = $dataReservationStatus->getStatus();

                    // Devolvemos todos los payments realizados por el cliente
                    $payments = $dataReservationStatus->getPayments();
                    foreach ($payments as $payment) {
                        if ($payment->getStatus() == 0) {
                            $data['senderBank'] = is_null($payment->getBank()) ? null : $payment->getBank()->getTitle();
                            $data['receiverBank'] = is_null($payment->getReceiverBank()) ? null : $payment->getReceiverBank()->getTitle();
                            $data['amountClient'] = $payment->getAmount();
                            $data['paymentId'] = $payment->getId();
                            $data['idConfirmation'] = $payment->getReference();
                            $arrayPayments[] = $data;
                        }
                    }
                    $dataResponse['payments'] = $arrayPayments;

                    breaK;

                case 2: // 2: confirmadas
                    $dataResponse['idReservation'] = $dataReservationStatus->getPublicId();
                    $dataResponse['checkin'] = $dataReservationStatus->getDateCheckIn()->format('d-m-Y');
                    breaK;

                case 3: // 3: canceladas
                    $currentState = $dataReservationStatus->getCurrentState();
                    $dataLog = isset($currentState) ? $dataReservationStatus->getCurrentState()->getDataLog() : null;
                    $description = isset($dataLog["description"]) ? $dataLog["description"] : null;

                    $dataResponse['idReservation'] = $dataReservationStatus->getPublicId();
                    $dataResponse['cancellationDate'] = isset($currentState) ?
                        $dataReservationStatus->getCurrentState()->getDate()->format('d-m-Y') :
                        null;
                    $dataResponse['description'] = $description;
                    $dataResponse['checkIn'] = $dataReservationStatus->getDateCheckIn()->format('d-m-Y');
                    breaK;

                case 4: // 4: todas
                    $dataResponse['idReservation'] = $dataReservationStatus->getPublicId();
                    $dataResponse['checkIn'] = $dataReservationStatus->getDateCheckIn()->format('d-m-Y');
                    $dataResponse['checkOut'] = $dataReservationStatus->getDateCheckOut()->format('d-m-Y');
                    $dataResponse['status'] = $dataReservationStatus->getStatus();
                    breaK;

            }
            $response[] = $dataResponse;
        }
        return new ResponseCommandBus(201, 'OK', $response);
    }

    /**
     * funcion para obtener la ciudad
     * @param Location $location
     * @return string
     */
    private function getCity(Location $location)
    {
        $cityParent = $location->getCityId();
        $city = isset($cityParent) ? $cityParent : $location->getParent();
        return $city->getTitle();
    }
}
