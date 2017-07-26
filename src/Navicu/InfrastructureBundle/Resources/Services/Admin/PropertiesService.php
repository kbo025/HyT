<?php

namespace Navicu\InfrastructureBundle\Resources\Services\Admin;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PropertiesService
 *
 * La siguiente clase se encarga de declarar los servicios
 * referentes a los establecimientos del sistema
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 07/08/2015
 * @package Navicu\InfrastructureBundle\Resources\Services\Admin
 */
class PropertiesService
{
    private $rf;

    /**
     * Constructor del servicio
     *
     * @param RepositoryFactory $rf
     */
    public function __construct($rf)
    {
        $this->rf= $rf;
    }

    /**
     * La siguiente función se encarga de retornar un array
     * con los datos de los establecimientos temporales
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 08/09/2015
     * @return array
     */
    public function getTempProperties()
    {
        $response = [];
        $rpTempOwner = $this->rf->get('TempOwner');
        $rpLocation = $this->rf->get('Location');
        $rpAccommodation = $this->rf->get('Accommodation');
        $tempProperties = $rpTempOwner->findAll();


        foreach ($tempProperties as $currentTempProperty) {

            $dataProperty = $currentTempProperty->getPropertyForm();
            $dataAgreement = $currentTempProperty->getTermsAndConditionsInfo();


            //$auxResponse es un variable temporal en en ciclo (foreach)
            $auxResponse = [];

            $auxResponse['signUpDate'] = $currentTempProperty->getExpiredate()->modify('-30 day')->format('Y-m-d');
            $auxResponse['userName'] = $currentTempProperty->getSlug();
            $auxResponse['propertyName'] = (isset($dataProperty['name'])) ? $dataProperty['name'] : null;
            $auxResponse['phone'] = isset($dataProperty['phones']) ? $dataProperty['phones'] : null;
            $progress = $currentTempProperty->evaluateProgress();
            $auxResponse['progress'] = $progress;
            $auxResponse['completed'] = $progress == 100 ? true : false;
            $auxResponse['discount_rate'] = isset($dataAgreement['discount_rate']) ? $dataAgreement['discount_rate'] : 0.3;

            //Precesando el location
            if (isset($dataProperty['location'])) {
                $currentLocation = $rpLocation->findOneBy(array('id' => $dataProperty['location']));
                if (!is_null($currentLocation))  {
                    $auxResponse['location'] =
                        !is_null($currentLocation->getCityId()) ?
                            $currentLocation->getCityId()->getTitle() : $currentLocation->getParent()->getTitle();
                } else
                    $auxResponse['location'] = null;

            } else
                $auxResponse['location'] = null;

            //Procesando los datos del contacto
            if (isset($dataProperty['contacts']) && !empty($dataProperty['contacts'])) {
                $auxResponse['nameContact'] = array_pop($dataProperty['contacts'])['name'];
            } else
                $auxResponse['nameContact'] = null;

            //Procesando el tipo de establecimiento
            if (isset($dataProperty['accommodation'])) {
                $accommodation = $rpAccommodation->getById($dataProperty['accommodation']);
                $auxResponse['propertyType'] = !is_null($accommodation) ?
                    $accommodation->getTitle() :
                    null;
            } else {
                $auxResponse['propertyType'] = null;
            }

            array_push($response, $auxResponse);
        }

        return $response;
    }

    /**
     *  la siguiente funcion retorna un array con la ubicacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 10/11/2015
     *
     * @param Location $location
     * @return Array
     */
    private static function getArrayLocation($location)
    {
        $response = array();
        if ($location->getLvl()==3) {
            $response['parish'] = $location
                ->getTitle();
            $response['parish_id'] = $location
                ->getId();
            $city = $location->getCityId();
            if (!empty($city)) {
                $response['parish'] = $response['parish'].'('.$location->getCityId()->getTitle().')';
            }
            $response['city'] = $location
                ->getParent()
                ->getTitle();
            $response['city_id'] = $location
                ->getParent()
                ->getId();
            $response['state'] = $location
                ->getParent()
                ->getParent()
                ->getTitle();
            $response['state_id'] = $location
                ->getParent()
                ->getParent()
                ->getId();
            $response['country'] = $location
                ->getParent()
                ->getParent()
                ->getParent()
                ->getTitle();
            $response['country_id'] = $location
                ->getParent()
                ->getParent()
                ->getParent()
                ->getId();
        }
        if ($location->getLvl()==2) {
            $response['parish'] = null;
            $response['parish_id'] = null;
            $response['city_id'] = $location
                ->getTitle();
            $response['city'] = $location
                ->getId();
            $response['state'] = $location
                ->getParent()
                ->getTitle();
            $response['state_id'] = $location
                ->getParent()
                ->getId();
            $response['country'] = $location
                ->getParent()
                ->getParent()
                ->getTitle();
            $response['country_id'] = $location
                ->getParent()
                ->getParent()
                ->getId();
        }
        if ($location->getLvl()==1) {
            $response['parish'] = null;
            $response['city'] = null;
            $response['parish_id'] = null;
            $response['city_id'] = null;
            $response['state'] = $location
                ->getTitle();
            $response['state_id'] = $location
                ->getId();
            $response['country'] = $location
                ->getParent()
                ->getTitle();
            $response['country_id'] = $location
                ->getParent()
                ->getId();
        }
        if ($location->getLvl()==0) {
            $response['parish'] = null;
            $response['parish_id'] = null;
            $response['city'] = null;
            $response['city_id'] = null;
            $response['state'] = null;
            $response['state_id'] = null;
            $response['country'] = $location
                ->getTitle();
            $response['country_id'] = $location
                ->getId();
        }
        return $response;
    }

    /**
     *  la siguiente funcion retorna la data que se necesita para editar la informacion general del establecimiento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 10/11/2015
     *
     * @param String $slug
     * @throws \NotFoundHttpException
     * @return Array
     */
    public function getGeneralInformationData($slug)
    {
        $response = [];
        $rProperty = $this->rf->get('Property');
        $rLocation = $this->rf->get('Location');
        $rContact = $this->rf->get('ContactPerson');
        $property = $rProperty->findOneBy(array('slug'=>$slug));
        if ($property) {
            $location = $rLocation->find($property->getLocation());
            $information = array();
            $information['location'] = array(
                'name' => $property->getName(),
                'accommodation_id' => $property->getAccommodation()->getId(),
                'accommodation_name' => $property->getAccommodation()->getTitle(),
                'star' => $property->getStar(),
                'phone' => ($property->getPhones() instanceof Phone ?
                    $property->getPhones()->toString() :
                    $property->getPhones()),
                'fax' => ($property->getFax() instanceof Phone ?
                    $property->getFax()->toString() :
                    $property->getFax()),
                'address' => array_merge(
                    self::getArrayLocation($location),
                    array('coordinates' => ($property->getCoordinates() instanceof Coordinate ?
                        $property->getCoordinates()->toArray() :
                        $property->getCoordinates()),
                        'address' => $property->getAddress()
                    )
                )
            );
            $hotelChainName = $property->getHotelChainName();
            $information['propertyDetails'] = array(
                'opening_year' => $property->getOpeningYear(),
                'renewal_year' => $property->getRenewalYear(),
                'public_areas_renewal_year' => $property->getPublicAreasRenewalYear(),
                'url_web' => ($property->getUrlWeb() instanceof Url ?
                    $property->getUrlWeb()->toString() :
                    $property->getUrlWeb()),
                'hotel_chain' => !empty($hotelChainName),
                'hotel_chain_name' => $hotelChainName,
                'amount_room' => $property->getAmountRoom(),
                'number_floor' => $property->getNumberFloor(),
                'check_in' => $property->getCheckIn()->format('H:i'),
                'check_out' => $property->getCheckOut()->format('H:i'),
                'languages' => array()
            );
            foreach ($property->getLanguages() as $language) {
                $information['propertyDetails']['languages'][] = array(
                    'language_name' => $language->getNative(),
                    'language_id' => $language->getId()
                );
            }
            $information['contacts'] = array();
            foreach ($property->getContacts() as $contact) {
                $newContact = $contact->toArray();
                $newContact['type'] = $rContact->getNameType($newContact['type']);
                $information['contacts'][] = $newContact;
            }
            $comercialRooms = $property->getComercialRooms();
            $bedsPriorNotice =  $property->getBedsPriorNotice();
            $cribsPriorNotice = $property->getCribsPriorNotice();
            $information['additionalInformation'] = array(
                'check_in_age' => $property->getCheckInAge(),
                'child' => $property->getChild(),
                'agePolicy' => $property->getAgePolicy(),
                'extras' => array(
                    'all_included' => $property->getAllIncluded(),
                    'beds' => array(
                        'beds' => $property->getBeds(),
                        'prior_notice' => !empty($bedsPriorNotice),
                        'value_beds_extra' => $property->getBedsAdditionalCost()
                    ),
                    'cribs' => array(
                        'cribs' => $property->getCribs(),
                        'max_cribs' => $property->getCribsMax(),
                        'prior_notice' => !empty($cribsPriorNotice),
                        'value_cribs' => $property->getCribsAdditionalCost()
                    ),
                    'pets' => array(
                        'pets' => $property->getPets(),
                        'value_pets' => $property->getPetsAdditionalCost()
                    ),
                    'comercialRooms' => array(
                        'comercial_rooms' => !empty($comercialRooms),
                        'amount_comercial_rooms' => $comercialRooms
                    ),
                    'design_view_property' => $property->getDesignViewProperty()
                )
            );
            $information['paymentsAccepted'] = array(
                'credit_card' => array(
                    'credit_card' => $property->getCreditCard(),
                    'credit_card_american' => $property->getCreditCardAmex(),
                    'credit_card_master' => $property->getCreditCardMc(),
                    'credit_card_visa' => $property->getCreditCardVisa()
                ),
                'cash' => array(
                    'cash' => $property->getCash(),
                    'to' => $property->getCash() ? $property->getMaxCash() : null
                ),
                'debit' => $property->getDebit()
            );
            $cityTax = $property->getCityTax();
            $cityCurrency = $property->getCityTaxCurrency();
            $information['loadingRates'] = array(
                'tax' => array(
                    'currency_name' => $property->getCurrency()->getTitle(),
                    'currency_id' => $property->getCurrency()->getId(),
		            'tax' => ($property->getTax() ? 1 : 0),
                    'tax_rate' => $property->getTaxRate() * 100
                ),
                'cityTax' => array(
                    'city_tax' => empty($cityTax) ? null : $cityTax*100,
                    'city_tax_currency_id' => !empty($cityCurrency) ?
                        $cityCurrency->getId() :
                        null, // ID de la moneda
                    'city_tax_currency_name' => !empty($cityCurrency) ?
                        $cityCurrency->getTitle() :
                        null, // Nombre de la moneda
                    'city_tax_max_nights' => $property->getCityTaxMaxNights(),
                    'city_tax_type' => $property->getCityTaxType()
                ),
                'rate_type' => $property->getRateType(),
                'quota_basis' => $property->getBasicQuota()
            );
            $information['descriptions'] = array(
                'description' => $property->getDescription(),
                'additional_info' => $property->getAdditionalInfo(),
            );
            $response['slug'] = $slug;
            $response['GeneralInformation'] = $information;
            $response['accommodations'] = $this
                ->rf
                ->get('Accommodation')
                ->getAccommodationList();
            $response['countries'] = $rLocation->getAll();
            $response['currency'] = $this
                ->rf
                ->get('CurrencyType')
                ->getAllCurrency();
            $response['language'] = $this
                ->rf
                ->get('Language')
                ->getLanguagesList();
        } else {
            throw new NotFoundHttpException();
        }
        return $response;
    }

    /**
     * Funcion que arma la estructura de servicios para ser entregada a frontend
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param string $slug
     * @return array
     */
    public function getServicesData($slug)
    {
        $response = [];
        $rProperty = $this->rf->get('Property');
        $rServicesType = $this->rf->get('ServiceType');
        $property = $rProperty->findOneBy(array('slug'=>$slug));
        $status = false;
        if ($property) {
            $allServices = $rServicesType->findBy(array('lvl' => 0));
            $response['services'] = $this->getServiceStructure($allServices,$property->getServices(),$status);
            $response['typeFood'] = $this
                ->rf
                ->get('FoodType')
                ->findListAll();
            $response['typeBar'] = $this
                ->rf
                ->get('Bar')
                ->getBarTypesArray();
            $response['typeSalon'] = $this
                ->rf
                ->get('Salon')
                ->getSalonTypesArray();
            $response['buffetCarta'] = $this
                ->rf
                ->get('Restaurant')
                ->getBuffetCartaTypesArray();
            $response['slug'] = $slug;
       } else
            throw new NotFoundHttpException();
        return $response;
    }

    /**
     * Funcion recursiva que devulve la representacion de array de un servicio listo para ser entregado a frontend
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param array $services
     * @paream array $currentData
     * @param boolean $parentStatus
     * @return array
     */
    private function getServiceStructure($services,$currentData,&$parentStatus)
    {
        $res = [];
        $children = false;
        foreach ($services as $service) {
            $status = false;
            $child = false;
            $activeService = $this->getActiveService($service->getId(),$currentData);
            if (!empty($activeService)) {
                $status = true;
                $child = true;
                $children = true;
                $schedule = $this->getServiceSchedule($activeService->getSchedule());
                $data = $this->getDataInstanceService($activeService);
                $subservices = [];
            } else {
                $schedule = [
                    'full_time' => false,
                    'opening' => '',
                    'closing' => '',
                    'days' => [],
                ];
                $data = [];
                $subservices = $this->getServiceStructure($service->getChildren(),$currentData,$child);
                $children = $children || $child;
            }
            $serviceType = $service->getType();
            $current_service = [
                'id' => !empty($activeService) ? $activeService->getId() : null,
                'idType' => $service->getId(),
                'name' => $service->getTitle(),
                'type' => $service->getType(),
                'required' => $service->getRequired(),
                'free' => !empty($activeService) ? $activeService->getFree() : false,
                'quantity' => !empty($activeService) ? $activeService->getFree() : false,
                'data' => $data,
                'schedule' => $schedule,
                'subservices' => $subservices,
                'status' => ($serviceType==1 || $serviceType==9) ? $child : $status,
            ];
            $res[] = $current_service;
        }
        $parentStatus = $children;
        return $res;
    }

    /**
     * devuelve un array formateado para ser entregado a frontend de un objeto Schedule
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param mixed $current
     * @return array
     */
    private function getServiceSchedule($current)
    {
        if(empty($current)) {
            $schedule = [
                'full_time' => false,
                'opening' => '',
                'closing' => '',
                'days' => [],
            ];
        } else {
            if (!($current instanceof Schedule)) {
                $current = new Schedule(
                    new \DateTime($current['opening']),
                    new \DateTime($current['closing']),
                    $current['days'],
                    $current['full_time']
                );
            }
            $schedule = $current->toArray();
            $schedule['days'] = $current->getDays();
            $opening = $current->getOpening();
            $closing = $current->getClosing();
            $schedule['opening'] = !empty($opening) ? date_format($opening, 'H:i:s') : null;
            $schedule['closing'] = !empty($closing) ? date_format($closing, 'H:i:s') : null;
        }
        return $schedule;
    }

    /**
     *  devuelve un servicio activo en un conjunto de servicios
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param integer $id
     * @param array $data
     * @return PropertyService
     */
    private function getActiveService($id,$data)
    {
        $res = null;
        foreach ($data as $service) {
            if ($service->getType()->getId() == $id) {
                $res = $service;
                break;
            }
        }
        return $res;
    }

    /**
     *  devuelve las instancias de datos de un servicio estructurado (Bar, Restaurant, Salon) en un array formatado para ser entregado a frontend
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param PropertyService $service
     * @return Array
     */
    private function getDataInstanceService(PropertyService $service)
    {
        $response = [];
        if($service->isRestaurant())
            foreach ($service->getRestaurants() as $restaurant)
                $response[] = $this->getDataRestaurant($restaurant);
        elseif ($service->isBar())
            foreach ($service->getBars() as $bar)
                $response[] = $this->getDataBar($bar);
        elseif ($service->isSalon())
            foreach ($service->getSalons() as $salon)
                $response[] = $this->getDataSalon($salon);
        return $response;
    }

    /**
     *  esta funcion da formato a una entidad restaurant para ser entregado a frontend como un array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param Restaurant $restaurant
     * @return Array
     */
    private function getDataRestaurant(Restaurant $restaurant)
    {
        $breakfast = $restaurant->getBreakfastTime();
        $breakfastArray = $this->getServiceSchedule($breakfast);
        $lunch = $restaurant->getLunchTime();
        $lunchArray = $this->getServiceSchedule($lunch);
        $dinner = $restaurant->getDinnerTime();
        $dinnerArray = $this->getServiceSchedule($dinner);
        $schedule = $this->getServiceSchedule($restaurant->getSchedule());
        return [
            'id' => $restaurant->getId(),
            'nombre' => $restaurant->getName(),
            'tipo_cocina' => $restaurant->getType()->getId(),
            'buffet_carta' => $restaurant->getBuffetCarta(),
            'menu_dietetico' => $restaurant->getDietaryMenu(),
            'dias_apertura' => $schedule['days'],// instanceof Schedule ? $schedule->getDays() : null,
            'status' => $restaurant->getStatus(),
            'descripcion' => preg_replace('/\n/','\\n',$restaurant->getDescription()),
            'breakfast' => [
                'breakfast' => !empty($breakfast),
                'apertura' => $breakfastArray['opening'],
                'cierre' => $breakfastArray['closing'],
            ],
            'lunch' => [
                'lunch' => !empty($lunch),
                'apertura' => $lunchArray['opening'],
                'cierre' => $lunchArray['closing'],
            ],
            'dinner' => [
                'dinner' => !empty($dinner),
                'apertura' => $dinnerArray['opening'],
                'cierre' => $dinnerArray['closing'],
            ]
        ];
    }

    /**
     *  esta funcion da formato a una entidad bar para ser entregado a frontend como un array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param Bar $bar
     * @return Array
     */
    private function getDataBar(Bar $bar)
    {
        $schedule = $this->getServiceSchedule($bar->getSchedule());
        $typeFood = $bar->getFoodType();
        return [
            'id' => $bar->getId(),
            'nombre' => $bar->getName(),
            'tipo'=> $bar->getType(),
            'edad_min'=> $bar->getMinAge(),
            'comida' => $bar->getFood(),
            'tipo_comida' => !empty($typeFood) ? $bar->getFoodType()->getId() : null,
            'descripcion' => $bar->getDescription(),//preg_replace('/\n/','\\n',$bar->getDescription()),
            'status' => $bar->getStatus(),
            'horario' => [
                'apertura' => $schedule['opening'],
                'cierre' => $schedule['closing'],
            ]
        ];

    }

    /**
     *  esta funcion da formato a una entidad salon para ser entregado a frontend como un array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16/12/2015
     *
     * @param Salon $salon
     * @return Array
     */
    private function getDataSalon(Salon $salon)
    {
        return [
            'id' => $salon->getId(),
            'nombre' => $salon->getName(),
            'tipo' => $salon->getType(),
            'tipo_nombre' => $salon->getTypeString(),
            'luz_natural' => $salon->getNaturalLight(),
            'tamano' => $salon->getSize(),
            'capacidad' =>$salon->getCapacity(),
            'status' =>$salon->getStatus()
        ];
    }

    /**
     *  función que retorna los datos necesarios para editar la informacion de pago en el registro del establecimiento
     *
     * @author Carlos Aguilera <ceaf.21@gmail.com>
     * @version 10/11/2015
     *
     * @param String $slug
     * @throws \NotFoundHttpException
     * @return Array
     */
    public function getPaymentData($slug)
    {
        $response = array();
        $rProperty = $this->rf->get('Property');
        $rLocation = $this->rf->get('Location');
        $rCurrencyType = $this->rf->get('CurrencyType');

        $property = $rProperty->findOneBy(array('slug'=>$slug));
        if ($property) {
            $information = array();//Array para recoger toda la información
            $information['same_data_property'] = $property->getPaymentInfo()->getSameDataProperty();
            if($information['same_data_property']==true){
                $information['name'] = $property->getName();
                $location = $rLocation->find($property->getLocation()); 
                   
            }else{
                $information['name'] = $property->getPaymentInfo()->getName();
                $location = $property->getPaymentInfo()->getLocation();
            }
            
            $information['address'] = self::getArrayLocation($location); 
            $information['address']['address'] = $property->getPaymentInfo()->getAddress();
            $information['fiscal_code'] = $property->getPaymentInfo()->getTaxId();
            $account_number = $property->getPaymentInfo()->getAccount();
            $information['account_number_part1'] = $account_number['entity'];
            $information['account_number_part2'] = $account_number['office'];
            $information['account_number_part3'] = $account_number['control'];
            $information['account_number_part4'] = $account_number['account'];

            // Si no existe la moneda por X motivo
            if ( is_null($property->getPaymentInfo()->getCurrency()) ) {
                $currency = $rCurrencyType->findOneByArray(
                    array('id' => 148) // "Por defecto le dejamos bolivar"
                );

                $information['currency_id'] = $currency->getId();
                $information['currency_payment_name'] = $currency->getTitle();
            }
            else {
                $information['currency_payment_name'] = $property->getPaymentInfo()->getCurrency()->getTitle();
                $information['currency_id'] = $property->getPaymentInfo()->getCurrency()->getId();
            }

            $information['charging_system'] = $location = $property->getPaymentInfo()->getChargingSystem();
            $information['swift'] = $property->getPaymentInfo()->getSwift();
            //Creando respuesta
            $response['slug'] = $slug;
            $response['payment'] = $information;
            $response['countries'] = $rLocation->getAll();
            $response['currency'] = $this
                ->rf
                ->get('CurrencyType')
                ->getAllCurrency();
            $response['propertyInfo'] = self::getArrayLocation($rLocation->find($property->getLocation()));
            $response['propertyInfo']['address'] = $property->getAddress();
            $response['propertyInfo']['name'] = $property->getName();
            $response['propertyInfo']['creditDays'] = $property->getAgreement()->getCreditDays();
            $response['propertyInfo']['discountRate'] = $property->getDiscountRate();
        } else {
            throw new NotFoundHttpException();
        }
        //var_dump(json_encode($response));
        return $response;
    }
}