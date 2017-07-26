<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\SetDataReservation;

use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\LockedAvailabilityService;
use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\NotAvailableException;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVReservationGroup;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationPack;

/**
 * Validar la disponibilidades de la reserva mediante AAVV
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working:  Freddy Contreras <freddycontreras3@gmail.com>
 * @version 16/09/2016
 */
class SetDataReservationHandler implements Handler
{
    private $checkIn;

    private $checkOut;

    private $rf;

    private $command;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * @return EmailInterface
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
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso Registrar Usuario Agencia de Viaje
     *
     * @param Command|SetDataReservationCommand $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        //Asignación de valores por defecto
        $request = $command->getRequest();
        $this->checkIn = $request['checkIn'];
        $this->checkOut = $request['checkOut'];
        $this->command = $command;
        $this->rf = $rf;
        $aavvRf = $rf->get('AAVV');
        $rpReservationGroup = $rf->get('AAVVReservationGroup');
        $totalBilling = 0;
        $totalIva = 0;
        $error = null;
        $flag = true;
        $userId = CoreSession::getUser()->getId();

        try {
            if ( is_null($this->rf->get('Location')->findOneBy(['slug' => $request['location']])) ) {
                $location = $this
                    ->rf
                    ->get('Property')
                    ->findOneBy(['slug' => $request['location']])
                    ->getLocation();
                $location = is_null($location->getCityId()) ? $location->getParent() : $location->getCityId();
            } else
                $location = $this->rf->get('Location')->findOneBy(['slug' => $request['location']]);

            $aavv = CoreSession::getUser()->getAavvProfile()->getAavv();
            $reservationGroup = new AAVVReservationGroup();
            $reservationGroup
                ->setStatus(0)
                ->setCreatedAt(new \DateTime('now'))
                ->setCreatedBy($userId)
                ->setUpdatedAt(new \DateTime('now'))
                ->setUpdatedBy($userId)
                ->setDateCheckIn($this->checkIn)
                ->setDateCheckOut($this->checkOut)
                ->setAavv($aavv)
                ->setLocation($location);

            // Incluimos la relacion del perfil de la aavv que hizo la reserva
            $aavvProfile = CoreSession::getUser()->getAavvProfile();
            $aavvProfile->addAavvReservationGroup($reservationGroup);
            $reservationGroup->setAavvProfile($aavvProfile);

            $response = [
                'properties' => [],
                'checkIn' => $request['checkIn']->format('d-m-Y'),
                'checkOut' => $request['checkOut']->format('d-m-Y'),
                'locationSlug' => $location->getSlug(),
                'location' => $location->getTitle(),
                'reservation_type' => $request['reservation_type']
            ];

            if (!CoreSession::isRole('ROLE_ADMIN')) {
                $aavv = CoreSession::getUser()->getAavvProfile()->getAavv();
                $response['personalizedMail'] = CoreSession::getUser()->getAavvProfile()->getAavv()->getPersonalizedMail();
                $availabilityCredit = $aavv->getCreditAvailable();
                // Credito disponible de la aavv mas la mitad del credito de navicu
                $availabilityCreditPlusNavicuGain = ($availabilityCredit + ($aavv->getNavicuGain() / 2));
            } else {
                $availabilityCredit = 1000000000;
                $availabilityCreditPlusNavicuGain = 10000000;
                $response['personalizedMail'] = false;
            }

            // Antes de intentar reservar verificamos que tenga credito disponible
            if ( ($request['reservation_type'] == 2) AND ($availabilityCredit <= 0) ) {
                $this->sendEmailAAVV($aavv); // Enviar correos de credito insuficiente
                return new ResponseCommandBus(400, "InsufficientCredit", 4);
            }
            // Si lo que quiere hacer es una pre-reserva
            //Verificamos todos los establecimientos a reservar
            foreach ($request['properties'] as $currentProperty) {
                // Obtener la data enviada a frontend
                $response['properties'][] = $this->getDataProperty($currentProperty, $reservationGroup, $totalBilling, $totalIva, $error);
                // Si hubo un error por disponibilidad
                if ($error) {
                    $flag = false;
                    $msj = ['availability' => 'insufficientAvailability'];
                    $code = 1; // Error por falta de disponibilidad
                    break;
                }
                if ($request['reservation_type'] == 2) {
                    // Si la reserva no puede ser procesada por falta de credito
                    if ($availabilityCreditPlusNavicuGain - $totalBilling < 0) {
                        $flag = false;
                        $this->sendEmailAAVV($aavv); // Enviar correos de credito insuficiente
                        $aavvRf->save($aavv);

                        $msj = ['credit' => $availabilityCreditPlusNavicuGain - $totalBilling];
                        $code = 4; // Error por extra credito consumido
                        break;
                    }
                }
            }
            $reservationGroup->setTotalAmount($totalBilling);
            $response['groupId'] = $reservationGroup->getPublicId();
            $response['IVA'] = $totalIva;
            $response['totalBilling'] = $totalBilling;
        } catch (\Exception $e) {
            return new ResponseCommandBus(500,'Internal Error',$e->getMessage().$e->getLine());
        }

        if ($flag) {
            $rpReservationGroup->save($reservationGroup);
            return new ResponseCommandBus(200,'Ok',$response);
        } else
            return new ResponseCommandBus(400, $msj, $code);
    }

    /**
     * La siguiente función valida el inventario y
     * obtiene los datos que se enviaran a frontend
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $currentProperty
     * @param $totalBilling
     * @return array
     * @throws \Navicu\Core\Domain\Adapter\EntityValidationException
     */
    public function getDataProperty($currentProperty, $reservationGroup, &$totalBilling, &$totalIva, &$error)
    {
        $rpPack = $this->rf->get('Pack');
        $response = [];

        $property = $this->rf->get('Property')->findOneBy(['id' => $currentProperty['id']]);
        $numberOfReservation = $this->rf->get('Reservation')->countReservation();

        $response['id'] = $property->getId();
        $response['name'] = $property->getName();
        $response['publicId'] = $property->getPublicId();
        $totalToPay = 0;
        $iteration = 0;

        $auxResponse = [];

        foreach ($currentProperty['packages'] as $index => $currentPack) {
            $pack = $rpPack->findOneBy(['id' => $currentPack['id']]);
            $roomId = $pack->getRoom()->getId();

            foreach ($currentPack['cancellationPolicies'] as $keyAux => $currentPoliciesPack) {

                $idReservation = ($numberOfReservation + $iteration);
                $reservation = new Reservation($idReservation);
                $reservation
                    ->setDateCheckIn($this->checkIn)
                    ->setDateCheckOut($this->checkOut)
                    ->setPropertyId($property)
                    ->setIpAddress($this->command->get('ip'))
                    ->setReservationDate(new \Datetime('now'))
                    ->setAavvReservationGroup($reservationGroup)
                    ->setDiscountRateAavv();

                $reservationGroup->addReservation($reservation);
                
                $reservationPack = new ReservationPack();
                $reservationPack
                    ->setPackId($pack)
                    ->setTypePack($pack->getType())
                    ->setTypeRoom($pack->getRoom()->getType())
                    ->setNumberRooms($currentPoliciesPack['numRooms'])
                    ->setNumberAdults($currentPoliciesPack['numAdults'])
                    ->setNumberKids($currentPoliciesPack['numChildren'] ?
                        $currentPoliciesPack['numChildren'] : 0
                    );

                $policyCancellation = null;
                foreach ($property->getPropertyCancellationPolicies() as $pcp) {
                    if($pcp->getCancellationPolicy()->getId()==$currentPoliciesPack['id']) {
                        $policyCancellation = $pcp;
                    }
                }

                if (!is_null($policyCancellation)) {
                    $reservationPack
                        ->setReservationId($reservation)
                        ->setPropertyCancellationPolicyId($policyCancellation)
                        ->setTypeCancellationPolicy(
                            $policyCancellation
                                ->getCancellationPolicy()
                                ->getType()
                        )
                        ->setCancellationPolicy(
                            $policyCancellation
                                ->getCancellationPolicy()
                                ->toArray()
                        );
                    $reservation->addReservationPackage($reservationPack);
                    try {

                        new RateCalculator($this->rf);
                        new LockedAvailabilityService($this->rf);
                        RateCalculator::calculateRateReservation($this->rf, $reservation);
                        LockedAvailabilityService::lockAvailability($this->rf, $reservation);
                        $auxResponse['priceNotRound'] = $reservation->getTotalToPay();
                    } catch (NotAvailableException $e) {
                        LockedAvailabilityService::unlockedAvailability($this->rf);
                        $error = [
                            'error' => 'not_available',
                            'message' => $e->getMessage(),
                            'date' => $e->getDate()
                        ];
                    } catch (\Exception $e) {
                        LockedAvailabilityService::unlockedAvailability($this->rf);
                        $error = [
                            'error' => $e->getCode(),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString()
                        ];
                    };
                    $auxResponse['packId'] = $pack->getId();
                    $auxResponse['packName'] = CoreTranslator::getTranslator($pack->getType()->getCode());
                    $auxResponse['numRooms'] = $currentPoliciesPack['numRooms'];
                    $auxResponse['numAdults'] = $currentPoliciesPack['numAdults'];
                    $auxResponse['numChildren'] = $currentPoliciesPack['numChildren'] ? $currentPoliciesPack['numChildren'] : 0;
                    $auxResponse['roomId'] = $roomId;
                    $auxResponse['roomName'] = CoreTranslator::getTranslator($pack->getRoom()->generateName());
                    $auxResponse['price'] = $reservation->getTotalToPay();
                    $auxResponse['public_id'] = $reservation->getPublicId();
                    if (isset($currentPoliciesPack['ageOfChildren']))
                        $auxResponse['ageOfChildren'] = $currentPoliciesPack['ageOfChildren'];

                    $auxResponse['cancellationPolicyName'] = CoreTranslator::getTranslator($policyCancellation
                        ->getCancellationPolicy()
                        ->getType()->getCode()
                    );
                    $auxResponse['cancellationPolicy'] = $policyCancellation
                        ->getCancellationPolicy()
                        ->getId();

                    $totalIva = $totalIva + $reservation->getTaxPay();
                    $totalToPay = $totalToPay +  $auxResponse['price'];
                    $response['reservations'][] = $auxResponse;
                }
                else {
                    $error = [
                        'error' => "null policy",
                        'message' => "Policy selected does'nt exist"
                    ];
                }
                // Variable para mantener el control sobre el identificador de la reserva
                $iteration++;
            }            
        }

        $response['totalToPay'] = $totalToPay;
        $totalBilling = $totalBilling + $totalToPay;

        return $response;
    }

    /**
     * Funcion encargada de enviar un correo a la agencia de viajes cuando se ha quedado sin credito
     *
     * @param $aavv object, agencia de viajes que realiza la reserva
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 24/11/2016
     */
    private function sendEmailAAVV($aavv)
    {
        // Si no ha sido enviado por primera vez
        if (!$aavv->getSentEmailForInsufficientCredit()) {
            $emailService = $this->getEmailService();
            $emailService->setConfigEmail('first_mailer');
            $emailService->setViewParameters(['deactivateAavv' => false]);

            $emailService->setViewRender('NavicuInfrastructureBundle:Email:insufficientCreditAAVV.html.twig');
            $emailService->setSubject('Crédito insuficiente');
            $emailService->setEmailSender('info@navicu.com');

            $recipients = [];
            foreach ($aavv->getAavvProfile() as $currentProfile)
                $recipients[] = $currentProfile->getEmail();

            $emailService->setRecipients($recipients);
            $emailService->sendEmail();
            $aavv->setSentEmailForInsufficientCredit(true);
        }
    }

    /**
     * Funcion encargada de deshabilitar la agencia de viaje por falta de credito para hacer la reserva
     *
     * @param $aavv object, agencia de viaje
     * @version 24/11/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    private function disableAavv($aavv)
    {
        if ($aavv->setStatusAgency(AAVV::STATUS_ACTIVE) ) {
            $aavv->setStatusAgency(AAVV::STATUS_INACTIVE);
            $profiles = $aavv->getAavvProfile();

            foreach ($profiles as $profile) {
                $user = $profile->getUser();
                $user->setEnabled(false);
                $profile->setStatus(2);
            }
        }
    }
}