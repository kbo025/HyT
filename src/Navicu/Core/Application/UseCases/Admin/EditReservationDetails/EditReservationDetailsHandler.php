<?php
namespace Navicu\Core\Application\UseCases\Admin\EditReservationDetails;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Services\CoordinatesGps;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationCanceled;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Application\Contract\EmailInterface;

/**
 * El handler se encarga de editar los datos de una reserva y procesar la cancelación de la reserva
 *
 * Class EditReservationDetails
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 29/10/2015
 */
class EditReservationDetailsHandler implements Handler
{
    /**
     *   instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * El manejador de la BD
     * @var
     */
    protected $managerBD;

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
     * @param $managerBD
     */
    public function setManagerBD($managerBD)
    {
        $this->managerBD = $managerBD;
    }

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 29/10/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;

        $rpReservation = $rf->get('Reservation');

        $reservation = $rpReservation->findOneBy(array('public_id' => $command->getPublicId()));

        //Si existe una reserva con ese id publico
        if ($reservation) {

            //Si aun no esta cancelada, se puede editar
            if (is_null($reservation->getReservationCanceled())) {
                $data = $command->getRequest()['data'];

                //Si se modifico el estado de la reserva
                if (isset($data['status']) and !$data['status']) {
                    $this->cancellationReservation($reservation);
                } else {

                    $oldNameClient = $reservation->getClientId()->getFullName();

                    $flagEdit = false;
                    if (isset($data['nameClient'])) {
                        $reservation->getClientId()->setFullName($data['nameClient']);
                        $flagEdit = true;
                    }

                    if (isset($data['identityCard'])) {
                        $reservation->getClientId()->setIdentityCard(strval($data['identityCard']));
                        $flagEdit = true;
                    }

                    if (isset($data['phone'])) {
                        $reservation->getClientId()->setPhone($data['phone']);
                        $flagEdit = true;
                    }

                    if (isset($data['email'])) {
                        $reservation->getClientId()->setEmail(new EmailAddress($data['email']));
                        $flagEdit = true;
                    }

                    if (isset($data['informationEmail'])){
                        $reservation->getClientId()->setEmailNews($data['informationEmail']);
                        $flagEdit = true;
                    }

                    if (isset($data['observations'])) {
                        $reservation->setSpecialRequest($data['observations']);
                        $flagEdit = true;
                    }
                    if ($flagEdit) {
                        $reservation->setLastEdit(new \DateTime());
                        $this->sendEmailEditReservation($reservation, $oldNameClient);
                    }
                    else
                        return new ResponseCommandBus(400, 'No se ha modificado ningun dato');
                }

                $rpReservation->save($reservation);
                return new ResponseCommandBus(201, 'Ok');
            } else
                return new ResponseCommandBus(400, 'Bad Request', 'Reserva Calcelada previamente');

        } else
            return new ResponseCommandBus(404, 'Not Found');

    }

    /**
     * La siguiente funcion envia un correo
     * de edicion de los datos de la reserva
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 11/11/2015
     * @param Reservation $reservation
     * @param $oldNameClient
     */
    private function sendEmailEditReservation(Reservation $reservation, $oldNameClient)
    {
        $emailData = $this->getDataEmail($reservation);
        $emailData['packages'] = array();
        $emailData['oldNameClient'] = $oldNameClient;
        $emailData['subTotalToPay'] = round($reservation->getTotalToPay(), 2);
        $emailData['lastEdit'] = $reservation->getLastEdit()->format('Y-m-d');
        $emailData['taxPay'] = round($reservation->getTaxPay(), 2);
        if (!is_null($reservation->getPropertyId()->getPhones()[0]))
            $emailData['phoneProperty'] = $reservation->getPropertyId()->getPhones()[0];
        $emailData['phoneProperty'] = $reservation->getPropertyId()->getPhones()[0];

        $coordenates = $reservation->getPropertyId()->getCoordinates();
        $emailData['propertyLatitude'] = $coordenates['latitude'];
        $emailData['propertyLongitude'] = $coordenates['longitude'];
        $emailData['propertyGps'] = CoordinatesGps::getGps($coordenates['latitude'], $coordenates['longitude']);

        if (!is_null($reservation->getSpecialRequest()))
            $emailData['specialRequest'] = $reservation->getSpecialRequest();

        foreach ($reservation->getReservationPackages() as $currentPack) {
            $auxPack = array();
            /*$auxPack['nameRoom'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getRoom()->getType()->getTitle());*/
            $auxPack['nameRoom'] = CoreTranslator::getTranslator($currentPack
                ->getTypeRoom()
                ->getTitle()
            );
            /*$auxPack['namePack'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getType()->getCode());*/
            $auxPack['namePack'] = CoreTranslator::getTranslator($currentPack
                ->getTypePack()
                ->getCode()
            );
            $auxPack['roomsNumber'] = $currentPack->getNumberRooms();
            $auxPack['childNumber'] = $reservation->getChildNumber();
            $auxPack['adultNumber'] = $reservation->getAdultNumber();
            $auxPack['bedsType'] = '';

            $bedRoom = $currentPack->getBedRoomId();

            if ($bedRoom) {
                foreach ($bedRoom['beds'] as $currentBed) {
                    $auxPack['bedsType'] = $auxPack['bedsType'] .
                        $currentBed['typeString'] .
                        ($currentBed['amount'] == 1 ? '' : '(' . $currentBed['amount'] . ') + ');
                }
            }

            array_push($emailData['packages'], $auxPack);
        }

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients(array($emailData['emailClient']));
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:editReservation.html.twig');
        $emailService->setSubject('Modificicación de la Reserva - navicu.com');
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
        $emailData['totalToPay'] =
            round($reservation->getTotalToPay() + $reservation->getTaxPay(),2);

        return $emailData;
    }

    /**
     * La siguiente procesa las cancelaciones de la reserva
     *
     * @param Reservation $reservation
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/11/2015
     *
     */
    private function cancellationReservation(Reservation $reservation)
    {
        $rpDailyPack = $this->rf->get('DailyPack');
        $checkInDate = $reservation->getDateCheckIn();
        $checkOutDate = $reservation->getDateCheckOut();
        $emailData = $this->getDataEmail($reservation);
        $emailData['packages'] = array();

        //Se crea la cancelación de la reserva
        //Aun no se calcula el porcentaje por política de cancelación
        $reservationCanceled = new ReservationCanceled();
        $reservationCanceled->setDateCancellation(new \DateTime("now"));
        $reservationCanceled->setReservation($reservation);
        $discountRate = $reservation->getPropertyId()->getDiscountRate();
        $totalPrice = $reservation->getTotalToPay() + $reservation->getTaxPay();
        $reservationCanceled->setRefundOwner($totalPrice * (1 - $discountRate));
        $reservationCanceled->setNoRefund($totalPrice * $discountRate);
        $reservationCanceled->setRefundClient(0);
        $reservation->setReservationCanceled($reservationCanceled);

        $this->managerBD->persist($reservationCanceled);

        //Se devuelven las disponibilidades de los servicios
        foreach ($reservation->getReservationPackages() as $currentPack) {
            $dailyPackages = $rpDailyPack
                ->findByDatesPackId(
                    $currentPack->getPackId()->getId(),
                    $checkInDate,
                    $checkOutDate,
                    null
                );

            foreach ($dailyPackages as $currentDailyPack) {

                $currentDailyPack->setSpecificAvailability(
                    $currentDailyPack->getSpecificAvailability() +
                    $currentPack->getNumberRooms()
                );

                $this->managerBD->persist($currentDailyPack);
            }

            $auxPack = array();
            $auxPack['nameRoom'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getRoom()->getType()->getTitle());
            $auxPack['namePack'] = CoreTranslator::getTranslator($currentPack
                ->getPackId()->getType()->getCode());
            $auxPack['roomsNumber'] = $currentPack->getNumberRooms();
            $auxPack['childNumber'] = $reservation->getChildNumber();
            $auxPack['adultNumber'] = $reservation->getAdultNumber();
            $auxPack['bedsType'] = '';

            $bedRoom = $currentPack->getBedRoomId();

            if ($bedRoom) {
                foreach ($bedRoom['beds'] as $currentBed) {
                    $auxPack['bedsType'] = $auxPack['bedsType'] .
                        $currentBed['typeString'] .
                        ($currentBed['amount'] == 1 ? '' : '(' . $currentBed['amount'] . ') + ');
                }
            }

            array_push($emailData['packages'], $auxPack);
        }

        $this->managerBD->save();

        $emailData['idCancelation'] = $reservationCanceled->getPublicId();
        $emailData['dateCancelation'] = $reservationCanceled->getDateCancellation()->format('Y-m-d');

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients(array($emailData['emailClient']));
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:reservationCanceled.html.twig');
        $emailService->setSubject('Cancelación de la Reserva - navicu.com');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();

        $rpContact = $this->rf->get('ContactPerson');

        $contact = $rpContact->findOneBy(
            array(
                'property' => $reservation->getPropertyId()->getId(),
                'type' => 1
                )
        );

        $emailOwner = !is_null($contact->getEmail()) ?
            $contact->getEmail() :
            $reservation->getPropertyId()->getOwnersProfiles()[0]->getUser()->getEmail();

        $emailService->setRecipients(array($emailOwner));
        $emailService->sendEmail();
    }
}