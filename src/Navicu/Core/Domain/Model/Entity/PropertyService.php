<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\ServiceType;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\Pool;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\ValueObject\Schedule;

/**
 * Clase PropertyService.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * los servicios que ofrece un establecimiento.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PropertyService
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el precio del servicio del Establecimiento.
     *
     * @var Float
     */
    protected $cost_service;

    /**
     *  indica si el servicio es gratuito
     * @var Boolean
     */
    protected $free;

    /**
     *  indica un horario de prestacion del servicio
     * @var Object Schedule
     */
    protected $schedule;

    /**
     * indica cantidad de 'instancias' del servicio representado
     * @var integer
     */
    protected $quantity;

    /**
     * Esta propiedad es usada para interactuar con el Establecimiento de un Servicio.
     * @var Property Type Object
     */
    protected $property;

    /**
     * Relacion con Objetos Restaurnat en caso de ser este el tipo de servicio
     * @var Array Colletion
     */
    protected $restaurants;

    /* Esta propiedad es usada para interactuar con el valor asignado en la lista de
     * tipos de servicios de establecimiento.
     * @var ServiceType
     */
    protected $type;

    /**
     * Relacion con Objetos Pool en caso de ser este el tipo de servicio
     * @var Array Colletion
     */
    protected $pools;

    /**
     * Relacion con Objetos Bar en caso de ser este el tipo de servicio
     * @var Array Colletion
     */
    protected $bars;

    /**
     * Relacion con Objetos Salon en caso de ser este el tipo de servicio
     * @var Array Colletion
     */
    protected $salons;


    /**
    *   esta variable contiene las validaciones fallidas de la instancia de la entidad
    *   @var Array
    */
    protected $errors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->free = true;
        $this->bars = new ArrayCollection();
        $this->restaurants = new ArrayCollection();
        $this->pools = new ArrayCollection();
        $this->salons = new ArrayCollection();
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
     * Set cost_service
     *
     * @param float $costService
     * @return PropertyService
     */
    public function setCostService($costService)
    {
        $this->cost_service = $costService;

        return $this;
    }

    /**
     * Get cost_service
     *
     * @return float 
     */
    public function getCostService()
    {
        return $this->cost_service;
    }

    /**
     * Set free
     *
     * @param boolean $free
     * @return PropertyService
     */
    public function setFree($free)
    {
        $this->free = $free;

        return $this;
    }

    /**
     * Get free
     *
     * @return boolean 
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Set schedule
     *
     * @param Shedule $schedule
     * @return PropertyService
     */
    public function setSchedule(Schedule $schedule = null )
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return PropertyService
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set property
     *
     * @param Property $property
     * @return PropertyService
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;
    }

    /**
     * Add restaurants
     *
     * @param Restaurant $restaurants
     * @return PropertyService
     */
    public function addRestaurant(Restaurant $restaurants)
    {
        if(!$this->isRestaurant()) {
            throw new \Exception('invalid action for type of service');
        }
        $this->restaurants[] = $restaurants;
    }

    /**
     * Remove restaurants
     *
     * @param Restaurant $restaurants
     */
    public function removeRestaurant(Restaurant $restaurants)
    {
        if(!$this->isRestaurant()){
            throw new \Exception('invalid action for type of service');
        }
    }

    /**
     * Get restaurants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestaurants()
    {
        if(!$this->isRestaurant()){
            throw new \Exception('invalid action for type of service');
        }
        return $this->restaurants;
    }

    /**
     * Add bars
     *
     * @param Bar $bars
     * @return PropertyService
     */
    public function addBar(Bar $bars)
    {
        if(!$this->isBar()){
            throw new \Exception('invalid action for type of service '.$this->type->getType());
        }
        $this->bars[] = $bars;
        return $this;
    }

    /**
     * Remove bars
     *
     * @param Bar $bars
     */
    public function removeBar(Bar $bars)
    {
        if(!$this->isBar()){
            throw new \Exception('invalid action for type of service');
        }
        $this->bars->removeElement($bars);
    }

    /**
     * Get bars
     *
     * @return ArrayCollection
     */
    public function getBars()
    {
        if(!$this->isBar()){
            throw new \Exception('invalid action for type of service');
        }
        return $this->bars;
    }

    /**
     * Add pools
     *
     * @param Pool $pools
     * @return PropertyService
     */
    public function addPool(Pool $pools)
    {
        if(!$this->isPool()){        
            throw new \Exception('invalid action for type of service');
        }
        $this->pools[] = $pools;
        return $this;
    }

    /**
     * Get property
     *
     * @return Property 
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set type
     *
     * @param ServiceType $type
     * @return PropertyService
     */
    public function setType(ServiceType $type = null)
    {
        $this->type = $type;
    }

    /**
     * Remove pools
     *
     * @param Pool $pools
     */
    public function removePool(Pool $pools)
    {
        if(!$this->isPool()){
            throw new \Exception('invalid action for type of service');
        }
        $this->pools->removeElement($pools);
    }

    /**
     * Get pools
     *
     * @return ArrayCollection
     */
    public function getPools()
    {
        if(!$this->isPool()){   
            throw new \Exception('invalid action for type of service');
        }
        return $this->pools;
    }

    /**
     * Add salons
     *
     * @param Salon $salons
     * @return PropertyService
     */
    public function addSalon(Salon $salons)
    {
        if(!$this->isSalon()){
            throw new \Exception('invalid action for type of service');
        }
        $this->salons[] = $salons;
        return $this;
    }

    /**
     * Remove salons
     *
     * @param Salon $salons
     */
    public function removeSalon(Salon $salons)
    {
        if(!$this->isSalon()){
            throw new \Exception('invalid action for type of service');
        }
        $this->salons->removeElement($salons);
    }

    /**
     * Get type
     *
     * @return ServiceType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get salons
     *
     * @return ArrayCollection
     */
    public function getSalons()
    {
        if(!$this->isSalon()&&!$this->isAuditorium()){
            throw new \Exception('invalid action for type of service');
        }
        return $this->salons;
    }

    /**
     *  devuelve una representacion del objeto como un array
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return array
     */
    public function toArray()
    {
        $res = array();
        if (isset($res['id'])) {
            $res['id']=$this->id;
        }
        $res['type']=$this->type->getId();
        $res['cost_service']=$this->cost_service;
        $res['free']=$this->free;
        $res['schedule']= empty($this->schedule) ? null : $this->schedule->toArray();
        $res['quantity']=$this->getQuantity();
        $res['data']=array();
        if($this->isRestaurant()){
            foreach($this->restaurants as $restaurant){
                array_push($res['data'], $restaurant->toArray());
            }
        }
        if ($this->isPool()) {
            foreach ($this->pools as $pool) {
                array_push($res['data'], $pool->toArray());
            }
        }
        if ($this->isBar()) {
            foreach ($this->bars as $bar) {
                array_push($res['data'], $bar->toArray());
            }
        }
        if (($this->isSalon())||($this->isAuditorium())) {
            foreach ($this->salons as $salon) {
                array_push($res['data'], $salon->toArray());
            }
        }
        return $res;
    }

    /**
     *  indica si el servicio es restaurante
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function isRestaurant()
    {
        return isset($this->type) && ($this->type->getType() == 3);
    }

    /**
     *  inidca si el servicio es piscina
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function isPool()
    {
        return false;
        //return $this->type->getTitle()==='Piscinas';
    }

    /**
     *  indica si el servicio es bar
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function isBar()
    {
        return isset($this->type) && $this->type->getType() === 2;
    }

    /**
     *  indica si el servicio es salon
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function isSalon()
    {
        return isset($this->type) && $this->type->getType() == 6;
    }

    /**
     *  indica si el servicio es de tipo salon
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function isAuditorium()
    {
        return isset($this->type) && $this->type->getType() == 6;
    }

    /**
     *  devuelve un valor boolean "true" cuando se este trabajando
     *  con un servicio de tipo: restaurante, bar o salon.
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function servicesOthers()
    {
        if ($this->isSalon() || $this->isBar() || $this->isRestaurant()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *  funcion que se ejecuta antes de persistir la entidad en mi BD
     *
     * @author gabriel camacho <kbo025@gmail.com>
     */
    public function prePersist()
    {
        if (!empty($this->schedule) && $this->schedule instanceof Schedule) {
            $this->schedule=$this->schedule->toArray();
        }
    }

    /**
     *  indica si la entidad se encuentra en un estado valido y almacena los errores en el atributo errors
     *
     * @author gabriel camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function validate()
    {
        $this->errors = array();
        if(!empty($this->type))
        {
            switch($this->type->getType())
            {
                case 2:
                    if ( !empty($this->bars) ) {
                        $errors = array();
                        foreach ( $this->bars as $index => $bar ) {
                            if ($bar instanceof Bar) {
                                if (!$bar->validate()) {
                                    //$errors[$index] = $bar->getErrors();
                                }
                            } else {
                                $errors[$index] = 'Intancia invalida de Bar';
                            }
                        }
                        if (!empty($errors)) {
                            $this->errors['bars'] = $errors;
                        }
                    } else {
                        array_push(
                            $this->errors,
                            array(
                                'attribute' => 'bars',
                                'error' => 'Debe existir al menos un Bar'
                            )
                        );
                    }
                    break;
                case 3:
                    if ( !empty($this->restaurants) ) {
                        $errors = array();
                        foreach ( $this->restaurants as $index => $rest ) {
                            if ( !($rest instanceof Restaurant) || !$rest->validate() ) {
                                $errors[$index] = $rest->getErrors();
                            }
                        }
                        if (!empty($errors)){
                            $this->errors['restaurants'] = $errors;
                        }
                    } else {
                        array_push(
                            $this->errors,
                            array(
                                'attribute' => 'restaurants',
                                'error' => 'Debe existir al menos un restaurante'
                            )
                        );
                    }
                    break;
                case 4:
                    if(!empty($this->schedule))
                    {
                        if (!($this->schedule instanceof Schedule )) {
                            array_push(
                                $this->errors,
                                array(
                                    'attribute' => 'schedule',
                                    'error' => 'Horario invalido'
                                )
                            );
                        }
                    } else {
                        array_push(
                            $this->errors,
                            array(
                                'attribute' => 'schedule',
                                'error' => 'Horario no debe estar vacio'
                            )
                        );
                    }
                    break;
                case 6:
                    if ( !empty($this->salons) ) {
                        $errors = array();
                        foreach ( $this->salons as $index => $salon ) {
                            if ( !($salon instanceof Salon) || !$salon->validate() ) {
                                $errors[$index] = $salon->getErrors();
                            }
                        }
                        if (!empty($errors)){
                            $this->errors['salons'] = $errors;
                        }
                    } else {
                        array_push(
                            $this->errors,
                            array(
                                'attribute' => 'salons',
                                'error' => 'Debe existir al menos un salon'
                            )
                        );
                    }
                    break;
                case 8:
                    if ( 
                        empty($this->quantity) ||
                        !is_integer($this->quantity) &&
                        $this->quantity < 0
                    ) {
                        array_push(
                            $this->errors,
                            array(
                                'attribute' => 'quantity',
                                'error' => 'Invalido para el tipo de servicio'
                            )
                        );
                    }
                    break;
            }            
        } else {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'type',
                    'error' => 'No debe estar Vacio'
                )
            );
        }
        return empty($this->errors);
    }

   /**
   * devuelve el array de los errores de validacion de una instancia en un momento determinado
   * @author Gabriel Camacho <kbo025@gmail.com>
   * @version 08/07/2015
   * @return array
   */    
    public function getErrors()
    {
        return $this->errors;
    }

   /**
   * Metodo que se ejecuta al ser leido un PropertyService
   * @author Gabriel Camacho <kbo025@gmail.com>
   * @version 08/05/2015
   */
    public function postLoad()
    {
        $this->bars = new ArrayCollection();
        $this->restaurants = new ArrayCollection();
        $this->pools = new ArrayCollection();
        $this->salons = new ArrayCollection();
    }
}
