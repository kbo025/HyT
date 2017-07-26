<?php
namespace Navicu\Core\Application\UseCases\Admin\TransferReservationExpiration;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\InventoryService;
use Navicu\Core\Domain\Model\Entity\ReservationChangeHistory;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * El siguiente handler busca las reserva con status de pre-reserva cuyo
 * tiempo supere las 48 horas.
 * 
 * Class TransferReservationExpiratioHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class TransferReservationExpirationHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * Método Set del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function setEmailService(EmailInterface $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * 
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf )
    {
		$currentDate = new \DateTime();
		$data = $command->getRequest();
		$repoReservation = $rf->get("Reservation");
		$inventoryService = new InventoryService;
		$response = [];
		$change = new InventoryService;

		$reservations = $repoReservation->findByTransferReservationExpiration($data["dateNow"]);

        if ($reservations) {
            
            for ($r = 0; $r < count($reservations); $r++) {
                $reservations[$r]->setState(3);
                $changeHistory = new ReservationChangeHistory;
                $changeHistory->setDate($currentDate);
                $changeHistory->setDataLog(["description" => "CronJob: Transfer Reservation Expiration"]);
                $changeHistory->setStatus(3);

                if ($reservations[$r]->getCurrentState()) {
                    $changeHistory->setLastStatus($reservations[$r]->getCurrentState());
                }

                $reservations[$r]->setCurrentState($changeHistory);
                $changeHistory->setReservation($reservations[$r]);
                $reservations[$r]->addChangeHistory($changeHistory);
                $change->increasedInventoryReserves($reservations[$r]);
                if ($reservations[$r]->getClientId()) 
                    $this->cancellationReservation($reservations[$r], $rf);
                
                array_push($response, [
                    "idReservation" => $reservations[$r]->getId(),
                    "reservationDate" => $reservations[$r]->getDateCheckIn()->format("Y-m-d H:i:s"),
                    "excuteDate" => $currentDate->format("Y-m-d H:i:s")
                ]);
            }
        }

        $repoReservation->save($reservations);

		$reservations = $repoReservation->findExpiredReservations($data["dateNow"]);
        if ($reservations) {

            for ($r = 0; $r < count($reservations); $r++) {
                $reservations[$r]->setState(3);
                $changeHistory = new ReservationChangeHistory;
                $changeHistory->setDate($currentDate);
                $changeHistory->setDataLog(["description" => "CronJob: Reservation Canceled for not completed"]);
                $changeHistory->setStatus(3);

                if ($reservations[$r]->getCurrentState()) {
                    $changeHistory->setLastStatus($reservations[$r]->getCurrentState());
                }

                $reservations[$r]->setCurrentState($changeHistory);
                $changeHistory->setReservation($reservations[$r]);
                $reservations[$r]->addChangeHistory($changeHistory);
                $repoReservation->save($reservations[$r]);
                array_push($response, [
                    "idReservation" => $reservations[$r]->getId(),
                    "reservationDate" => $reservations[$r]->getDateCheckIn()->format("Y-m-d H:i:s"),
                    "excuteDate" => $currentDate->format("Y-m-d H:i:s")
                ]);
            }

            $repoReservation->save($reservations);
        }

        return new ResponseCommandBus(200, 'ok', $response);
    }

    /**
     * La siguiente procesa las cancelaciones de la reserva
     *
     * @param Reservation $reservation
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/11/2015
     *
     */
    private function cancellationReservation(Reservation $reservation, $rf)
    {
        $emailData = $this->getDataEmail($reservation);
        $emailData['packages'] = array();

        $dateNow = new \DateTime();
        $emailData['idCancelation'] = $reservation->getPublicId();
        $emailData['dateCancelation'] = $dateNow->format('Y-m-d');

        $emailService = $this->getEmailService($reservation);
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients(array($emailData['emailClient']));
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationCanceled.html.twig');
        $emailService->setSubject('Cancelación de la Reserva - navicu.com');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
    
	/**
     * La siguiente funcion se encarga de obtener los datos basicos
     * para la actualizacion de una reserva, sea al editar o cancelar la reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Reservation $reservation
     * @return array
     */
    private function getDataEmail(Reservation $reservation)
    {
        $emailData = array();
        $reservation->roundReservation();
        $client = $reservation->getClientId();
        $emailData['nameProperty'] = $reservation->getPropertyId()->getName();
        $emailData['propertyImage'] = str_replace(' ', '%20',$reservation->getPropertyId()->getProfileImage()->getImage()->getFileName());
        $emailData['stars'] = $reservation->getPropertyId()->getStar();
        $emailData['address'] = $reservation->getPropertyId()->getAddress();
        $emailData['idReservation'] = $reservation->getPublicId();
        $emailData['dateReservation'] = $reservation->getReservationDate()->format('Y-m-d');
        $emailData['checkIn'] = $reservation->getDateCheckIn()->format('Y-m-d');
        $emailData['checkOut'] = $reservation->getDateCheckOut()->format('Y-m-d');
        $emailData['nameClient'] = $reservation->getClientId()->getFullName();
        $emailData['identityCardClient'] = $reservation->getClientId()->getIdentityCard();
        $emailData['emailClient'] = ($reservation->getClientId()->getEmail() instanceof EmailAddress) ?
            $reservation->getClientId()->getEmail()->toString() :
            $reservation->getClientId()->getEmail();
        $emailData['phoneClient'] = $reservation->getClientId()->getPhone();
        $emailData['genderClient'] = $reservation->getClientId()->getGender();
        $emailData['totalToPay'] = $reservation->getTotalToPay(true);
        $emailData['numberAdults'] = $reservation->getAdultNumber();
        $emailData['numberChildren'] = $reservation->getChildNumber();
        if ($reservation->getPropertyId()->getLocation()->getCityId())
            $cityOrState = $reservation->getPropertyId()->getLocation()->getCityId()->getTitle();
        else
            $cityOrState = $reservation->getPropertyId()->getLocation()->getParent()->getParent()->getTitle();
        $emailData['city'] = $cityOrState;
        return $emailData;
    }
}