<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\SaveTempRoom;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;


class SaveTempRoomCommand extends CommandBase implements Command
{
    /**
    * slug que identifica al usuario
    * @var $slug Integer
    */
    protected $slug;

    /**
    * tipo de habitacion 
    * @var $type Integer
    */
    protected $type;

    /**
    * subtipo o nombre de habitacion 
    * @var $subtype Integer
    */
    protected $subtype;

    /**
    * subtipo o nombre de habitacion 
    * @var $subtype Integer
    */
    protected $index;

    /**
    * minimo de personas 
    * @var $subtype Integer
    */
    protected $minPeople;

    protected $amount;

    protected $size;

    protected $smoking_policy;

    protected $balcony;

    protected $terrace;

    protected $pool;

    protected $spa;

    protected $amount_persons;

    protected $amount_baths;

    protected $laundry;

    protected $garden;

    protected $amount_living_room;

    protected $amount_dresser;

    protected $amount_bedrooms;

    protected $base_availability;

    protected $amount_dining_room;

    protected $kitchen;

    protected $services;

    protected $rates_by_people;

    protected $rates_by_kids;

    protected $kid_pay_as_adult;

    protected $same_increment_adult;

    protected $same_increment_kid;

    protected $type_rate_people;

    protected $type_rate_kid;

    protected $bedrooms;

    protected $livingrooms;

    protected $beds_combinations;

    protected $increment;

    protected $increment_kid;

    protected $is_admin;

	public function __construct($data = null)
	{
        $this->is_admin = false;
        if(isset($data['slug'])){
            $this->slug = $data['slug'];
        }
        if(isset($data['room']['typeroom'])){
            $this->type = $data['room']['typeroom'];
        }
        if(isset($data['room']['subroom'])){
            $this->subtype = $data['room']['subroom'];
        }
        if(isset($data['room']['index'])){
            $this->index = (integer)$data['room']['index'];
        }
        if(isset($data['room']['numRooms'])){
            $this->amount = $data['room']['numRooms'];
        }
        if(isset($data['room']['sizeRoom'])){
            $this->size = $data['room']['sizeRoom'];
        }
        if(isset($data['room']['smoking'])){
            $this->smoking_policy = $data['room']['smoking'];
        }
        if(isset($data['room']['numBedroom'])){
            $this->amount_bedrooms = $data['room']['numBedroom'];
        }
        if(isset($data['room']['balcony'])){
            $this->balcony = $data['room']['balcony'];
        }
        if(isset($data['room']['terrace'])){
            $this->terrace = $data['room']['terrace'];
        }
        if(isset($data['room']['pool'])){
            $this->pool = $data['room']['pool'];
        }
        if(isset($data['room']['spa'])){
            $this->spa = $data['room']['spa'];
        }
        if(isset($data['room']['numPeople'])){
            $this->amount_persons = $data['room']['numPeople'];
        }
        if(isset($data['room']['minPeople'])){
            $this->minPeople = $data['room']['minPeople'];
        }
        if(isset($data['room']['bath'])){
            $this->amount_baths = $data['room']['bath'];
        }
        if(isset($data['room']['laundry'])){
            $this->laundry = $data['room']['laundry'];
        }
        if(isset($data['room']['garden'])){
            $this->garden = $data['room']['garden'];
        }
        if(isset($data['room']['numLiving'])){
            $this->amount_living_room = $data['room']['numLiving'];
        }
        if(isset($data['room']['dresser'])){
            $this->amount_dresser = $data['room']['dresser'];
        }
        if(isset($data['room']['diner'])){
            $this->amount_dining_room = $data['room']['diner'];
        }
        if(isset($data['room']['cooking'])){
            $this->kitchen = $data['room']['cooking'];
        }
        /*if(isset($data['room']['featuresRoom'])){
            $this->services['all'] = $data['room']['featuresRoom'];
        }*/

        if (isset($data['room']['featuresRoomBeedRoom'])) {
            $this->services['Bedroom'] = $data['room']['featuresRoomBeedRoom'];
        }

        if(isset($data['room']['featuresRoomBath'])){
            $this->services['Bath'] = $data['room']['featuresRoomBath'];
        }

        if(isset($data['room']['featuresRoomOthers'])){
            $this->services['Others'] = $data['room']['featuresRoomOthers'];
        }

        if(isset($data['room']['prices']['priceMax'])){
            $this->max_price_person = $data['room']['prices']['priceMax'];
        }
        if(isset($data['room']['prices']['priceMin'])){
            $this->min_price_person = $data['room']['prices']['priceMin'];
        }
        $this->type_rate_people = isset($data['room']['prices']['variationTypePeople']) ?
            (int)$data['room']['prices']['variationTypePeople'] :
            null;
        $this->type_rate_kid = isset($data['room']['prices']['variationTypeKid']) ?
            $data['room']['prices']['variationTypeKid'] :
            null;
        if (isset($data['room']['prices']['increment'])) {
            $this->increment = $data['room']['prices']['increment'];
        }
        if(isset($data['room']['prices']['incrementKid'])) {
            $this->increment_kid = $data['room']['prices']['incrementKid'];
        }
        if (isset($data['room']['prices']['ratePeople'])) {
            $this->rates_by_people = $data['room']['prices']['ratePeople'];
        }
        if (isset($data['room']['prices']['rateKids'])) {
            $this->rates_by_kids = $data['room']['prices']['rateKids'];
        }
        if(isset($data['room']['bedrooms'])){
            $this->bedrooms = $data['room']['bedrooms'];
        }
        if(isset($data['room']['livings'])){
            $this->livingrooms = $data['room']['livings'];
        }
        if(isset($data['room']['base_availability'])){
            $this->base_availability = $data['room']['base_availability'];
        }
        if(isset($data['room']['combinationsBeds'])){
            $this->beds_combinations = $data['room']['combinationsBeds'];
        }
        $this->kid_pay_as_adult = isset($data['room']['prices']['kidPayAsAdult']) ?
            $data['room']['prices']['kidPayAsAdult'] :
            false;
        $this->same_increment_adult = isset($data['room']['prices']['sameIncrementAdult']) ?
            $data['room']['prices']['sameIncrementAdult'] :
            false;
        $this->same_increment_kid = isset($data['room']['prices']['sameIncrementKid']) ?
            $data['room']['prices']['sameIncrementKid'] :
            false;
	}
}