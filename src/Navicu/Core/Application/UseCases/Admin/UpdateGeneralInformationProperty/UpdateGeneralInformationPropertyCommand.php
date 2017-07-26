<?php

namespace Navicu\Core\Application\UseCases\Admin\UpdateGeneralInformationProperty;


use Navicu\Core\Application\Contract\Command;

class UpdateGeneralInformationPropertyCommand implements Command
{
    private $slug;
    private $name;
    private $star;
    private $phone;
    private $fax;
    private $location;
    private $address;
    private $longitude;
    private $latitude;
    private $accommodation_id;

    private $opening_year;
    private $renewal_year;
    private $public_areas_renewal_year;
    private $url_web;
    private $hotel_chain;
    private $hotel_chain_name;
    private $amount_room;
    private $number_floor;
    private $check_in;
    private $check_out;
    private $languages;

    private $contacts;

    private $check_in_age;
    private $child;
    private $age_policy;
    private $all_included;
    private $beds_policy;
    private $cribs_policy;
    private $pets_policy;
    private $cormecial_rooms_policy;
    private $design_view_property;

    private $credit_card_policy;
    private $cash_policy;
    private $debit;

    private $currency_id;
    private $tax;
    private $tax_rate;
    private $rate_type;
    private $quota_basis;
    private $city_tax_policy;

    private $description;
    private $additional_info;

    /**
     * @param $slug
     * @param $data
     */
    public function __construct($slug,$data)
    {
        $this->slug = $slug;
        $this->name = self::asign($data['location'],'name');
        $this->star = self::asign($data['location'],'star');
        $this->phone = self::asign($data['location'],'phone');
        $this->fax = self::asign($data['location'],'fax');
        $this->setLocation($data['location']['address']);
        $this->address = self::asign($data['location']['address'],'address');
        $this->longitude = self::asign($data['location']['address']['coordinates'],'longitude');
        $this->latitude = self::asign($data['location']['address']['coordinates'],'latitude');
        $this->accommodation_id = self::asign($data['location'],'accommodation_id');

        $this->opening_year = self::asign($data['propertyDetails'],'opening_year');
        $this->renewal_year = self::asign($data['propertyDetails'],'renewal_year');
        $this->public_areas_renewal_year = self::asign($data['propertyDetails'],'public_areas_renewal_year');
        $this->url_web = self::asign($data['propertyDetails'],'url_web');
        $this->hotel_chain = self::asign($data['propertyDetails'],'hotel_chain');
        $this->hotel_chain_name = self::asign($data['propertyDetails'],'hotel_chain_name');
        $this->amount_room = self::asign($data['propertyDetails'],'amount_room');
        $this->number_floor = self::asign($data['propertyDetails'],'number_floor');
        $this->check_in = self::asign($data['propertyDetails'],'check_in');
        $this->check_out = self::asign($data['propertyDetails'],'check_out');
        $this->languages = self::asign($data['propertyDetails'],'languages');

        $this->contacts = $data['contacts'];

        $this->check_in_age = self::asign($data['additionalInformation'],'check_in_age');
        $this->child = self::asign($data['additionalInformation'],'child');
        $this->age_policy = self::asign($data['additionalInformation'],'agePolicy');
        $this->all_included = self::asign($data['additionalInformation']['extras'],'all_included');
        $this->beds_policy = self::asign($data['additionalInformation']['extras'],'beds');
        $this->cribs_policy = self::asign($data['additionalInformation']['extras'],'cribs');
        $this->pets_policy = self::asign($data['additionalInformation']['extras'],'pets');
        $this->cormecial_rooms_policy = self::asign($data['additionalInformation']['extras'],'comercialRooms');
        $this->design_view_property = self::asign($data['additionalInformation']['extras'],'design_view_property');

        $this->credit_card_policy = self::asign($data['paymentsAccepted'],'credit_card');
        $this->cash_policy = self::asign($data['paymentsAccepted'],'cash');
        $this->debit = self::asign($data['paymentsAccepted'],'debit');

        $this->currency_id = self::asign($data['loadingRates']['tax'],'currency_id');
        $this->tax = self::asign($data['loadingRates']['tax'],'tax');
        $this->tax_rate = self::asign($data['loadingRates']['tax'],'tax_rate')  ;
        $this->rate_type = self::asign($data['loadingRates'],'rate_type');
        $this->quota_basis = self::asign($data['loadingRates'],'quota_basis');
        $this->city_tax_policy = self::asign($data['loadingRates'],'cityTax');

        $this->description = self::asign($data['descriptions'],'description');
        $this->additional_info = self::asign($data['descriptions'],'additional_info');
    }

    /**
     * @param $data
     */
    private function setLocation($data)
    {
        if(!empty($data['parish_id']))
            $this->location = $data['parish_id'];
        else if (!empty($data['city_id']))
            $this->location = $data['city_id'];
        else if (!empty($data['state_id']))
            $this->location = $data['state_id'];
        else if (!empty($data['country_id']))
            $this->location = $data['country_id'];
        else
            $this->location = null;
    }


    /**
     * @param $data
     * @param $att
     * @return null
     */
    private static function asign($data,$att)
    {
        return isset($data[$att]) ? $data[$att] : null;
    }

    /**
     * @param $att
     * @return null
     */
    public function get($att)
    {
        return ( isset($this->$att) ? $this->$att : null );
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return [];
    }
}