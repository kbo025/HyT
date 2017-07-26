<?php
namespace Navicu\Core\Application\UseCases\Admin\GetDetailsProperty;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * El siguiente handler procesa los datos del establecimiento afiliados
 *  *
 * Class GetDetailsPropertyHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 26/10/2015
 */
class GetDetailsPropertyHandler implements Handler
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
        $this->rf = $rf;
        $slug = $command->getSlug();
        $request = $command->getRequest();

        $rpProperty = $this->rf->get('Property');
        $property = $rpProperty->findOneBy(array('slug' => $slug));
        $searchVector = "search_vector";
        //$request['search'] = $request['search']." ".$property->getName();
        $request['slug'] = $property->getSlug(); //HACK para buscar por slug, debe hacerse mas generico
        $dataFilter = $rpProperty->getPropertyDetailFromFilter($request, $searchVector);
        if ($property) {
            $response = array();
            $response['slug'] = $property->getSlug();
            $response['propertyName'] = $property->getName();
            $response['locationType'] = CoreTranslator::getTranslator(
                $property->getAccommodation()->getCode(),
                'propertytype'
            );
            $response['countryCode'] = $property->getLocation()->getRoot()->getAlfa2();
            $response['chain'] = $property->getHotelChainName();
            $response['ranking'] = null;
            $response['status'] = $property->getActive();
            $response['id'] = $property->getPublicId();
            //Procesa los datos del detalle del establecimiento
            $response['details'] = array();
            $response['details']['address'] = $property->getAddress();
            $location = $property->getLocation();
            $response['details']['country'] = $location->getRoot()->getTitle();
            $response['details']['city'] =
                !is_null($location->getCityId()) ?
                    $location->getCityId()->getTitle() : $location->getParent()->getTitle();

            $response['details']['state'] = $location->getParent()->getParent()->getTitle();
            $response['details']['web'] = $property->getUrlWeb();
            $collector = $property->getRecruit();
            $response['details']['collector'] = $collector ? $collector->getFullName() : null;
            $response['details']['registrationDate'] = $property->getRegistrationDate()->format('Y-m-d');
            $response['details']['joinDate'] = $property->getJoinDate()->format('Y-m-d');
            $executive = $property->getCommercialProfile();
            $response['details']['accountExecutive'] = $executive ? $executive->getFullName() : null;
            $response['details']['registeredUser'] = $property->getOwnersProfiles()[0]->getUser()->getUserName();
            $response['propertyResponsible'] = array();
            $contacts = $property->getContacts();
            foreach ($contacts as $currentContact) {
                $auxContact = array();
                $auxContact['name'] = $currentContact->getName();
                $auxContact['position'] = $currentContact->getCharge();
                $auxContact['phone'] = $currentContact->getPhone();
                $auxContact['email'] = $currentContact->getEmail();

                array_push($response['propertyResponsible'], $auxContact);
            }

            // Procesa los datos de reserva realizadas en el establecimiento
            $response['reservation'] = array();
            $response['reservationStatus'] = $rf->get('Reservation')->getStatesList();
            $reservations = $property->getReservations();

            $totalBilling = 0;
            $totalDays = 0;

            foreach ($reservations as $currentReservation) {
                $currentReservation->roundReservation();
                $totalDays = $totalDays +
                            $currentReservation->getDateCheckOut()->format('Y-m-d') -
                            $currentReservation->getDateCheckIn()->format('Y-m-d') + 1;
                if($currentReservation->getStatus() != 3) // no incluir reserva cancelada
                    $totalBilling = $totalBilling + $currentReservation->getTotalToPay();
            }

            // Data con la informacion para realizar las busquedas de las reservas
            $response['reservation'] = $dataFilter['data'];
            $response['pagination'] = $dataFilter['pagination'];

            // Precosa los datos de las estadisticas de reserva y pago del establecimiento
            $response['propertyStatistics'] = array();
            $response['propertyStatistics']['production'] = round($totalBilling, 2);
            $response['propertyStatistics']['netProduction'] = round($totalBilling * $property->getDiscountRate(), 2);
            $response['propertyStatistics']['reservationsNumbers'] = count($reservations);
            $response['propertyStatistics']['adrExpected'] = null;
            $response['propertyStatistics']['adrSold'] = $totalDays > 0 ? round($totalBilling / $totalDays, 2) : 0;
            //**** Se debe arreglar el calculo del este valor ****
            $response['propertyStatistics']['revPAR'] = $totalDays > 0 ? round($totalBilling / $totalDays, 2) : 0;
            $response['propertyStatistics']['averageAvailability'] = null;
            $response['propertyStatistics']['advance'] = null;
            $response['propertyStatistics']['roomTypeTopSellers'] = null;
            $response['propertyStatistics']['monthWithMoreSales'] = null;
            $response['propertyStatistics']['fullProfile'] = null;
            $response['propertyStatistics']['basedAvailability'] = null;
            $response['propertyStatistics']['commission'] = $property->getDiscountRate() * 100;
            $response['propertyStatistics']['conversion'] = null;
            // Devuelve el captador si existe
            $response['recruit'] = null;
            if ($property->getRecruit()) {
                $response['recruit']['name'] = $property->getRecruit()->getFullName();
                $response['recruit']['id'] = $property->getRecruit()->getId();
            }

            $arrayCommercial = $this->getCommercials();
            $response['commercials'] = $arrayCommercial;
            return new ResponseCommandBus(200, 'Ok', $response);
        } else {
            return new ResponseCommandBus(404,'Not Found');
        }
    }

    /**
     * Funcion encargada de listar los vendedores existentes
     *
     * @return array
     */
    public function getCommercials()
    {
        $rpCommercialProfile =  $this->rf->get('NvcProfile');
        $commercials = $rpCommercialProfile->findAll();
        $response = [];
        foreach ($commercials as $currentCommercial) {
            $auxCommercial['id'] = $currentCommercial->getId();
            $auxCommercial['name'] = $currentCommercial->getFullName();
            $response[] = $auxCommercial;
        }
        return $response;
    }
}