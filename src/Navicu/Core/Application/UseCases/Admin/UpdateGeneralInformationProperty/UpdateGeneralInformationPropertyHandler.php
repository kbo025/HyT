<?php

namespace Navicu\Core\Application\UseCases\Admin\UpdateGeneralInformationProperty;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\ContactPerson;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

class UpdateGeneralInformationPropertyHandler implements Handler
{

    /**
     *   instancia del repositoryFactory
     */
    protected $rf;

    /**
     *  el comando que se ejecutó
     */
    protected $command;

    /**
     * @var Almacena la data necesaria para generar el pdf personalizado de terminos y condiciones
     */
    protected $response;

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
        $this->command = $command;
        $rProperty = $this->rf->get('Property');
        $rAccommodation = $this->rf->get('Accommodation');
        $rLocation = $this->rf->get('Location');
        $rLanguage = $this->rf->get('Language');
        $rContact = $this->rf->get('ContactPerson');
        $rCurrencyType = $this->rf->get('CurrencyType');
        $property = $rProperty->findOneByArray(array('slug'=>$this->command->get('slug')));
        $data["propertyC"] = clone $property;

        //$property = new Property();
        if (!is_null($property)) {
            try {
                $oldSlug = $property->getSlug();
                //primera seccion
                $property->setName($this->command->get('name'));
                $accommodation = $rAccommodation->getById(
                    $this->command->get('accommodation_id')
                );
                $property->setAccommodation($accommodation);
                $property->setStar($this->command->get('star'));
                $property->setPhones(
                    new Phone($this->command->get('phone'))
                );
                $fax = $this->command->get('fax');
                if (!empty($fax))
                    $property->setFax(new Phone($fax));
                $location = $rLocation->findOneByArray(
                    array('id' => $this->command->get('location'))
                );
                $property->setLocation($location);
                $property->setAddress($this->command->get('address'));
                $property->setCoordinates(
                    new Coordinate(
                        $this->command->get('longitude'),
                        $this->command->get('latitude')
                    )
                );

                //segunda seccion
                $property->setOpeningYear($this->command->get('opening_year'));
                $property->setRenewalYear($this->command->get('renewal_year'));
                $property->setPublicAreasRenewalYear(
                    $this->command->get('public_areas_renewal_year')
                );
                $url = $this->command->get('url_web');
                if (!empty($url))
                    $property->setUrlWeb(new Url($url));
                else
                    $property->setUrlWeb();
                if ($this->command->get('hotel_chain'))
                    $property->setHotelChainName($this->command->get('hotel_chain_name'));
                else
                    $property->setHotelChainName(null);
                $property->setAmountRoom($this->command->get('amount_room'));
                $property->setNumberFloor($this->command->get('number_floor'));
                $property->setCheckIn(new \DateTime($this->command->get('check_in')));
                $property->setCheckOut(new \DateTime($this->command->get('check_out')));
                $newLan = $this->command->get('languages');
                $data["languagesC"] = clone $property->getLanguages();
                foreach ($property->getLanguages() as $lan) {
                    $index = array_search($lan->getId(), $newLan);
                    if ($index) {
                        unset($newLan[$index]);
                    } else {
                        $property->removeLanguage($lan);
                    }
                }
                foreach ($newLan as $lan) {
                    $language = $rLanguage->findOneByArray(array('id' => $lan));
                    $property->addLanguage($language);
                }

                //tercera seccion
                $data["contactC"] = clone $property->getContacts();
                $minEmailSended = 0;
                foreach ($this->command->get('contacts') as $currentContact) {

                    $contact = $rContact->findOneByArray(array('id' => $currentContact['id']));
                    $contact = isset($contact) ? $contact : new ContactPerson();
                    $contact->setCharge($currentContact['charge']);
                    $contact->setEmail(new EmailAddress($currentContact['email']));
                    $contact->setPhone(new Phone($currentContact['phone']));
                    $contact->setName($currentContact['name']);
                    $contact->setEmailReservationReceiver(false);
                    if(!empty($currentContact['email_reservation_receiver'])) {
                        $contact->setEmailReservationReceiver(true);
                        $minEmailSended++;
                    }
                    $contact->setType($rContact->getIdType($currentContact['id']));
                    $contact->setFax(
                        !empty($currentContact['fax']) ?
                            new Phone($currentContact['fax']) :
                            null
                    );
                    $contact->setProperty($property);
                    $property->addContact($contact);
                }

                if ($minEmailSended<1) {
                    throw new EntityValidationException(
                        'email_reservation_receiver',
                        'Property',
                        'Debe seleccionar a menos un contacto para recibir corres electronicos',
                        400
                    );
                }
                //Cuarta seccion
                $property->setAllIncluded($this->command->get('all_included'));
                $property->setCheckInAge($this->command->get('check_in_age'));

                $age_policy = $this->command->get('age_policy');
                $property->setChild($this->command->get('child'));
                if ($property->getChild()) {
                    $property->setAgePolicy(
                        $age_policy['adult'],
                        $age_policy['child'],
                        $age_policy['baby']
                    );
                }

                $beds_policy = $this->command->get('beds_policy');
                $property->setBeds($beds_policy['beds']);
                $property->setBedsPriorNotice(
                    $beds_policy['beds'] ?
                        $beds_policy['prior_notice'] :
                        null
                );
                $property->setBedsAdditionalCost(
                    $beds_policy['beds'] ?
                        $beds_policy['value_beds_extra'] :
                        null
                );

                $pets_policy = $this->command->get('pets_policy');
                $property->setPets($pets_policy['pets']);
                $property->setPetsAdditionalCost(
                    $pets_policy['pets'] ?
                        $pets_policy['value_pets'] :
                        null
                );

                $cribs_policy = $this->command->get('cribs_policy');
                $property->setCribs($cribs_policy['cribs']);
                $property->setCribsAdditionalCost(
                    $cribs_policy['cribs'] ?
                        $cribs_policy['value_cribs'] :
                        null
                );
                $property->setCribsMax(
                    $cribs_policy['cribs'] ?
                        $cribs_policy['max_cribs'] :
                        null
                );
                $property->setCribsPriorNotice(
                    $cribs_policy['cribs'] ?
                        $cribs_policy['prior_notice'] :
                        null
                );

                $comercial_rooms_policy = $this->command->get('cormecial_rooms_policy');
                $property->setComercialRooms(
                    $comercial_rooms_policy['comercial_rooms'] ?
                        $comercial_rooms_policy['amount_comercial_rooms'] :
                        null
                );

                $property->setDesignViewProperty($this->command->get('design_view_property'));

                //Quinta seccion
                $credit_card_policy = $this->command->get('credit_card_policy');
                $property->setCreditCard($credit_card_policy['credit_card']);
                $property->setCreditCardAmex(
                    $credit_card_policy['credit_card'] and
                    $credit_card_policy['credit_card_american']
                );
                $property->setCreditCardMc(
                    $credit_card_policy['credit_card'] and
                    $credit_card_policy['credit_card_master']
                );
                $property->setCreditCardVisa(
                    $credit_card_policy['credit_card'] and
                    $credit_card_policy['credit_card_visa']
                );
                $cash_policy = $this->command->get('cash_policy');
                $property->setCash($cash_policy['cash']);
                $property->setMaxCash($cash_policy['cash'] ? $cash_policy['to'] : null);
                $property->setDebit($this->command->get('debit'));

                //sexta seccion
                $property->setTax($this->command->get('tax'));
                $property->setTaxRate($this->command->get('tax_rate') / 100);
                $property->setRateType($this->command->get('rate_type'));
                $property->setBasicQuota($this->command->get('quota_basis'));
                $currency = $rCurrencyType->findOneByArray(
                    array('id' => $this->command->get('currency_id'))
                );
                $property->setCurrency($currency);

                $city_tax_policy = $this->command->get('city_tax_policy');
                $property->setCityTax($city_tax_policy['city_tax'] / 100);
                $property->setCityTaxCurrency(
                    !empty($city_tax_policy['city_tax_currency_id']) ?
                        $rCurrencyType->findOneByArray(
                            array('id' => $city_tax_policy['city_tax_currency_id'])
                        ) :
                        null
                );
                $property->setCityTaxMaxNights($city_tax_policy['city_tax_max_nights']);
                $property->setCityTaxType($city_tax_policy['city_tax_type']);

                //septima seccion
                $property->setDescription($this->command->get('description'));
                $property->setAdditionalInfo($this->command->get('additional_info'));

                $rProperty->save($property);
                if ($oldSlug != $property->getSlug()) {
                    $property->renameFolders($oldSlug);
                }
                $this->saveLog($data, $property, $rf);
                $response = new ResponseCommandBus(201, 'Ok',array('slug'=>$property->getSlug()));
            } catch (EntityValidationException $e) {
                $response = new ResponseCommandBus(400, 'Bad Request','Error en los datos ingresado, verifique e intente de nuevo'.$e->getAttribute().$e->getMessage());
            } catch (\Exception $e) {
                $response = new ResponseCommandBus(500, 'Internal Server Error',$e->getMessage().$e->getFile().$e->getLine());
            }
        } else {
            $response = new ResponseCommandBus(400, 'Bad Request','Error en la petición');
        }
        return $response;
    }

    /**
     * Esta función es usada para Guardar un log dado las distintas
     * acción que pueden ocurrir dentro del caso de uso.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $log          Arreglo con la información anterior
     * @param $property         Objeto Habitación
     *
     * @return boolean
     */
    public function saveLog($data, $property, $rf)
    {
        $response = [];
        $response += $this->propertyLog($data["propertyC"], $property);
        $response += $this->languagesLog($data["languagesC"]->toArray(), $property->getLanguages()->toArray());
        $response += $this->ContactsLog($data["contactC"]->toArray(), $property->getContacts()->toArray());

        if (empty($response))
            return false;

        $data["action"] = "update";
        $data["resource"] = "property";
        $data["idResource"] = $property->getId();
        $data["description"] = $response;
        $data['property'] = $property;
        $data['user'] = CoreSession::getUser();

        $logsUser = new LogsUser();
        $logsUser->updateObject($data);
        $rf->get("LogsUser")->save($logsUser);

        return true;
    }

    /**
     * Esta función es usada para buscar el historico de los
     * contactos de un establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $contactsC        Arreglo con la información anterior
     * @param $contacts         Objetos Contactos
     *
     * @return Array
     */
    public function ContactsLog($contactsC, $contacts)
    {
        $contacts = array_map(
            function($c)
            {
                return $c->getName()." - ".$c->getCharge();
            },
            $contacts
        );
        $contactsC = array_map(
            function($c)
            {
                return $c->getName()." - ".$c->getCharge();
            },
            $contactsC
        );

        $response["deleteContacts"] = array_diff($contactsC, $contacts);
        $response["addContacts"] = array_diff($contacts, $contactsC);


        return (empty($response["deleteContacts"]) and empty($response["addContacts"])) ? [] : $response;
    }

    /**
     * Esta función es usada para buscar el historico de los
     * idiomas que maneja un establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $contactsC        Arreglo con la información anterior
     * @param $contacts         Objetos Contactos
     *
     * @return Array
     */
    public function languagesLog($languageC, $language)
    {
        $languageC  = array_map(
            function($l)
            {
                return $l->getId();
            },
            $languageC
        );

        $language  = array_map(
            function($l)
            {
                return $l->getId();
            },
            $language
        );

        $respo["deleteLanguage"] = array_diff($languageC,$language);
        $respo["addLanguage"] = array_diff($language,$languageC);

        return (empty($respo["deleteLanguage"]) and empty($respo["addLanguage"])) ? [] : $respo;
    }

    /**
     * Esta función es usada para buscar el historico de la
     * informacion que maneja un establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param $propertyC        Arreglo con la información anterior
     * @param $property         Objeto establei
     *
     * @return Array
     */
    public function propertyLog($propertyC, $property)
    {
        $res = [];
        if ($property->getName() != $propertyC->getName())
            $res['name'] = $property->getName();

        $accommodationC = is_object($propertyC->getAccommodation()) ? $propertyC->getAccommodation()->getId() : null;
        $accommodation = is_object($property->getAccommodation()) ? $property->getAccommodation()->getId() : null;
        if ($accommodationC != $accommodation)
            $res['currency'] = $accommodation;

        if ($property->getStar() != $propertyC->getStar())
            $res['star'] = $property->getStar();

        $phones = is_object($property->getPhones()) ? $property->getPhones()->toString() : $property->getPhones();
        if ($phones != $propertyC->getPhones())
            $res['phone'] = $phones;

        $fax = is_object($property->getFax()) ? $property->getFax()->toString() : $property->getFax();
        if ($fax != $propertyC->getFax())
            $res['currency'] = $fax;


        if ($property->getLocation()->getId() != $propertyC->getLocation()->getId())
            $res['location'] = $property->getLocation()->getId();


        $coordinates = is_object($property->getCoordinates()) ? $property->getCoordinates()->toArray() : $property->getCoordinates();
        if (array_diff($coordinates, $propertyC->getCoordinates()))
            $res['coordinates'] = $coordinates;

        if ($property->getAddress() != $propertyC->getAddress())
            $res['address'] = $property->getAddress();
        if ($property->getOpeningYear() != $propertyC->getOpeningYear())
            $res['openingYear'] = $property->getOpeningYear();
        if ($property->getRenewalYear() != $propertyC->getRenewalYear())
            $res['renewalYear'] = $property->getRenewalYear();
        if ($property->getPublicAreasRenewalYear() != $propertyC->getPublicAreasRenewalYear())
            $res['publicAreasRenewalYear'] = $property->getPublicAreasRenewalYear();

        $urlWeb = is_object($property->getUrlWeb()) ? $property->getUrlWeb()->toString() : $property->getUrlWeb();
        if ($urlWeb != $propertyC->getUrlWeb())
            $res['urlWeb'] = $urlWeb;

        if ($property->getHotelChainName() != $propertyC->getHotelChainName())
            $res['hotelChainName'] = $property->getHotelChainName();
        if ($property->getAmountRoom() != $propertyC->getAmountRoom())
            $res['amountRoom'] = $property->getAmountRoom();
        if ($property->getNumberFloor() != $propertyC->getNumberFloor())
            $res['numberFloor'] = $property->getNumberFloor();
        if ($property->getCheckIn()->format("H:m") != $propertyC->getCheckIn()->format("H:m"))
            $res['checkIn'] = $property->getCheckIn()->format("H:m");
        if ($property->getCheckOut()->format("H:m") != $propertyC->getCheckOut()->format("H:m"))
            $res['checkOut'] = $property->getCheckOut()->format("H:m");
        if ($property->getCheckInAge() != $propertyC->getCheckInAge())
            $res['checkInAge'] = $property->getCheckInAge();
        if ($property->getAdultAge() != $propertyC->getAdultAge())
            $res['adultAge'] = $property->getAdultAge();
        if ($property->getChild() != $propertyC->getChild())
            $res['child'] = $property->getChild();
        if ($property->getAllIncluded() != $propertyC->getAllIncluded())
            $res['allIncluded'] = $property->getAllIncluded();
        if ($property->getBeds() != $propertyC->getBeds())
            $res['beds'] = $property->getBeds();
        if ($property->getBedsPriorNotice() != $propertyC->getBedsPriorNotice())
            $res['bedsPriorNotice'] = $property->getBedsPriorNotice();
        if ($property->getBedsAdditionalCost() != $propertyC->getBedsAdditionalCost())
            $res['bedsAdditionalCost'] = $property->getBedsAdditionalCost();
        if ($property->getCribs() != $propertyC->getCribs())
            $res['cribs'] = $property->getCribs();
        if ($property->getCribsMax() != $propertyC->getCribsMax())
            $res['cribsMax'] = $property->getCribsMax();
        if ($property->getCribsPriorNotice() != $propertyC->getCribsPriorNotice())
            $res['cribsPriorNotice'] = $property->getCribsPriorNotice();
        if ($property->getCribsAdditionalCost() != $propertyC->getCribsAdditionalCost())
            $res['cribsAdditionalCost'] = $property->getCribsAdditionalCost();
        if ($property->getPets() != $propertyC->getPets())
            $res['pets'] = $property->getPets();
        if ($property->getPetsAdditionalCost() != $propertyC->getPetsAdditionalCost())
            $res['petsAdditionalCost'] = $property->getPetsAdditionalCost();
        if ($property->getComercialRooms() != $propertyC->getComercialRooms())
            $res['comercialRooms'] = $property->getComercialRooms();
        if ($property->getDesignViewProperty() != $propertyC->getDesignViewProperty())
            $res['designViewProperty'] = $property->getDesignViewProperty();
        if ($property->getCreditCard() != $propertyC->getCreditCard())
            $res['creditCard'] = $property->getCreditCard();
        if ($property->getCreditCardAmex() != $propertyC->getCreditCardAmex())
            $res['creditCardAmex'] = $property->getCreditCardAmex();
        if ($property->getCreditCardMc() != $propertyC->getCreditCardMc())
            $res['creditCardMc'] = $property->getCreditCardMc();
        if ($property->getCreditCardVisa() != $propertyC->getCreditCardVisa())
            $res['creditCardVisa'] = $property->getCreditCardVisa();
        if ($property->getCash() != $propertyC->getCash())
            $res['cash'] = $property->getCash();
        if ($property->getMaxCash() != $propertyC->getMaxCash())
            $res['maxCash'] = $property->getMaxCash();
        if ($property->getDebit() != $propertyC->getDebit())
            $res['debit'] = $property->getDebit();

        $currencyC = is_object($propertyC->getCurrency()) ? $propertyC->getCurrency()->getId() : null;
        $currency = is_object($property->getCurrency()) ? $property->getCurrency()->getId() : null;
        if ($currencyC != $currency)
            $res['currency'] = $currency;

        if ($property->getTax() != $propertyC->getTax())
            $res['tax'] = $property->getTax();
        if ($property->getTaxRate() != $propertyC->getTaxRate())
            $res['taxRate'] = $property->getTaxRate();
        if ($property->getCityTax() != $propertyC->getCityTax())
            $res['cityTax'] = $property->getCityTax();

        $cityTaxCurrencyC = is_object($propertyC->getCityTaxCurrency()) ? $propertyC->getCityTaxCurrency()->getId() : null;
        $cityTaxCurrency = is_object($property->getCityTaxCurrency()) ? $property->getCityTaxCurrency()->getId() : null;
        if ($cityTaxCurrencyC != $cityTaxCurrency)
            $res['cityTaxCurrency'] = $cityTaxCurrency;

        if ($property->getCityTaxMaxNights() != $propertyC->getCityTaxMaxNights())
            $res['cityTaxMaxNights'] = $property->getCityTaxMaxNights();
        if ($property->getCityTaxType() != $propertyC->getCityTaxType())
            $res['cityTaxType'] = $property->getCityTaxType();
        if ($property->getRateType() != $propertyC->getRateType())
            $res['rateType'] = $property->getRateType();
        if ($property->getBasicQuota() != $propertyC->getBasicQuota())
            $res['basicQuota'] = $property->getBasicQuota();
        if ($property->getDescription() != $propertyC->getDescription())
            $res['description'] = $property->getDescription();
        if ($property->getAdditionalInfo() != $propertyC->getAdditionalInfo())
            $res['additionalInfo'] = $property->getAdditionalInfo();

        return empty($res) ? [] : ["infoProperty" =>$res];
    }
}