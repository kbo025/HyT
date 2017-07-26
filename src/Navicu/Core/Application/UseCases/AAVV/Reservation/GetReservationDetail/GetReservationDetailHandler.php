<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 11/10/16
 * Time: 09:34 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetReservationDetail;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\PublicId;

class GetReservationDetailHandler implements Handler
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
        $reservationRf = $rf->get('Reservation');
        $aavvRf = $rf->get('AAVV');
        $reservation = $reservationRf->findOneByArray(["public_id"=>$request['id_reservation']]);

        $response = $this->buildStructure($reservation,$aavvRf);

        $bankTypeRepository = $rf->get('BankType');  
        $foreignBankList = $bankTypeRepository->getListBanksArray(true);
        $localBankList = $bankTypeRepository->getListBanksArray(false);
        $response['foreignBankList'] = $foreignBankList;
        $response['localBankList'] = $localBankList;

        return new ResponseCommandBus(200, "ok", $response);
    }

    /**
     * Funcion constructuro de la estructura con los datos de la reserva
     *
     * @param $reservation
     * @return ResponseCommandBus
     * @version 2016/10/13
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    private function buildStructure($reservation,$aavvRf)
    {
        $response = [];

        if (is_null($reservation))
            return new ResponseCommandBus(400, "Reservation does'nt exist", $response);
        else {
            $group = $reservation->getAavvReservationGroup();
            if ($group) {
                $response['aavvName'] = $group->getAavv()->getCommercialName();
            }
            $date = $reservation->getReservationDate();
            $response['reservationDate'] = $date->format('d/m/Y');
            $response['reservationTime'] = $date->format('H:m:s');
            $status = $reservation->getStatus();
            $response['status'] = CoreTranslator::getTranslator('aavv.reservations.status.'.$status);
            $response['statusNumber'] = $status;

            $state = $reservation->getPropertyId()->getLocation();
            // Buscamos el estado al que pertenece el hotel donde la reserva se realizo
            if ($state->getLvl() > 2) {
                $found = false;
                do {
                    if ($state->getLvl() >= 2)
                        $state = $state->getParent();
                    else
                        $found = true;
                } while (!$found);

            }
            $response['state'] = $state->getTitle();
            $response['country'] = $state->getParent()->getTitle();

            $response['idReservation'] = $reservation->getPublicId() instanceof PublicId ?
                $reservation->getPublicId()->toString() :
                $reservation->getPublicId();

            $response['checkIn'] = $reservation->getDateCheckIn()->format('d/m/Y');
            $response['checkOut'] = $reservation->getDateCheckOut()->format('d/m/Y');

            // Buscamos los datos del cliente
            $client = $reservation->getClientId();
            $response['fullName'] = $client->getFullName();
            $response['email'] = $client->getEmail() instanceof EmailAddress ?
                $client->getEmail()->toString() :
                $client->getEmail();
            $response['phone'] = $client->getPhone() instanceof Phone ?
                $client->getPhone()->toString() :
                $client->getPhone();
            $response['identityCard'] = $client->getIdentityCard();

            // Buscamos los datos de la cantidad de personas
            $response['numberAdult'] = $reservation->getAdultNumber();
            $response['numberChild'] = $reservation->getChildNumber();

            // Buscamos la informacion de la reserva
            $sellRate = $reservation->getTotalToPay();
            $response['sellRate'] = number_format($sellRate,2,",",".");
            foreach ($reservation->getReservationPackages() as $currentPack) {
                $response['roomName'] = $currentPack->getRoomName();
                $response['namePack'] = CoreTranslator::getTranslator(
                    $currentPack
                        ->getTypePack()
                        ->getCode()
                );
                $response['totalRooms'] = $currentPack->getNumberRooms();
                $response['cancellationPolicy'] = CoreTranslator::getTranslator($currentPack
                    ->getPropertyCancellationPolicyId()
                    ->getCancellationPolicy()
                    ->getType()
                    ->getCode()
                );
            }

            $aavv = $reservation->getAavvReservationGroup()->getAavv();
            if (is_null($aavv->getAgreement())) {
                $error = [];
                $error['message']['message'] = "¡Caution! Terms and Condition not found";
                return new ResponseCommandBus(400, 'Bad Request', $error);
            }
            else {
                $discountRate = $aavv->getAgreement()->getDiscountRate();
                // Calculamos cuanto es lo que le la aavv cancelara a navicu tras la reserva
                $discount = $reservation->getTotalToPay() * $discountRate;
                $netRate = $reservation->getTotalToPay() - $discount;
                $response['netRate'] = number_format($netRate,2,",",".");
                $response['discountRate'] = $discountRate*100;
            }

            //Datos el property
            $property = $reservation->getPropertyId();
            $response['propertyName'] = $property->getName();
            return $response;
        }

    }
}