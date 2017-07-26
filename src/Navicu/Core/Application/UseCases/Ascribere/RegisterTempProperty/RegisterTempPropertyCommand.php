<?php

namespace Navicu\Core\Application\UseCases\Ascribere\RegisterTempProperty;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* comando 'Registrar Propiedad Temporal'
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 27/05/2015
*/

class RegisterTempPropertyCommand extends CommandBase implements Command
{

    protected $name;
    protected $address;
    protected $star;
    protected $url_web;
    protected $amount_room;
    protected $number_floor;
    protected $check_in;
    protected $check_out;
    protected $currency;
    protected $phones;
    protected $fax;
    protected $opening_year;
    protected $renewal_year;
    protected $public_areas_renewal_year;
    protected $tax;
    protected $description;
    protected $tax_rate;
    protected $is_admin;


    protected $additional_info;
    protected $contacts;
    protected $city;
    protected $longitude;
    protected $latitude;
    protected $slug;

    protected $accommodation;
    protected $languages;

    protected $beds;
    protected $beds_additional_cost;
    protected $beds_prior_notice;

    protected $check_in_age;

    protected $child;
    protected $age_policy;

    protected $cribs_additional_cost;
    protected $cribs_max;
    protected $cribs;
    protected $cribs_prior_notice;

    protected $pets;
    protected $pets_additional_cost;

    protected $cash;
    protected $max_cash;

    protected $city_tax;
    protected $city_tax_currency;
    protected $city_tax_type;
    protected $city_tax_max_nights;

    protected $credit_card;
    protected $credit_card_amex;
    protected $credit_card_mc;
    protected $credit_card_visa;

    protected $hotel_chain;
    protected $hotel_chain_name;

    protected $all_included;
    protected $debit;

    protected $comercial_rooms;
    protected $amount_comercial_rooms;

    protected $design_view_property;

    //protected $discount_rate;
    protected $basic_quota;
    protected $rate_type;

	/**
	* 	constructor de la clase
	*	@param $data Array
	*/
	public function __construct($data = null)
	{
        $this->is_admin = false;
		if(isset($data)&&(is_array($data)))
		{
		    if(array_key_exists('name',$data))
                $this->name=$data['name'];
			//if(array_key_exists('emails',$data))
                //$this->emails=$data['emails'];
		    if(array_key_exists('address',$data))
                $this->address=$data['address'];
		    //if(array_key_exists('postal_code',$data))
                //$this->postal_code=$data['postal_code'];
		    if(array_key_exists('star',$data))
                $this->star=$data['star'];
		    if(array_key_exists('url_web',$data))
                $this->url_web=$data['url_web'];
		    if(array_key_exists('amount_room',$data))
                $this->amount_room=$data['amount_room'];
		    if(array_key_exists('number_floor',$data))
                $this->number_floor=$data['number_floor'];
		    if(array_key_exists('check_in',$data))
                $this->check_in=$data['check_in'];
		    if(array_key_exists('check_out',$data))
                $this->check_out=$data['check_out'];
		    //if(array_key_exists('airport_code',$data))
                //$this->airport_code=$data['airport_code'];
		    if(array_key_exists('currency',$data))
                $this->currency=$data['currency'];
            //if(array_key_exists('other_currency',$data))
                //$this->other_currency=$data['other_currency'];
		    if(array_key_exists('phones',$data))
                $this->phones=$data['phones'];
		    if(array_key_exists('fax',$data))
                $this->fax=$data['fax'];
		    if(array_key_exists('opening_year',$data))
                $this->opening_year=$data['opening_year'];
		    if(array_key_exists('renewal_year',$data))
                $this->renewal_year=$data['renewal_year'];
            if(array_key_exists('description',$data))
                $this->description=$data['description'];
		    if(array_key_exists('public_areas_renewal_year',$data))
                $this->public_areas_renewal_year = $data['public_areas_renewal_year'];
		    if(array_key_exists('check_in_age',$data))
                $this->check_in_age = $data['check_in_age'];

            $this->child = !empty($data['child']);
            $this->age_policy = $data['agePolicy'];

            if ( array_key_exists('beds',$data)  && ($data['beds']['beds']) ) {
                $this->beds = true;
                $this->beds_additional_cost = $data['beds']['value_beds_extra'];
                $this->beds_prior_notice = !empty($data['beds']['prior_notice']);
            }

		    if(array_key_exists('additional_info',$data))
                $this->additional_info=$data['additional_info'];
            if(array_key_exists('contact',$data))
                $this->contacts=$data['contact'];
            if(array_key_exists('longitude',$data))
                $this->longitude=$data['longitude'];
            if(array_key_exists('latitude',$data))
                $this->latitude=$data['latitude'];
		    if(array_key_exists('location',$data))
                $this->city=$data['location'];
            if(array_key_exists('slug',$data))
                $this->slug=$data['slug'];
            //if(array_key_exists('discount_rate',$data))
                //$this->discount_rate=$data['discount_rate'];
            if(array_key_exists('quota_basis',$data))
                $this->basic_quota=$data['quota_basis'];
            if(array_key_exists('tax',$data))
                $this->tax=$data['tax'];
            if(array_key_exists('rate_type',$data)) {
                if (is_integer($data['rate_type']))
                    $this->rate_type = $data['rate_type'];
                else {
                    if ('tarifa neta' == strtolower($data['rate_type']))
                        $this->rate_type = 1; //tarifa neta
                    elseif ('tarifa bruta' == strtolower($data['rate_type']))
                        $this->rate_type = 2; // tarifa bruta
                    else
                        $this->rate_type = 0;
                }
            }
            $this->comercial_rooms=!empty($data['comercial_rooms']);
            $this->amount_comercial_rooms=isset($data['amount_comercial_rooms']) ?
                $data['amount_comercial_rooms'] :
                null;
            if(array_key_exists('tax_rate',$data))
            {
                if ( is_string($data['tax_rate']) ) {
                    if(strtolower($data['tax_rate'])=='si')
                        $this->tax_rate = true;    
                    else if (strtolower($data['tax_rate'])=='no')
                        $this->tax_rate = false;
                    else
                        $this->tax_rate = null;
                } else {
                    $this->tax_rate = $data['tax_rate'];
                }
            }
            if(array_key_exists('accommodation',$data))
                $this->accommodation = $data['accommodation'];
            if( array_key_exists('cribs',$data) && ($data['cribs']['cribs'])  ){
                $this->cribs=true;
                $this->cribs_additional_cost = $data['cribs']['value_cribs'];
                $this->cribs_max = isset($data['cribs']['max_cribs']) ? $data['cribs']['max_cribs'] : null;
                $this->cribs_prior_notice =  isset($data['cribs']['prior_notice']) ? $data['cribs']['prior_notice'] : null;
            }
            if(array_key_exists('mascot',$data)){
                $this->pets=$data['mascot']['mascot'];
                $this->pets_additional_cost = $data['mascot']['value_mascot'];
            }
            $this->hotel_chain = !empty($data['hotel_chain']);
            if ($this->hotel_chain) {
                $this->hotel_chain_name = isset($data['hotel_chain_name']) ?
                    $data['hotel_chain_name'] :
                    null;
            }
            if (array_key_exists('cash',$data)) {
                $this->cash = $data['cash']['cash'];
                $this->max_cash = isset($data['cash']['to']) ? $data['cash']['to'] : null;
            }
            if ( array_key_exists('credit_card',$data) && ($data['credit_card']['credit_card'])) {
                $this->credit_card = true;
                if(isset($data['credit_card']['credit_card_american']))
                    $this->credit_card_amex = $data['credit_card']['credit_card_american'];
                if(isset($data['credit_card']['credit_card_master']))
                    $this->credit_card_mc = $data['credit_card']['credit_card_master'];
                if(isset($data['credit_card']['credit_card_visa']))
                    $this->credit_card_visa = $data['credit_card']['credit_card_visa'];
            }
            if(array_key_exists('amount',$data)) {
                $this->city_tax = $data['amount'];
            }
            $this->all_included = !empty($data['all_included']);
            $this->debit = !empty($data['debit']);
            if(array_key_exists('currency_city',$data)) {
                $this->city_tax_currency = $data['currency_city'];
            }
            if(array_key_exists('nightperson',$data)){
                if (is_string($data['nightperson'])) {
                    switch(strtolower($data['nightperson']))
                    {
                        case 'persona':
                            $this->city_tax_type = 1;
                            break;
                        case 'noche':
                            $this->city_tax_type = 2;
                            break;
                        case 'persona y noche':
                            $this->city_tax_type = 3;
                            break;
                    }
                } else {
                    $this->city_tax_type = isset($data['nightperson']) ? $data['nightperson'] : null;
                }
            }
            if (array_key_exists('max_night',$data)) {
                $this->city_tax_max_nights = $data['max_night'];
            }
            if (array_key_exists('languages',$data)) {
                $this->languages = $data['languages'];
            }
            
            $this->design_view_property = !empty($data['design_view_property']) ? $data['design_view_property'] : 2;
        }
	}

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;
    }
}