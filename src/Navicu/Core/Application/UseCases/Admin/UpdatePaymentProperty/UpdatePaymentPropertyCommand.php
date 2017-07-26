<?php

namespace Navicu\Core\Application\UseCases\Admin\UpdatePaymentProperty;


use Navicu\Core\Application\Contract\Command;

class UpdatePaymentPropertyCommand implements Command
{

    private $slug;
    private $same_data_property;
    private $name;
    private $location;
    private $address;
    private $fiscal_code;
    private $charging_system;
    private $account_number_part1;
    private $account_number_part2;
    private $account_number_part3;
    private $account_number_part4;
    private $currency_id;
    private $swift;
    private $discountRate;
    private $creditDays;


    /**
     * @param $slug
     * @param $data
     */
    public function __construct($slug,$data)
    {
        $this->slug = $slug;
        $this->same_data_property = self::asign($data,'same_data_property');
        $this->name = self::asign($data,'name');
        $this->setLocation($data['address']);
        $this->address = self::asign($data['address'],'address');
        $this->fiscal_code = self::asign($data,'fiscal_code');
        $this->charging_system = self::asign($data,'charging_system');
        $this->account_number_part1 = self::asign($data,'account_number_part1');
        $this->account_number_part2 = self::asign($data,'account_number_part2');
        $this->account_number_part3 = self::asign($data,'account_number_part3');
        $this->account_number_part4 = self::asign($data,'account_number_part4');
        $this->discountRate = self::asign($data,'discountRate');
        $this->creditDays = self::asign($data,'creditDays');
        $this->currency_id = self::asign($data,'currency_id');
        $this->swift = self::asign($data,'swift');

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
        return array();
    }
}