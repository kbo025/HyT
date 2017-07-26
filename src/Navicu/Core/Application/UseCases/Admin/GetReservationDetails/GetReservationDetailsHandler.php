<?php
namespace Navicu\Core\Application\UseCases\Admin\GetReservationDetails;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * La siguiente clase implementa el handler del caso de uso "getReservationDetail"
 * Donde de consultará los detalles de una estructura
 *
 * Class GetDetailsPropertyHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 26/10/2015
 */
class GetReservationDetailsHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 26/10/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $request = $command->getRequest();
        $rfReservation = $rf->get('Reservation');
        $rfProperty = $rf->get('Property');
        $reservation = $rfReservation->findOneBy(['public_id' => $request["publicId"]]);

        //Si no exite la reservacion se retorna un 404
        if (!$reservation)
            return new ResponseCommandBus(404, 'Not Found');

        $reservation->roundReservation();

        $validPermiss = false;
        if (!is_null($request["userSession"])) {
            if ($request["userSession"]->getOwnerProfile()) {
                $ownerProperty = $request["userSession"]->getOwnerProfile()->getProperties();
                $validPermiss = false;
                for ($p = 0; $p < count($ownerProperty); $p++) {
                    if ($ownerProperty[$p]->getName() == $reservation->getPropertyId()->getName())
                        $validPermiss = true;
                }

            }else if(!is_null($request['userSession']->getClientProfile())){
                $clientReservations = $request['userSession']->getClientProfile()->getReservations();
                $validPermiss = false;
                foreach ($clientReservations as $clientReservation) {
                    if($clientReservation->getPublicId() == $request["publicId"])
                        $validPermiss = true;
                }
            } else if (CoreSession::havePermissons('admin_affiliates','read'))
                $validPermiss = true;
        }

        //si esa reserva no pertenece al establecimiento o al cliete se retorna 404
        if (!$validPermiss)
            return new ResponseCommandBus(403, 'Forbidden');
        
        // Si existe reservación
        if (!$reservation)
            return new ResponseCommandBus(404, 'Not Found');

        $reservation->roundReservation();

        //Datos generales de la reserva
        $reserveCreation = $reservation->getReservationDate();

        $clientProfile = $request['userSession']->getClientProfile();
        $total = round($reservation->getTotalToPay(),!is_null($clientProfile) ? 0 : 2);
        $tax = round($reservation->getTaxPay(),!is_null($clientProfile) ? 0 : 2);
        $subtotal = round($total - $tax,!is_null($clientProfile) ? 0 : 2);
        $hotelAmount = $reservation->getNetRate();
        $clientReservation = $reservation->getClientId();

        $totalAAVV = $reservation->getTotalToPay() * $reservation->getDiscountRateAavv();
 
        $dataClient = [
            'nameClient' => is_null($clientReservation) ? null : $clientReservation->getFullName(),
            'identityCard' => is_null($clientReservation) ? null : $clientReservation->getIdentityCard(),
            'nationality' => null,
            'phone' => is_null($clientReservation) ? null : $clientReservation->getPhone(),
            'email' => is_null($clientReservation) ? null : $clientReservation->getEmail(),
            'informationEmail' => is_null($clientReservation) ? null : $clientReservation->getEmailNews(),
        ];

        $response = [
            'idReserve' => $reservation->getPublicId(),
            'checkin' => $reservation->getDateCheckIn()->format('d-m-Y'),
            'checkout' => $reservation->getDateCheckOut()->format('d-m-Y'),
            'reserveCreationDate' => $reserveCreation->format('d-m-Y'),
            'reserveCreationTime' => $reserveCreation->format('H:i:s'),
            'reserveCreationLocation' => null,
            'numberAdults' => $reservation->getAdultNumber(),
            'numberChildren' => $reservation->getChildNumber(),
            'status' => $reservation->getStatus(),
            'total' => $total,
            'totalProperty' => $reservation->getNetRate(),
            'totalNavicu' => $total - $reservation->getNetRate() - $totalAAVV,
            'totalAAVV' => $totalAAVV,
            'tax' => $tax,
            'discountRate' => ($reservation->getDiscountRate()*100),
            'arrivalTime' => null,
            'subTotal' => $subtotal,
            'observations' => $reservation->getSpecialRequest(),
            'paymentType' => $reservation->getPaymentType(),
            'propertyName' => $reservation->getPropertyId()->getName(),
            'statusPaymentType' => $reservation->getPaymentType() == 1 ?
                [['status' => 2, 'name' => 'Confirmada']] :
                [
                    ['status' => 0, 'name' => 'Pre Reserva'],
                    ['status' => 1, 'name' => 'Proceso de Confirmación'],
                    ['status' => 2, 'name' => 'Confirmada'],
                    ['status' => 3, 'name' => 'Cancelada']
                ],
            'paymentTypes' => [
                ['code' => PaymentGateway::BANESCO_TDC, 'name' => 'TDC Instapago'],
                ['code' => PaymentGateway::NATIONAL_TRANSFER, 'name' => 'Tranferencia Nacional'],
                ['code' => PaymentGateway::STRIPE_TDC, 'name' => 'TDC Stripe'],
                ['code' => PaymentGateway::INTERNATIONAL_TRANSFER, 'name' => 'Transferencia Internacional'],
                ['code' => PaymentGateway::AAVV, 'name' => 'AAVV'],
                ['code' => PaymentGateway::PAYEEZY, 'name' => 'TDC payeezy'],
            ],
            'type' => $reservation->getAavvReservationGroup() == null ? 'Web' : 'AAVV',
            'paymentMethod' => $this->translatePaymentType($reservation->getPaymentType()),
            'currency' => $reservation->getCurrencyConvertionInformation() == null ? 'VEF' : $reservation->getCurrencyConvertionInformation()['alphaCurrency'],
            'ceco' => $reservation->getPropertyId()->getLocation()->getRoot()->getAlfa2(),
            'detailsPerson' => $dataClient,
        ];

        //Detalles de pago de la reserva
        $response['payments'] = [];
        foreach($reservation->getPayments() as $payment) {
            $bank = $payment->getBank();
            $response['payments'][] = [
                'date' => $payment->getDate()->format('d-m-Y'),
                'type' => $payment->getType(),
                'state' => $payment->getStatus(),
                'message' => null,
                'currency' => $payment->isForeignCurrency() ? $payment->getAlphaCurrency() : 'VEF',
                'bank' => isset($bank) ? $bank->getTitle() : null,
                'holder' => $payment->getHolder(),
                'holderId' => $payment->getHolderId(),
                'amount' => $payment->getAmount(),
                'IpCountry' => null,
                'city' => null,
                'idConfirmation' => $payment->getReference()
            ];
        }

        $response['packages'] = array();
        $packages = $reservation->getReservationPackages();

        foreach ($packages as $currentPack) {
            $auxPack = array();
            $auxPack['numberRoom'] = $currentPack->getNumberRooms();
            $auxPack['roomName'] = $currentPack->getPackId()->getRoom()->generateName();
            $auxPack['bedsType'] = null;

            $bedRooms = $currentPack->getBedRoomId();
            //Obtener el nombre de los tipos de camas
            if ($bedRooms) {
                $maxBed = count($bedRooms['beds']);
                $i = 1;
                foreach ($bedRooms['beds'] as $currentBed) {
                    if ($i != $maxBed)
                        $auxPack['bedsType'] = $auxPack['bedsType'] . $currentBed['typeString'] . ' + ';
                    else
                        $auxPack['bedsType'] = $auxPack['bedsType'] . $currentBed['typeString'];
                    $i++;
                }
            }

            $auxPack['namePack'] = CoreTranslator::getTranslator($currentPack->getPackId()->getType()->getCode());
            //Obtener el nombre de la politica de cancelación
            $auxPack['nameCancellationPolicy'] =
                CoreTranslator::getTranslator(
                $currentPack
                    ->getPropertyCancellationPolicyId()
                    ->getCancellationPolicy()
                    ->getType()
                    ->getCode());

            array_push($response['packages'], $auxPack);
        }            

        return new ResponseCommandBus(200, 'Ok', $response);
    }

    private function translatePaymentType($type) {

        $response = null;

        switch ($type){
            case 1:
                $response = 'BCO';
                break;
            case 2:
                $response = 'TRF';
                break;
            case 3:
                $response = 'STR';
                break;
            case 4:
                $response = 'TRF';
                break;
            case 5:
                $response = 'TAC';
                break;
            case 6:
                $response = 'PYZ';
                break;
        }
        return $response;
    }


}