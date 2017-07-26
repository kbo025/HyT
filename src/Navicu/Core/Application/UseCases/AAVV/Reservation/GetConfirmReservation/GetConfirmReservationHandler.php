<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetConfirmReservation;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\PublicId;
use Symfony\Component\Config\Definition\Exception\Exception;

class GetConfirmReservationHandler implements Handler
{
    const LIMIT_CREDIT = 0.6;

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
        $reservationGroupRf = $rf->get('AAVVReservationGroup');
        $reservationGroup = $reservationGroupRf->findOneByArray(["public_id" => $request['public_group_id']]);

        $aavv = $reservationGroup->getAavv();
        try {
            $groupReservation = $aavv->getAavvReservationGroup();
            $length = count($groupReservation);
            $found = false;
            if ($length > 0) {
                for ($ii = 0; (($ii < $length) AND (!$found)); $ii++) {
                    $publicId = $groupReservation[$ii]->getPublicId() instanceof PublicId ?
                        $groupReservation[$ii]->getPublicId()->toString() :
                        $groupReservation[$ii]->getPublicId();
                    if (strcmp($publicId, $request['public_group_id']) == 0)
                        $found = true;
                }
                if ($found) {
                    $response = $this->buildStructure($request, $rf, $reservationGroup, $request['owner']);
                    return $response;
                }
            }
            throw new \Exception("Reservation not found", 400);
        }
        catch (\Exception $e) {
            $msj['file'] = $e->getFile();
            $msj['line'] = $e->getLine();
            $msj['message'] = $e->getMessage();
            $error['error'] = $msj;
            return new ResponseCommandBus(400, 'Bad Request', $error );
        }
    }

    /**
     * Funcion encarcagad de construir la estructura de respuesta en base a la busqueda por el id del
     * grupo de la reserva
     *
     * @param $request
     * @param $rf
     * @param $reservationsGroup
     * @param $owner bool
     * @return ResponseCommandBus
     * @author Isabel Nieto
     * @version 05/10/2016
     */
    public function buildStructure($request, $rf, $reservationsGroup, $owner)
    {
        try {
            $response = [];
            $arrayOfReservation = [];
            $arrayOfReservation['success'] = [];
            $totalAmountReservationGroup = 0;
//            $reservationGroupRf = $rf->get('AAVVReservationGroup');

//            $reservationsGroup = $reservationGroupRf->findOneByArray(["public_id" => $request['public_group_id']]);

            if (is_null($reservationsGroup))
                throw new \Exception("Reservation not found", 404);
            $reservations = $reservationsGroup->getReservation();

            if (count($reservations) >= 1 ) {
                $lengthReservation = count($reservations);
                for ($ii = 0; $ii < $lengthReservation; $ii++) {
                    $data = $this->buildResponseStructure($reservations[$ii],
                        $response, // array con la informacion de todas las reservas
                        $owner, // si se muestra la informacion como aavv o como cliente de la aavv
                        $totalAmountReservationGroup
                    );
                    // Si no es la primera reserva entonces se suma al array de las reservas existentes
                    if (!is_null($data))
                        array_push($response, $data);
                }

                $lengthReservation = count($response);
                // Transformamos los numeros con el punto decimal
                for ($ii = 0; $ii < $lengthReservation; $ii++) {
                    $response[$ii]['total_amount_per_property'] = number_format($response[$ii]['total_amount_per_property'], 2, ',', '.');
                    $innerReservation = $response[$ii]['reservation'];
                    $lengthInnerReservation = count($innerReservation);
                    for ($jj = 0; $jj < $lengthInnerReservation; $jj++) {
                        $response[$ii]['reservation'][$jj]['client_payment'] = number_format(
                                                                                $response[$ii]['reservation'][$jj]['client_payment'],
                                                                                2, ',', '.');
                    }
                }

                // Incluimos el resto de la informacion del grupo de reservas
                $check_in = $reservationsGroup->getDateCheckIn();
                $check_out = $reservationsGroup->getDateCheckOut();
                // Si la agencia tiene una razon para estar desactivada por falta de credito
                $responseData['deactivateMsg'] = ($reservationsGroup->getAavv()->getDeactivateReason() == 3) ? true : false;
                $responseData['isCustomize'] = $reservationsGroup->getAavv()->isCustomize();
                $responseData['hash_url'] = $reservationsGroup->getHashUrl();
                $responseData['customize'] = $reservationsGroup->getAavv()->getCustomize();
                $logo = $reservationsGroup->getAavv()->getdocumentByType('LOGO');
                $responseData['logo'] = is_null($logo) ? null : $logo->getDocument()->getFileName();  
                $responseData['agencyName'] = $reservationsGroup->getAavv()->getCommercialName();
                $responseData['array_of_reservations'] = $response;
                $responseData['check_in_reservation'] = $check_in->format('d-m-Y');
                $responseData['check_out_reservation'] = $check_out->format('d-m-Y');
                $responseData['location'] = $rf->get('Location')->findOneBy(['slug' => $request['location']])->getTitle();
                $responseData['locationSlug'] = $request['location'];
                $aavv = $reservationsGroup->getAavv();
	            $responseData['aavv'] = $aavv;
                $responseData['creditLessThan'] = false;
                $responseData['creditIncrement'] = $aavv->getNavicuGain() / 2;
                /* TODO: comentado para no colocar a la agencia imposibilitada de realizar mas reservas
                if (($aavv->getCreditAvailable() < self::LIMIT_CREDIT * $aavv->getCreditInitial()) && !$aavv->getSentEmailForCreditLessThan()) {
                    $responseData['creditLessThan'] = true;
                    $aavv->setSentEmailForCreditLessThan(true);
                    $rf->get('AAVV')->save($aavv);
                }*/
                $totalToPay = $reservationsGroup->getTotalAmount();
                $responseData['total_to_pay'] = number_format($totalToPay, 2, ',', '.');
                $responseData['group_reservation_id'] = $reservationsGroup->getPublicId() instanceof PublicId ?
                    $reservationsGroup->getPublicId()->toString() :
                    $reservationsGroup->getPublicId();
                $responseData['show_reservation_group'] = count($response) > 1 ? true : false;
                // Si es una pre-reserva (0) o una reserva pagada exitosamente (2)
                $responseData['reservation_type'] = $this->getStatusReservation($reservationsGroup);

                return new ResponseCommandBus(200, 'OK', $responseData);
            }
            throw new \Exception("Reservation not found", 404);
        }
        catch (\Exception $e) {
            $msj['file'] = $e->getFile();
            $msj['line'] = $e->getLine();
            $msj['message'] = $e->getMessage();

            $error['error'] = $msj;

            return new ResponseCommandBus(400, 'Bad Request', $error );
        }
    }

    /**
     * Funcion para construir la estructura de respuesta de la confirmacion de la reserva
     *
     * @param $reservation object
     * @param array $responseFinal
     * @param $owner bool si es o no la informacion para la aavv
     * @param $totalAmountReservationGroup total a pagar por el grupo de la reserva
     * @return array|null
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 05/10/2016
     */
    public function buildResponseStructure($reservation, &$responseFinal=[], $owner, &$totalAmountReservationGroup)
    {
        $position = -1;
        if (isset($responseFinal))
            $position = $this->findExistingReservation($reservation->getPropertyId()->getSlug(), $responseFinal);

        // Datos del cliente
        $responseObj['client_names'] = $reservation->getClientId()->getFullName();
        $responseObj['client_document_id'] = $reservation->getClientId()->getIdentityCard();
        $responseObj['client_phone'] = ($reservation->getClientId()->getPhone() instanceof Phone) ?
            $reservation->getClientId()->getPhone()->toString() :
            $reservation->getClientId()->getPhone();
        $responseObj['client_email'] = ($reservation->getClientId()->getEmail() instanceof EmailAddress) ?
            $reservation->getClientId()->getEmail()->toString() :
            $reservation->getClientId()->getEmail();

        // Datos de la reserva
        $responseObj['reservation_public_id'] = $reservation->getPublicId() instanceof PublicId ?
            $reservation->getPublicId()->toString() :
            $reservation->getPublicId();
        $responseObj['number_adults'] = $reservation->getAdultNumber();
        $responseObj['number_children'] = $reservation->getChildNumber();

        $currentReservationPack = $reservation->getReservationPackages()[0];
        // Datos de la habitacion
        $name = $currentReservationPack->getRoomName();
        $pack['pack_name'] = CoreTranslator::getTranslator($currentReservationPack
            ->getTypePack()
            ->getCode());
        $pack['policy_cancellation_name'] = CoreTranslator::getTranslator($currentReservationPack
            ->getTypeCancellationPolicy()
            ->getCode()
        );
        $responseObj['room_name'] = $name;
        $responseObj['package'] = $pack;
        $responseObj['number_rooms'] = $currentReservationPack->getNumberRooms();

        $array_children_age = [];
        foreach ($currentReservationPack->getChildrenAge() as $childrenAge)
            array_push($array_children_age, $childrenAge->getAge());
        $responseObj['children_age'] = $array_children_age;

        // Si es la primera vez que se esta agregando la reserva
        if ($position == -1) {
            $response = [];
            $response['reservation'] = [];

            // Datos del property
            $response['property_public_id'] = $reservation->getPropertyId()->getPublicId() instanceof PublicId ?
                $reservation->getPropertyId()->getPublicId()->toString() :
                $reservation->getPropertyId()->getPublicId();

            $response['property_name'] = $reservation->getPropertyId()->getName();
            $response['property_star'] = $reservation->getPropertyId()->getStar();
            $response['property_slug'] = $reservation->getPropertyId()->getSlug();
            // buscamos el estado al que pertenece el establecimiento
            $location = $reservation->getPropertyId()->getLocation();
            if ($location->getLvl() >=1) {
                do {
                    if ($location->getParent()->getLvl() >= 1) {
                        $location = $location->getParent();
                    }
                } while ($location->getParent()->getLvl() >= 1);
            }
            $response['property_location'] = $location->getTitle();

            // Monto Total de la reserva por property
            $response['total_amount_per_property'] = $owner ? abs($reservation->getTotaltoPay() - $reservation->getTotalForAavv()) : $reservation->getTotalToPay();

            $totalAmountReservationGroup = $response['total_amount_per_property'];

            // Monto total de la reserva
            $responseObj['client_payment'] = $owner ? abs($reservation->getTotaltoPay() - $reservation->getTotalForAavv()) : $reservation->getTotalToPay();
            array_push($response['reservation'],$responseObj);

            return $response;
        }
        else {
            $total = $responseFinal[$position]['total_amount_per_property'];
            $total += $owner ? abs($reservation->getTotaltoPay() - $reservation->getTotalForAavv()) : $reservation->getTotalToPay();

            $responseFinal[$position]['total_amount_per_property'] = $total;

            $totalAmountReservationGroup += $total;

            $responseObj['client_payment'] = $owner ? abs($reservation->getTotaltoPay() - $reservation->getTotalForAavv()) : $reservation->getTotalToPay();
            array_push($responseFinal[$position]['reservation'],$responseObj);
        }
        return null;
    }

    /**
     * Funcion encargada de buscar si el slug del property ya ha agregado una habitacion reservada
     *
     * @param $propertySlug string
     * @param $response array, array con todos los objetos construidos para la reserva
     * @return int
     * @version 2016/09/27
     */
    private function findExistingReservation($propertySlug, $response)
    {
        $length = count($response);

        for ($ii = 0; $ii < $length; $ii++)
            if (strcmp($response[$ii]['property_slug'], $propertySlug) == 0)
                return $ii;
        return -1;
    }

    /**
     * Funcion encargada de obtener el estado del grupo de reserva
     *
     * @param $reservationGroup
     * @return int
     */
    private function getStatusReservation($reservationGroup)
    {
        $paid = true;
        $reservations = $reservationGroup->getReservation();
        foreach ($reservations as $reservation) {
            if (count($reservation->getPayments()) == 0)
                $paid = false;
            foreach ($reservation->getPayments() as $payment) {
                $paid = $paid && ($payment->getState() == 1);
            }
        }
        if ($paid)
            return 2; //reservas aceptadas
        return 0; // pre-reservas
    }
}
