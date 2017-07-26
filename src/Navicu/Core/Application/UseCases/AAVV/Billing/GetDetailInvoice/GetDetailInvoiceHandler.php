<?php
namespace Navicu\Core\Application\UseCases\AAVV\Billing\GetDetailInvoice;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * caso de uso para obtener los detalles de una reserva
 */
class GetDetailInvoiceHandler implements Handler
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
        $rpInvoice = $rf->get('AAVVInvoice');

        $invoice = $rpInvoice->findOneByNumberAndAavv($command->get('slug'),$command->get('idInvoice'));

        if(empty($invoice))
            return new ResponseCommandBus(404,'Not Found');

        if((!$command->get('isAdmin')) && $invoice->getAavv()->getSlug() != $command->get('slug'))
            return new ResponseCommandBus(404,'Not Found');

        $data = $this->getData($invoice);

        return new ResponseCommandBus(200,'ok',$data);
    }

    private function getData($invoice)
    {
        $aavv = $invoice->getAavv();

        $address = null;
        foreach ($aavv->getAavvAddress() as $current) {
            if ($current->getTypeAddress() == 0) {
                $address = $current;
            }
        }

        $services = $invoice->getLines();

        $data = [
            'rif' => $aavv->getRif(),
            'social_reason' => $aavv->getSocialReason(),
            'commercial_name' => $aavv->getCommercialName(),
            'number_invoice' => $invoice->getNumber(),
            'date_invoice' => $invoice->getDate()->format('d-m-Y'),
            'description' => $invoice->getDescription(),
            'status' => $invoice->getStatus(),
            'tax' => $invoice->getTax(),
            'address' => isset($address) ? $address->getAddress() : '',
            'phone' => isset($address) ? $address->getPhone() : '',
            'tax_rate' => $invoice->getTaxRate(),
            'total_amount' => $invoice->getTotalAmount(),
            'groups' => [],
            'services' => [],
        ]; 

        foreach ($invoice->getAavvReservationGroup() as $group) {

            $array_group = [
                'public_id' => $group->getPublicId(),
                'date_check_in' => $group->getDateCheckIn()->format('d-m-Y'),
                'date_check_out' => $group->getDateCheckOut()->format('d-m-Y'),
                'reservations' => []
            ];

            foreach ($group->getReservation() as $reservation) {
                $pack = $reservation->getReservationPackages()[0];
                $tcp = $pack->getTypeCancellationPolicy();
                $code_Room = CoreTranslator::getTranslator($pack->getTypePack()->getCode());
                $title_room = $pack->getTypeRoom()->getTitle();
                $array_reservation = [
                    'public_id' => $reservation->getPublicId(),
                    //'date_check_in' => $reservation->getDateCheckIn()->format('d-m-Y'),
                    //'date_check_out' => $reservation->getDateCheckOut()->format('d-m-Y'),
                    'child_number' => $reservation->getChildNumber(),
                    'property' => $reservation->getPropertyId()->getName(),
                    'adult_number' => $reservation->getAdultNumber(),
                    'total_to_pay' => $reservation->getTotalToPay() * (1-$reservation->getDiscountRateAavv()),
                    'tax' => $reservation->getTax(),
                    'number_rooms' => $pack->getNumberRooms(),
                    'name_room' => empty($code_room) ? $title_room : $code_Room,
                    'name_pack' => CoreTranslator::getTranslator($pack->getTypePack()->getCode()),
                    'cancellation_policy' => isset($tcp) ? CoreTranslator::getTranslator($tcp->getCode()) : null,
                    //'packages' => [],
                ];

                /*foreach ($reservation->getReservationPackages() as $rp) {
                    $data['reservation_group']['reservations']['packages'][] = [
                        'number_rooms' => $rp->getNumberRooms(),
                        'price' => $rp->getPrice(),
                        'number_adults' => $rp->getNumberAdults(),
                        'number_kids' => $rp->getNumberKids()
                    ];
                }*/
                $array_group['reservations'][] = $array_reservation;
            }
            $data['groups'][] = $array_group;
        }

        foreach ($invoice->getLines() as $line) { 
            $data['services'][] = [
                'date' => $line->getDate()->format('d-m-Y'),
                'description' => $line->getDescription(),
                'quantity' => $line->getQuantity(),
                'price' => $line->getPrice(),
                'total' => $line->getTotal(),
            ];
         }

        return $data;
    }
}
