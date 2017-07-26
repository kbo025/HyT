<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Application\Services\RateCalculator;

/**
 * Clase DailyPack.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los cambios diarios de un Servicio.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DailyPack
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha del Servicio Diario.
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * Esta propiedad es usada para saber si un Servicio Diario esta creado con todos
     * sus valores.
     *
     * @var Boolean
     */
    protected $is_completed;

    /**
     * Esta propiedad es usada para interactuar con el minimo de noche del Servicio Diario.
     *
     * @var Integer
     */
    protected $min_night;

    /**
     * Esta propiedad es usada para interactuar con el maximo de noche del Servicio Diario.
     *
     * @var Integer
     */
    protected $max_night;

    /**
     * Esta propiedad es usada para interactuar con la disponibilidad especifica del Servicio Diario.
     *
     * @var Integer
     */
    protected $specific_availability;

    /**
     * Esta propiedad es usada para interactuar con el precio base del Servicio Diario.
     *
     * @var Float
     */
    protected $base_rate;

    /**
     * Esta propiedad es usada para interactuar con el precio de venta base
     * (baseRate + basePolicy)del Servicio Diario.
     *
     * @var Float
     */
    protected $sell_rate;

    /**
     * Esta propiedad es usada para interactuar con el precio neto base
     * (sellRate - discountRate) del Servicio Diario.
     *
     * @var Float
     */
    protected $net_rate;

    /**
     * Esta propiedad es usada para interactuar con el close out del Servicio Diario.
     *
     * @var Boolean
     */
    protected $close_out;

    /**
     * Esta propiedad es usada para interactuar con el close to arrival del Servicio Diario.
     *
     * @var Boolean
     */
    protected $closed_to_arrival;

    /**
     * Esta propiedad es usada para interactuar con el close to departure del Servicio Diario.
     *
     * @var Boolean
     */
    protected $closed_to_departure;

    /**
     * Esta propiedad es usada para interactuar con el servicio a quien se le asigna los cambios diarios.
     *
     * @var Pack Type Object
     */
    protected $pack;

    /**
     * @var \DateTime ultima modificación del daily
     */
    private $last_modified;

    /**
     * coleccion de bloqueos de disponibilidad
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lockeds;

    /**
     * @var boolean
     */
    private $promotion = false;

    /**
     * Constructor de la entidad asignando valores por defecto
     */
    public function __construct()
    {
        $this->min_night = 1;
        $this->max_night = 365;
        $this->closed_to_arrival = false;
        $this->closed_to_departure = false;
        $this->close_out = false;
		$this->is_completed = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DailyPack
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get date
     *
     * @return String
     */
    public function getStringDate()
    {
        return is_object($this->date) ? $this->date->format('Y-m-d') : $this->date;
    }

    /**
     * Set is_completed
     *
     * @param boolean $isCompleted
     *
     * @return DailyPack
     */
    public function setIsCompleted($isCompleted)
    {
        $this->is_completed = $isCompleted;

        return $this;
    }

    /**
     * Get is_completed
     *
     * @return boolean
     */
    public function getIsCompleted()
    {
        return $this->is_completed;
    }

    /**
     * Set min_night
     *
     * @param integer $minNight
     *
     * @return DailyPack
     */
    public function setMinNight($minNight)
    {
        $this->min_night = $minNight;

        return $this;
    }

    /**
     * Get min_night
     *
     * @return integer
     */
    public function getMinNight()
    {
        return $this->min_night;
    }

    /**
     * Set max_night
     *
     * @param integer $maxNight
     *
     * @return DailyPack
     */
    public function setMaxNight($maxNight)
    {
        $this->max_night = $maxNight;

        return $this;
    }

    /**
     * Get max_night
     *
     * @return integer
     */
    public function getMaxNight()
    {
        return $this->max_night;
    }

    /**
     * Set specific_availability
     *
     * @param integer $specificAvailability
     *
     * @return DailyPack
     */
    public function setSpecificAvailability($specificAvailability)
    {
        $this->specific_availability = $specificAvailability;

        return $this;
    }

    /**
     * Get specific_availability
     *
     * @return integer
     */
    public function getSpecificAvailability()
    {
        return $this->specific_availability;
    }

    /**
     * Set base_rate
     *
     * @param float $baseRate
     *
     * @return DailyPack
     */
    public function setBaseRate($baseRate)
    {
        $this->base_rate = $baseRate;

        return $this;
    }

    /**
     * Get base_rate
     *
     * @return float
     */
    public function getBaseRate()
    {
        return $this->base_rate;
    }

    /**
     * Set sell_rate
     *
     * @param float $sellRate
     *
     * @return DailyPack
     */
    public function setSellRate($sellRate)
    {
        $this->sell_rate = $sellRate;

        return $this;
    }

    /**
     * Get sell_rate
     *
     * @return float
     */
    public function getSellRate()
    {
        return $this->sell_rate;
    }

    /**
     * devuelve la tarifa de venta incluyendo el impuesto del establesimiento si es necesario incluirlo
     *
     * @return float
     */
    public function getSellRateWithTax()
    {
        $sellRate = $this->sell_rate;
        $property = $this
            ->pack
            ->getRoom()
            ->getProperty();
        if (!$property->getTax() && $property->getRateType()==2) {
            $sellRate = $sellRate * (1+$property->getTaxRate());
        }
        return $sellRate;
    }

    /**
     * Set net_rate
     *
     * @param float $netRate
     *
     * @return DailyPack
     */
    public function setNetRate($netRate)
    {
        $this->net_rate = $netRate;

        return $this;
    }

    /**
     * Get net_rate
     *
     * @return float
     */
    public function getNetRate()
    {
        return $this->net_rate;
    }

    /**
     * Set close_out
     *
     * @param boolean $closeOut
     *
     * @return DailyPack
     */
    public function setCloseOut($closeOut)
    {
        $this->close_out = $closeOut;

        return $this;
    }

    /**
     * Get close_out
     *
     * @return boolean
     */
    public function getCloseOut()
    {
        return $this->close_out;
    }

    /**
     * Set closed_to_arrival
     *
     * @param boolean $closedToArrival
     *
     * @return DailyPack
     */
    public function setClosedToArrival($closedToArrival)
    {
        $this->closed_to_arrival = $closedToArrival;

        return $this;
    }

    /**
     * Get closed_to_arrival
     *
     * @return boolean
     */
    public function getClosedToArrival()
    {
        return $this->closed_to_arrival;
    }

    /**
     * Set closed_to_departure
     *
     * @param boolean $closedToDeparture
     *
     * @return DailyPack
     */
    public function setClosedToDeparture($closedToDeparture)
    {
        $this->closed_to_departure = $closedToDeparture;

        return $this;
    }

    /**
     * Get closed_to_departure
     *
     * @return boolean
     */
    public function getClosedToDeparture()
    {
        return $this->closed_to_departure;
    }

    /**
     * Set pack
     *
     * @param Pack $pack
     *
     * @return DailyPack
     */
    public function setPack(Pack $pack = null)
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return Pack
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * Función para el manejo del slug de un establecimiento desde el dailyPack.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return String
     */
    public function getSlug()
    {
        return $this->pack->getRoom()->getProperty()->getSlug();
    }

    /**
     * La función actualiza los datos de un DailyPack, dado un array ($data)
     * con los valores asociados al DailyPack
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Array $data
     * @return void
     */
    public function updateObject($data)
    {
        if (isset($data['date']) and $this->date == null)
            if (gettype($data['date']) != "object") {
                $this->date = date_create($data['date']);
            } else {
                $this->date = $data['date'];
            }

        if (isset($data['minNight']))
            $this->min_night = $data['minNight'];

        if (isset($data['maxNight']))
            $this->max_night = $data['maxNight'];

        if (isset($data['specificAvailability']))
           $this->specific_availability = $data['specificAvailability'];

        if (isset($data['closeOut']))
            $this->close_out = $data['closeOut'];
        else if (!$this->close_out)
            $this->close_out = false;

        if (isset($data['closedToDeparture']))
            $this->closed_to_departure = $data['closedToDeparture'];
        else if (!$this->closed_to_departure)
            $this->closed_to_departure = false;

        if (isset($data['closedToArrival']))
            $this->closed_to_arrival = $data['closedToArrival'];
        else if (!$this->closed_to_arrival)
            $this->closed_to_arrival = false;

        if (isset($data['price']))
            $this->setRate($data['price']);

        return $this;
    }

    /**
     * Función para el manejo de los precio sellRate,
     * netRate y baseRate, dependiendo de la configuración del
     * establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param $rate
     *
     * @return DailyPack
     */
    public function setRate($rate)
    {
        if(is_null($rate))
            return $this;

        $type = $this->getPack()->getRoom()->getProperty()->getRateType();
        $property = $this->getPack()->getRoom()->getProperty();

        $basePolicy = $this->getPack()
            ->getRoom()->getProperty()
            ->getBasePolicy()->getCancellationPolicy();

        $discountRate = 1 - $this->getPack()->getRoom()->getProperty()->getDiscountRate();
        $tax = $this->getPack()->getRoom()->getProperty()->getTax();
        $taxRate = 1 + $this->getPack()->getRoom()->getProperty()->getTaxRate();

        switch ($type) {
            case 1: //NetRate
                $netRate = $rate;
                $sellRate = self::calculateClientRate($rate, $property );

                break;
            case 2: //SellRate
                $sellRate = $rate;

                if ($tax) { // Con Iva
                    $netRate = $sellRate * $discountRate;
                } else { // Sin Iva
                    $netRate = ($sellRate * $taxRate) * $discountRate;
                }

                break;
        }

        if ($basePolicy->getVariationType() == 1) {
            $baseRate = $sellRate - ($sellRate * $basePolicy->getVariationAmount());
        } else {
            $baseRate = $sellRate - $basePolicy->getVariationAmount();
        }

        $this->base_rate = round($baseRate, 3);
        $this->sell_rate = round($sellRate, 3);
        $this->net_rate = round($netRate, 3);

        return $this;
    }

    /**
     * Función para el manejo de la información por
     * medio de un arreglo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function getArray()
    {
        $data['idDailyPack'] = $this->id;
        $data['date'] = $this->getStringDate();
        $data['minNight'] = $this->min_night;
        $data['maxNight'] = $this->max_night;
        $data['idPack'] = $this->pack->getId();
        $data['specificAvailability'] = $this->specific_availability;
        $data['closeOut'] = $this->close_out;
        $data['closedToDeparture'] = $this->closed_to_departure;
        $data['closedToArrival'] = $this->closed_to_arrival;
        $data['isCompleted'] = $this->is_completed;
        $data['sellRate'] = $this->sell_rate;
        $data['netRate'] = $this->net_rate;

        return $data;
    }

    /**
     * Esta función es usada para actualizar ciertas propiedades de
     * un daily bajo los criterios de una habitación "dailyRoom". Dichos
     * criterios son: Valores por defectos cuando la minNight o maxNight
     * vienen nulo y Valores por defecto cuando la disponibilidad de
     * un dailyPack viene nula.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $arrayDaily
     * @return Null
     */
    public function updateRestriction($objDailyRoom)
    {
        if (!$this->min_night && $objDailyRoom)
            $this->min_night = $objDailyRoom->getMinNight();

        if (!$this->max_night && $objDailyRoom)
            $this->max_night = $objDailyRoom->getMaxNight();

        if (!$this->specific_availability && $objDailyRoom)
            $this->specific_availability = $objDailyRoom->getAvailability();

        if ($objDailyRoom) {

            if ($this->min_night < $objDailyRoom->getMinNight())
                $this->min_night = $objDailyRoom->getMinNight();

            if ($this->max_night > $objDailyRoom->getMaxNight())
                $this->max_night = $objDailyRoom->getMaxNight();
        }

        return $this;
    }

    /**
     * Función para el manejo la propiedad is_completed,
     * saber si esta o no completo el dailyPack.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return void
     */
    public function isCompleted() {
        $error = CoreValidator::getValidator($this, array(
			'isNull', 'isNullSellRate'
        ));

		if (count($error)) {
			$this->setIsCompleted(false);
		} else {
			$this->setIsCompleted(true);
        }
        return $this;
    }

    /**
     * Set last_modified
     *
     * @param \DateTime $lastModified
     * @return DailyPack
     */
    public function setLastModified()
    {
        $this->last_modified = new \DateTime();

        return $this;
    }

    /**
     * Get last_modified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->last_modified;
    }


    /**
     * funcion que calcula el sell rate de un precio especifico tomando en cuenta las politicas del establecimiento
     */
    private static function calculateClientRate($price,$property)
    {
        $sellPrice = $price;

        //si se cargo tarifa neta
        if ( $property->getRateType()==1 )
            $sellPrice = $price / (1-$property->getDiscountRate());

        if ( !$property->getTax())
            $sellPrice = $sellPrice * (1+$property->getTaxRate());

        return round($sellPrice,2);
    }

    /**
     * Add lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     * @return DailyPack
     */
    public function addLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds[] = $lockeds;

        return $this;
    }

    /**
     * Remove lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     */
    public function removeLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds->removeElement($lockeds);
    }

    /**
     * Get lockeds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLockeds()
    {
        return $this->lockeds;
    }

    /**
     * calcula la disponibilidad bloqueada del dp
     */
    public function getLockeAvailability($idSession = [])
    {
        $locked = 0;
        $now = strtotime('now');
        foreach ($this->lockeds as $lock) {
            if (($lock->getExpiry() > $now) && (!in_array($lock->getIdSession(),$idSession)))
                 $locked++;
        }
        return $locked;
    }

    /**
     * indica si el dp tiene disponibilidad bloqueada
     */
    public function isLocked()
    {
        return $this->getLockeAvailability() > 0;
    }

    /**
     * Set promotion
     *
     * @param boolean $promotion
     *
     * @return DailyPack
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return boolean
     */
    public function getPromotion()
    {
        return $this->promotion;
    }
}
