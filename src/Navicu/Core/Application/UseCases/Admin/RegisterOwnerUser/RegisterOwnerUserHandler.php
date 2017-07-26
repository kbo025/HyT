<?php
namespace Navicu\Core\Application\UseCases\Admin\RegisterOwnerUser;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\ContactPerson;
use Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\RateByKid;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Model\Entity\RoomFeature;
use Navicu\Core\Domain\Model\Entity\RateByPeople;
use Navicu\Core\Domain\Model\Entity\Livingroom;
use Navicu\Core\Domain\Model\Entity\Bedroom;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\Entity\Agreement;
use Navicu\Core\Domain\Model\Entity\PaymentInfoProperty;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\BankAccount;


class RegisterOwnerUserHandler implements Handler
{
    /**
     *   instancia del repositoryFactory
     */
    protected $rf;

    /**
     * @var Almacena la data necesaria para generar el pdf personalizado de terminos y condiciones
     */
    protected $response;

    /**
     * @var La variable se almacena la imagen de perfil del establecimiento
     */
    private $profileImage;

    /**
     * @var Representa el Slug del establecimiento al que se dará de alta
     */
    private $propertySlug;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $this->response = array();
        //obtengo los repositorios de TempOwner, Category y Location del repositoryFactory
        $tempowner_repository = $rf->get('TempOwner');
        $rolerep = $rf->get('Role');

        $this->propertySlug = $command->get('slug');

        //Busco el usuario
        $tempowner = $tempowner_repository->findOneByArray(
            array('slug' => $command->get('slug'))
        );

        //si existe procedo a ejecutar los metodos para dar de alta
        if (!empty($tempowner)) {
            // si el usuario esta completo procedo a ejecutar los metodos para dar de alta
            if ($tempowner->evaluateProgress() >= 100) {
                $owner = new OwnerProfile();
                $property = $this->createProperty($tempowner);

                $this->addServiceProperty($tempowner, $property);
                $this->addRoomsProperty($tempowner, $property);
                $payment = $this->createPaymentInfo($tempowner);
                $payment->setProperty($property);
                $property->setPaymentInfo($payment);
                $property->setJoinDate((new \DateTime()));
                $property->setRegistrationDate($tempowner->getExpiredate()->modify('-30 days'));
                $owner->addProperty($property);
                $agreement = $this->createAgreement($tempowner);
                $agreement->setProperty($property);
                $property->setAgreement($agreement);
                $owner->setUser($tempowner->getUserId());
                $owner->getUser()->setOwnerProfile($owner);

                $property->setPublicId($this->rf->get('Property')->getLastId()[1] + 1);
                $role = $rolerep->findByName('ROLE_EXTRANET_ADMIN');
                $owner->getUser()->addRole($role);
                $owner->getUser()->removeRole('ROLE_TEMPOWNER');
                $owner->getUser()->setTempOwner(null);
                $tempowner->setUserId(null);
                if ($tempowner->getNvcProfile()) {
                    $property->setNvcProfile($tempowner->getNvcProfile());
                    $property->setRecruit($tempowner->getRecruit());
                }
                if ($tempowner->getRecruit())
                    $property->setRecruit($tempowner->getRecruit());

                $this->addGalleries($tempowner, $property);
                $this->rf->get('OwnerProfile')->save($owner);
                $this->rf->get('TempOwner')->delete($tempowner);
                $this->replaceSlugImages($property->getSlug());


                $command->setPublicId($property->getPublicId());
                $command->setEmail($owner->getUser()->getEmail());

                $response = new ResponseCommandBus(201, 'Ok', array('profile_image' => $this->profileImage, 'data_terms' => $this->response));
            } else {
                $response = new ResponseCommandBus(400, 'Bad request', array('error' => 'Datos de usuario incompletos'));
            }
        } else {
            $response = new ResponseCommandBus(401, 'Unauthorized');
        }
        return $response;
    }

    /**
     * La siguiente función se encarga de modificar todos los path de las carpetas de imagenes
     * por el slug del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Freddy Contreras <freddycontreras3@gmail.com>
     * @param $newSlug
     * @version 10/09/2015
     *
     */
    public function replaceSlugImages($newSlug)
    {
        $webPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
        $documentPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/documents/';

        rename($webPath.'images_original/property/'.$this->propertySlug,
            $webPath.'images_original/property/'.$newSlug );

        rename($webPath.'images_md/property/'.$this->propertySlug,
            $webPath.'images_md/property/'.$newSlug );

        rename($webPath.'images_sm/property/'.$this->propertySlug,
            $webPath.'images_sm/property/'.$newSlug );

        rename($webPath.'images_xs/property/'.$this->propertySlug,
            $webPath.'images_xs/property/'.$newSlug);

        rename($documentPath.$this->propertySlug,
            $documentPath.$newSlug);
    }

    /**
     * genera un Property en base al registro hecho en tempowner
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param TempOwner $tempowner
     * @return Property
     */
    private function createProperty($tempowner)
    {
        $contactrep = $this->rf->get('ContactPerson');
        $accommodation_repository = $this->rf->get('Accommodation');
        $currency_repository = $this->rf->get('CurrencyType');
        $languages_repository = $this->rf->get('Language');
        $location_repository = $this->rf->get('Location');
        $polCancellationRepository = $this->rf->get('CancellationPolicy');

        $request = $tempowner->getPropertyForm();
        $termsData = $tempowner->getTermsAndConditionsInfo();
        $property = new Property();
        $property->setName($request['name']);
        $property->setAddress($request['address']);
        $property->setStar($request['star']);
        $property->setNumberFloor($request['number_floor']);
        $property->setHotelChainName($request['hotel_chain_name']);
        $property->setAmountRoom($request['amount_room']);
        $property->setCheckInAge($request['check_in_age']);
        $property->setOpeningYear($request['opening_year']);
        $property->setDiscountRate(!empty($termsData['discount_rate']) ? $termsData['discount_rate'] : 0.3 );
        $property->setAllIncluded(!empty($request['all_included']));
        $property->setDebit(!empty($request['debit']));
        $property->setRating(0);
        $property->setTaxRate(
            isset($request['tax_rate']) ?
                ($request['tax_rate']>1 ?
                    $request['tax_rate']/100 :
                    $request['tax_rate']
                ) :
                0.0
        );
        $property->setTax(!empty($request['tax']));
        $property->setRenewalYear(isset($request['renewal_year']) ? $request['renewal_year'] : null);

        $property->setPublicAreasRenewalYear(
            isset($request['public_areas_renewal_year']) ?
                $request['public_areas_renewal_year'] : null
        );

        $property->setDesignViewProperty(!empty($request['design_view_property']));
        $property->setAdditionalInfo($request['additional_info']);
        $property->setComercialRooms(isset($request['comercial_rooms']) ? $request['comercial_rooms'] : null);
        $property->setDescription($request['description']);


        //política de cancelación del establecimiento (No rembolsable)

        $polNoRefundable = $polCancellationRepository->findByTitle('No Refundable');

        if (!empty($polNoRefundable)) {
            $property_cancellation = new PropertyCancellationPolicy();
            $property_cancellation->setCancellationPolicy($polNoRefundable);
            $property_cancellation->setProperty($property);
            $property->addPropertyCancellationPolicy($property_cancellation);
            $property->setBasePolicy($property_cancellation);
        }

        //informacion de camas extra
        $property->setBeds(!empty($request['beds']));
        if ($property->getBeds()) {
            $property->setBedsAdditionalCost($request['beds']['beds_additional_cost']);
            $property->setBedsPriorNotice($request['beds']['beds_prior_notice']);
        }

        $property->setRateType($request['rate_type']);
        $property->setBasicQuota($request['basic_quota']);
        //politicas sobre niños
        $property->setChild(!empty($request['child']));

        if($property->getChild()) {
            $property->setAgePolicy(
                $request['agePolicy']['adult'],
                $request['agePolicy']['child'],
                $request['agePolicy']['baby']
            );
        }

        //informacion de servicio de cunas en la habitacion
        $property->setCribs(!empty($request['cribs']));
        if ($property->getCribs()) {
            $property->setCribsAdditionalCost($request['cribs']['cribs_additional_cost']);
            $property->setCribsMax($request['cribs']['cribs_max']);
            $property->setCribsPriorNotice($request['cribs']['cribs_prior_notice']);
        }

        //politicas sobre mascotas
        $property->setPets(!empty($request['pets']['pets']));
        if ($property->getPets()) {
            $property->setPetsAdditionalCost($request['pets']['pets_additional_cost']);
        }

        //politicas sobre pago con tdc
        $property->setCreditCard(!empty($request['credit_card']));
        if ($property->getCreditCard()) {
            $property->setCreditCardAmex(!empty($request['credit_card_america']));
            $property->setCreditCardMc(!empty($request['credit_card_mc']));
            $property->setCreditCardVisa(!empty($request['credit_card_visa']));
        }

        //cantidad maxima sobre la cual se calcula impuestos de la ciudad
        $property->setCityTaxMaxNights(
            isset($request['city_tax_max_nights']) ?
                $request['city_tax_max_nights'] : 0.0
        );

        //politicas de pago en efectivo
        $property->setCash($request['cash']['cash']);
        $property->setMaxCash(
            isset($request['cash']['max_cash']) ?
                $request['cash']['max_cash'] :
                0.0
        );

        //tipo de porcentaje cobrado (por )
        $property->setCityTax(
            isset($request['city_tax']) ?
                ($request['city_tax']>1 ?
                    $request['city_tax']/100 :
                    $request['city_tax']) :
                0.0
        );
        $property->setCityTaxType(
            isset($request['city_tax_type']) ?
                $request['city_tax_type'] :
                0.0
        );

        foreach ($request['contacts'] as $contact) {
            $newcontact = new ContactPerson();
            $newcontact->setName($contact['name']);
            $newcontact->setType($contact['type']);
            $newcontact->setCharge($contact['charge']);
            $newcontact->setPhone(new Phone($contact['phone']));
            $newcontact->setEmail(new EmailAddress($contact['email']));
            $newcontact->setEmailReservationReceiver($contact['email_reservation_receiver']);
            $newcontact->setFax(
                isset($contact['fax']) ?
                    new Phone($contact['fax']) :
                    null
            );
            $newcontact->setProperty($property);
            $property->addContact($newcontact);
        }

        $property->setUrlWeb(
            isset($request['url_web']) ?
                new Url($request['url_web']) :
                null
        );

        $property->setPhones(new Phone($request['phones']));
        $property->setFax(
            isset($request['fax']) ?
                new Phone($request['fax']) :
                null
        );
        $property->setCoordinates(
            new Coordinate(
                $request['coordinates']['longitude'],
                $request['coordinates']['latitude']
            )
        );
        $accommodation = $accommodation_repository->getById($request['accommodation']);
        $property->setAccommodation($accommodation);
        if (!empty($request['currency'])) {
            $currency = $currency_repository->find($request['currency']);
            $property->setCurrency($currency);
        }

        if (!empty($request['city_tax_currency'])) {
            $currency = $currency_repository->find($request['city_tax_currency']);
            $property->setCityTaxCurrency($currency);
        }

        foreach ($request['language'] as $language) {
            $newlang = $languages_repository->find($language);
            $property->addLanguage($newlang);
        }

        $location = $location_repository->find($request['location']);
        $property->setLocation($location);

        $newdate = new \DateTime(
            isset($request['check_in']['date']) ?
                $request['check_in']['date'] :
                $request['check_in']
        );
        $property->setCheckIn($newdate);

        $newdate = new \DateTime(
            isset($request['check_out']['date']) ?
                $request['check_out']['date'] :
                $request['check_out']
        );
        $property->setCheckOut($newdate);
        $property->setActive(true);

        $property->generateSlug();

        return $property;
    }

    /**
     * agrega a property los servicios seleccionados en el formulario de registro
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param TempOwner $tempowner
     * @param Property $property
     */
    private function addServiceProperty($tempowner, $property)
    {

        $array_services = $this
            ->rf
            ->get('ServiceType')
            ->findAllWithKeys();
        $services = $tempowner->getServicesForm();
        foreach ($services as $service) {
            $propserv = new PropertyService();
            $propserv->setType($array_services[$service['type']]);
            $propserv->setFree($service['free']);
            $propserv->setQuantity($service['quantity']);
            if (isset($service['schedule'])) {
                $propserv->setSchedule(
                    new Schedule(
                        isset($service['schedule']['opening']) ? new \DateTime($service['schedule']['opening']) : null,
                        isset($service['schedule']['closing']) ? new \DateTime($service['schedule']['closing']) : null,
                        isset($service['schedule']['days']) ? $service['schedule']['days'] : null,
                        isset($service['schedule']['full_time']) ? $service['schedule']['full_time'] : null
                    )
                );
            }
            if ($propserv->isSalon()) {
                foreach ($service['data'] as $datasalon) {
                    $salon = new Salon();
                    $salon->setName($datasalon['name']);
                    $salon->setSize($datasalon['size']);
                    $salon->setStatus($datasalon['status']);
                    $salon->setType($datasalon['type']);
                    $salon->setDescription(isset($datasalon['description']) ? $datasalon['description'] : null);
                    $salon->setQuantity(
                        isset($datasalon['quantity']) ?
                            $datasalon['quantity'] :
                            null
                    );
                    $salon->setCapacity($datasalon['capacity']);
                    $salon->setNaturalLight(!empty($datasalon['natural_light']));
                    $salon->setService($propserv);
                    $propserv->addSalon($salon);
                }
            }
            if ($propserv->isBar()) {
                $foods = $this->rf->get('FoodType')->findAllWithKeys();
                foreach ($service['data'] as $databar) {
                    $bar = new Bar();
                    $bar->setName($databar['name']);
                    $bar->setMinAge($databar['min_age']);
                    $bar->setDescription($databar['description']);
                    $bar->setStatus($databar['status']);
                    $bar->setType($databar['type']);
                    $bar->setType($databar['type']);
                    $bar->setFood(!empty($databar['food']));
                    $bar->setFoodType($foods[$databar['type']]);
                    $bar->setSchedule(
                        new Schedule(
                            isset($databar['schedule']['opening']) ? new \DateTime($databar['schedule']['opening']) : null,
                            isset($databar['schedule']['closing']) ? new \DateTime($databar['schedule']['closing']) : null,
                            isset($databar['schedule']['days']) ? $databar['schedule']['days'] : null,
                            isset($databar['schedule']['full_time']) ? $databar['schedule']['full_time'] : null
                        )
                    );
                    $bar->setService($propserv);
                    $propserv->addBar($bar);
                }
            }
            if ($propserv->isRestaurant()) {
                $foods = $this->rf->get('FoodType')->findAllWithKeys();
                foreach ($service['data'] as $datarest) {
                    $restaurant = new Restaurant();
                    $restaurant->setName($datarest['name']);
                    $restaurant->setStatus($datarest['status']);
                    $restaurant->setBuffetCarta($datarest['buffet_carta']);
                    $restaurant->setDietaryMenu(!empty($datarest['MenuDietetico']));
                    $restaurant->setType($foods[$datarest['type']]);
                    $restaurant->setSchedule(
                        new Schedule(
                            isset($datarest['schedule']['opening']) ? new \DateTime($datarest['schedule']['opening']) : null,
                            isset($datarest['schedule']['closing']) ? new \DateTime($datarest['schedule']['closing']) : null,
                            isset($datarest['schedule']['days']) ? $datarest['schedule']['days'] : null,
                            isset($datarest['schedule']['full_time']) ? $datarest['schedule']['full_time'] : null
                        )
                    );
                    if (isset($datarest['breakfast_time'])) {
                        $restaurant->setBreakfastTime(
                            new Schedule(
                                isset($datarest['breakfast_time']['opening']) ? new \DateTime($datarest['breakfast_time']['opening']) : null,
                                isset($datarest['breakfast_time']['closing']) ? new \DateTime($datarest['breakfast_time']['closing']) : null,
                                isset($datarest['breakfast_time']['days']) ? $datarest['breakfast_time']['days'] : null,
                                isset($datarest['breakfast_time']['full_time']) ? $datarest['breakfast_time']['full_time'] : null
                            )
                        );
                    }
                    if (isset($datarest['lunch_time'])) {
                        $restaurant->setLunchTime(
                            new Schedule(
                                isset($datarest['lunch_time']['opening']) ? new \DateTime($datarest['lunch_time']['opening']) : null,
                                isset($datarest['lunch_time']['closing']) ? new \DateTime($datarest['lunch_time']['closing']) : null,
                                isset($datarest['lunch_time']['days']) ? $datarest['lunch_time']['days'] : null,
                                isset($datarest['lunch_time']['full_time']) ? $datarest['lunch_time']['full_time'] : null
                            )
                        );
                    }
                    if (isset($datarest['dinner_time'])) {
                        $restaurant->setDinnerTime(
                            new Schedule(
                                isset($datarest['dinner_time']['opening']) ? new \DateTime($datarest['dinner_time']['opening']) : null,
                                isset($datarest['dinner_time']['closing']) ? new \DateTime($datarest['dinner_time']['closing']) : null,
                                isset($datarest['dinner_time']['days']) ? $datarest['dinner_time']['days'] : null,
                                isset($datarest['dinner_time']['full_time']) ? $datarest['dinner_time']['full_time'] : null
                            )
                        );
                    }
                    $restaurant->setService($propserv);
                    $propserv->addRestaurant($restaurant);
                }
            }
            $propserv->setProperty($property);
            $property->addService($propserv);
        }
    }

    /**
     * agrega a property las habitaciones agregadas en el formulario de registro
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param TempOwner $tempowner
     * @param Property $property
     */
    private function addRoomsProperty($tempowner, $property)
    {
        $rooms = $tempowner->getRoomsForm();
        $room_types = $this
            ->rf
            ->get('RoomType')
            ->findAllWithKeys();
        $feature_room_types = $this
            ->rf
            ->get('RoomFeatureType')
            ->findAllWithKeys();
        $allInclude = !empty($tempowner->getPropertyForm()['all_include']);
        $categoryRepository = $this
            ->rf
            ->get('Category');

        $galleryRooms = $tempowner->getGalleryForm();
        //El siguiente array contiene el conjunto de pack por defecto de los establecimientos
        $packTypes = [];
        if ($allInclude) {
            $packTypes[] = $categoryRepository->findOneByArray(['title' => 'All Included']);
        } else {
            $packTypes[] = $categoryRepository->findOneByArray(['title' => 'Room Only']);
            $packTypes[] = $categoryRepository->findOneByArray(['title' => 'BreakFast Included']);
        }
        $roomIndex = 0;
        foreach ($rooms as $current_room) {
            $room = new Room();
            $room
                ->setBaseAvailability(!empty($current_room['base_availability']) ?
                    $current_room['base_availability'] :
                    0
                )
                ->setType($room_types[$current_room['type']])
                ->setName($current_room['name'])
                ->setAmountRooms($current_room['amount_rooms'])
                ->setSize( isset($current_room['size']) ? $current_room['size'] : null)
                ->setSmokingPolicy(!empty($current_room['smoking_policy']))
                ->setMinPeople(is_null($current_room['min_people']) ? 1 : $current_room['min_people'])
                ->setMaxPeople($current_room['max_people']);

            foreach ($current_room['features'] as $current_feature) {
                $feature = new RoomFeature();
                $amount = isset($current_feature['amount']) ?
                        $current_feature['amount'] :
                        0;
                $feature
                    ->setFeature($feature_room_types[$current_feature['feature']])
                    ->setAmount($amount)
                    ->setRoom($room);
                $room->addFeature($feature);
            }

            //incremento por Adultos
            $room
                ->setIncrement($current_room['increment'])
                ->setKidPayAsAdult($current_room['kid_pay_as_adult'])
                ->setSameIncrementAdult($current_room['same_increment_adult'])
                ->setVariationTypePeople((int)$current_room['variation_type_people']);
            if ($room->getIncrement()) {
                for ($i=0; $i<$room->getMaxPeople(); $i++) {
                    if (isset($current_room['rates_by_people'][$i])) {
                        $rate = new RateByPeople();
                        $amount = isset($current_room['rates_by_people'][$i]['amount_rate']) ?
                            $current_room['rates_by_people'][$i]['amount_rate'] :
                            0;
                        $amount = $room->getVariationTypePeople()==1 ? $amount/100 : $amount;
                        $rate
                            ->setNumberPeople($current_room['rates_by_people'][$i]['number_people'])
                            ->setAmountRate($amount)
                            ->setRoom($room);
                        $room->addRatesByPeople($rate);
                    } else
                        throw new \Exception('rate_by_adults_incorrect',400);
                }
            }

            //aumento por niños
            $room
                ->setIncrementKid($current_room['increment_kid'])
                ->setSameIncrementKid($current_room['same_increment_kid'])
                ->setVariationTypeKids($current_room['variation_type_kids']);
            if ($property->getChild() && $room->getIncrementKid() && !$room->getKidPayAsAdult()) {
                for ($i=0; $i<count($current_room['rates_by_kids']); $i++) {
                    if (isset($current_room['rates_by_kids'][$i])) {
                        $rate = new RateByKid();
                        $amount = isset($current_room['rates_by_kids'][$i]['amount_rate']) ?
                            $current_room['rates_by_kids'][$i]['amount_rate'] :
                            0;
                        $amount = $room->getVariationTypeKids()[$current_room['rates_by_kids'][$i]['index']] == 1 ? $amount/100 : $amount;
                        $rate
                            ->setAmountRate($amount)
                            ->setNumberKid($current_room['rates_by_kids'][$i]['number_kid'])
                            ->setIndex($current_room['rates_by_kids'][$i]['index'])
                            ->setRoom($room);
                        $room->addRatesByKid($rate);
                    } else
                        throw new \Exception('rate_by_kids_incorrect',400);
                }
            }
            //dormitorios de la habitacion
            if (!empty($current_room['livingrooms'])) {
                foreach ($current_room['livingrooms'] as $current_livingrooms) {
                    $livingroom = new Livingroom();
                    $livingroom->setAmountCouch($current_livingrooms['amount_couch'])
                        ->setAmountPeople($current_livingrooms['amount_people'])
                        ->setRoom($room);
                    $room->addLivingroom($livingroom);
                }
            }
            foreach ($current_room['bedrooms'] as $current_bedroom) {
                $bedroom = new Bedroom();
                foreach ($current_bedroom['beds'] as $bed) {
                    $bed = new Bed($bed['type'], $bed['amount']);
                    $bed->setBedroom($bedroom);
                    $bedroom->addBed($bed);
                }
                $bedroom
                    ->setAmountPeople($current_bedroom['amount_people'])
                    ->setBath($current_bedroom['bath'])
                    ->setRoom($room);
                $room->addBedroom($bedroom);
            }
            $room->setProperty($property);

            //Se crea los pack de una habitación
            foreach ($packTypes as $currentPackType) {
                $pack = new Pack();
                $pack->setType($currentPackType);

                foreach ($property->getPropertyCancellationPolicies() as &$currentCancelPoly) {
                    $currentCancelPoly->addPackage($pack);
                    $pack->addPackCancellationPolicy($currentCancelPoly);
                }

                $pack->setRoom($room);
                $room->addPackage($pack);
            }

            $this->addGalleriesRoom($room, $galleryRooms, $property);

            $property->addRoom($room);
            $roomIndex++;
        }
    }

    /**
     * crea una instancia de la entidad PaymentInfoProperty
     * con la informacion referente a los datos de pago y facturacion del
     * establecimiento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param TempOwner $tempowner
     * @return PaymentInfoProperty
     */
    private function createPaymentInfo($tempowner)
    {
        $currency_repository = $this->rf->get('CurrencyType');
        $location_repository = $this->rf->get('Location');
        $payment_data = $tempowner->getPaymentInfoForm();
        $payment = new PaymentInfoProperty();
        $payment->setSameDataProperty(!empty($payment_data['same_data_property']));
        $payment->setTaxId($payment_data['tax_id']);
        $payment->setChargingSystem($payment_data['charging_system']);
        $payment->setName(
            isset($payment_data['name']) ?
                $payment_data['name'] :
                null
        );
        $payment->setAddress(
            isset($payment_data['address']) ?
                $payment_data['address'] :
                null
        );
        $payment->setSwift(
            isset($payment_data['swift']) ?
                $payment_data['swift'] :
                null
        );
        if (!empty($payment_data['currency'])) {
            $currency = $currency_repository->find($payment_data['currency']);
            $payment->setCurrency($currency);
        }
        else { //Si no se selecciono ninguna moneda con la cual pagarle a navicu se asiga bolivares por defecto
            $currency = $currency_repository->findOneByArray(
                array('id' => 148));
            $payment->setCurrency($currency);
        }
        if (!empty($payment_data['location'])) {
            $location = $location_repository->find($payment_data['location']);
            $payment->setLocation($location);
        }
        $account = new BankAccount(
            $payment_data['account']['entity'],
            $payment_data['account']['office'],
            $payment_data['account']['control'],
            $payment_data['account']['account']
        );
        $payment->setAccount($account);

        if (!empty($payment_data['rif'])) {
            $document = new Document();
            $document->setName($payment_data['rifName']);
            $document->setFileName($payment_data['rif']);

            $payment->setRif($document);
        }
        return $payment;
    }

    /**
     * crea un objeto Agreement con la informacion del registro de un usuario temporal
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 07-08-15
     * @param TempOwner $tempowner
     * @return Agreement
     */
    private function createAgreement($tempowner)
    {
        $data = $tempowner->getTermsAndConditionsInfo();
        $dataProperty = $tempowner->getPropertyForm();
        $this->response['discount_rate'] = $dataProperty['discount_rate'];
        $this->response['hotel_slug'] = $dataProperty['slug'];
        $this->response['user_slug'] = $tempowner->getSlug();
        $this->response['address'] = $dataProperty['address'];
        $this->response['fax'] = isset($dataProperty['address']) ? $dataProperty['address'] : '';
        $agreement = new Agreement();
        $document = new Document();
        $document->setName($dataProperty['slug'].'.pdf');
        $document->setFileName($_SERVER['DOCUMENT_ROOT'].'/uploads/Agreement/'.$tempowner->getSlug().'/'.$dataProperty['slug'].'.pdf');
        $agreement->setPdf($document);
        $agreement->setClientIpAddress($data['client_ip']);
        $agreement->setCreditDays($data['credit_days']);
        $date = new \DateTime($data['date']['date']);
        $agreement->setDate($date);
        return $agreement;
    }

    /**
     * Persiste las galerias del establecimiento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 13/08/2015
     * @param TempOwner $tempowner
     * @param Property $property
     * @return Agreement
     */
    private function addGalleries($tempowner, $property)
    {
        $data = $tempowner->getGalleryForm();

        $rpServiceType = $this->rf->get('ServiceType');

        foreach ($data['otherGalleries'] as $current_gallery) {

            $gallery = new PropertyGallery();
            $serviceType = $rpServiceType->findOneBy(array('id' => $current_gallery['idSubGallery']));
            $gallery->setType($serviceType);

            foreach ($current_gallery['images'] as $current_image) {
                $image = new PropertyImagesGallery();
                $document = new Document();
                $document->setName($current_image['name']);
                $auxPath = $current_image['path'];
                $auxPath = preg_replace('/'.$this->propertySlug.'/', $property->getSlug(),$auxPath, 1);
                $document->setFileName($auxPath);
                $image->setImage($document);

                $image->setPropertyGallery($gallery);

                foreach ($data['favorites'] as $key => $currentFavorite) {
                    if ($currentFavorite["subGallery"] == $current_gallery["subGallery"] and
                        $currentFavorite["path"] == $current_image['path']) {
                        $favorite = new PropertyFavoriteImages();
                        $favorite->setImage($document);
                        $favorite->setProperty($property);
                        $property->addPropertyFavoriteImage($favorite);

                        if (!$property->getProfileImage() and $key == 0) {
                            $property->setProfileImage($favorite);
                            $this->profileImage = $auxPath;
                        }

                        array_splice($data['favorites'], $key, 1);
                        break;
                    }
                }

                $gallery->addImagesGallery($image);
            }
            $gallery->setProperty($property);
            $property->addPropertyGallery($gallery);
        }

        $tempowner->setGalleryForm($data);
    }

    /**
     * Almacena las imagenes de las habitaciones del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Room $room
     * @param $dataGallery
     * @param $property
     * @version 13/08/2015
     */
    private function addGalleriesRoom(Room $room, &$dataGallery, $property)
    {
        $idSubGallery = $room->getType()->getId();

        foreach ($dataGallery['rooms'] as $key => $currentGallery) {
            if ($currentGallery['idSubGallery'] == $idSubGallery) {
                foreach ($currentGallery['images'] as $currentImage) {
                    $document = new Document();
                    $document->setName($currentImage['name']);
                    $auxPath = $currentImage['path'];
                    $auxPath = preg_replace('/'.$this->propertySlug.'/', $property->getSlug(),$auxPath, 1);
                    $document->setFileName($auxPath);

                    $roomGallery = new RoomImagesGallery();
                    $roomGallery->setImage($document);
                    $roomGallery->setRoom($room);
                    $room->addImagesGallery($roomGallery);

                    foreach ($dataGallery['favorites'] as $key2 => $currentFavorite) {
                        if ($currentFavorite["subGallery"] == $currentGallery["subGallery"] and
                            $currentFavorite["path"] == $currentImage['path']) {
                            $favorite = new PropertyFavoriteImages();
                            $favorite->setImage($document);
                            $favorite->setProperty($property);
                            $property->addPropertyFavoriteImage($favorite);

                            if (!$property->getProfileImage() and $key2 == 0) {
                                $property->setProfileImage($favorite);
                                $this->profileImage = $auxPath;
                            }

                            array_splice($dataGallery['favorites'], $key2, 1);
                            break;
                        }
                    }

                    if (!$room->getProfileImage())
                        $room->setProfileImage($roomGallery);
                }
                
                array_splice($dataGallery['rooms'], $key, 1);
                break;
            }
        }
    }
}