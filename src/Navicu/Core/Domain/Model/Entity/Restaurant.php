<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\Entity\FoodType;

/**
 * Clase Restaurant.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * los restaurante de un establecimiento.
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Restaurant
{
    /**
     * identificador de la entidad
     * @var integer
     */
    protected $id;

    /**
     * nombre del restaurant
     * @var integer
     */
    protected $name;

    /**
     * @var array
     */
    protected $breakfast_time;

    /**
     * @var array
     */
    protected $lunch_time;

    /**
     * @var array
     */
    protected $dinner_time;

    /**
     * @var boolean
     */
    protected $dietary_menu;

    /**
     * @var integer
     */
    protected $buffet_carta;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $schedule;

    /**
     * @var boolean
     */
    protected $status;

    /**
     * @var PropertyService
     */
    protected $service;

    /**
     * @var FoodType
     */
    protected $type;

    /**
     * Contiene los errores conseguidos luego de validar la entidad
     * @var array
     */
    protected $errors;

    public function __construct()
    {
        $this->dietary_menu = false;
        $this->status = false;
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
     * Set name
     *
     * @param string $name
     * @throws EntityValidationException
     * @return Restaurant
     */
    public function setName($name)
    {
        if (empty($name))
            throw new EntityValidationException('name',get_class($this),'not_null');
        else
            $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set breakfast_time
     *
     * @param array $breakfastTime
     * @return  Schedule
     */
    public function setBreakfastTime( Schedule $breakfastTime=null)
    {
        $this->breakfast_time = $breakfastTime;

        return $this;
    }

    /**
     * Get breakfast_time
     *
     * @return Schedule 
     */
    public function getBreakfastTime()
    {
        return $this->breakfast_time;
    }

    /**
     * Set lunch_time
     *
     * @param array $lunchTime
     * @return Restaurant
     */
    public function setLunchTime( Schedule $lunchTime=null)
    {
        $this->lunch_time = $lunchTime;

        return $this;
    }

    /**
     * Get lunch_time
     *
     * @return array 
     */
    public function getLunchTime()
    {
        if ($this->lunch_time) {
            $dataSchedule = $this->lunch_time;
            $dataSchedule["opening"] = date("h:i A", strtotime($dataSchedule["opening"]));
            $dataSchedule["closing"] = date("h:i A", strtotime($dataSchedule["closing"]));
        } else {
            $dataSchedule = null;
        }
        return $dataSchedule;
    }

    /**
     * Set dinner_time
     *
     * @param array $dinnerTime
     * @return Restaurant
     */
    public function setDinnerTime( Schedule $dinnerTime=null)
    {
        $this->dinner_time = $dinnerTime;

        return $this;
    }

    /**
     * Get dinner_time
     *
     * @return array 
     */
    public function getDinnerTime()
    {
        if ($this->dinner_time) {
            $dataSchedule = $this->dinner_time;
            $dataSchedule["opening"] = date("h:i A", strtotime($dataSchedule["opening"]));
            $dataSchedule["closing"] = date("h:i A", strtotime($dataSchedule["closing"]));
        } else {
            $dataSchedule = null;
        }
        return $dataSchedule;
    }

    /**
     * Set dietary_menu
     *
     * @param boolean $dietaryMenu
     * @return Restaurant
     */
    public function setDietaryMenu($dietaryMenu)
    {
        $this->dietary_menu = !empty($dietaryMenu);

        return $this;
    }

    /**
     * Get dietary_menu
     *
     * @return boolean 
     */
    public function getDietaryMenu()
    {
        return $this->dietary_menu;
    }

    /**
     * Set buffet_carta (1: buffet, 2: carta, 3: ambas)
     *
     * @param integer $buffetCarta
     * @throws EntityValidationException
     * @return Restaurant
     */
    public function setBuffetCarta($buffetCarta)
    {
        if (empty($buffetCarta))
            throw new EntityValidationException('buffet_carta',get_class($this),'not_null');
        elseif (!is_integer($buffetCarta))
            throw new EntityValidationException('buffet_carta',get_class($this),'invalid');
        elseif ($buffetCarta <= 0 or $buffetCarta >= 4)
            throw new EntityValidationException('buffet_carta',get_class($this),'invalid');
        else
            $this->buffet_carta = $buffetCarta;

        return $this;
    }

    /**
     * Get buffet_carta
     *
     * @return integer 
     */
    public function getBuffetCarta()
    {
        return $this->buffet_carta;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Restaurant
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set schedule
     *
     * @param array $schedule
     * @return Restaurant
     */
    public function setSchedule( Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return  Schedule  
     */
    public function getSchedule()
    {
        if ($this->schedule) {
            $dataSchedule = $this->schedule;
            $dataSchedule["opening"] = date("h:i A", strtotime($dataSchedule["opening"]));
            $dataSchedule["closing"] = date("h:i A", strtotime($dataSchedule["closing"]));
        } else {
            $dataSchedule = null;
        }
        return $dataSchedule;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Restaurant
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set service
     *
     * @param PropertyService $service
     * @return Restaurant
     */
    public function setService(PropertyService $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return PropertyService 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set type
     *
     * @param FoodType $type
     * @return Restaurant
     */
    public function setType(FoodType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return FoodType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Devuelve una representacion del objeto en array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function toArray()
    {
        $res = array();
        if (!empty($this->id))
            $res['id'] = $this->id;
        if (!empty($this->type))
            $res['type'] = $this->type->getId();
        if (!empty($this->name))
            $res['name'] = $this->name;
        if (!empty($this->breakfast_time) and  $this->breakfast_time instanceof Schedule)
            $res['breakfast_time'] = $this->breakfast_time->toArray();
        if (!empty($this->schedule) and $this->schedule instanceof Schedule)
            $res['schedule'] = $this->schedule->toArray();
        if (!empty($this->lunch_time) and $this->lunch_time instanceof Schedule)
            $res['lunch_time'] = $this->lunch_time->toArray();
        if (!empty($this->dinner_time) and $this->dinner_time instanceof Schedule)
            $res['dinner_time'] = $this->dinner_time->toArray();
        if (!empty($this->description))
            $res['description'] = $this->description;
        $res['buffet_carta'] = $this->buffet_carta;
        $res['status']=!empty($this->status);
        $res['dietary_menu'] =! empty($this->dietary_menu);
        return $res;
    }

    /**
     * indica si el objeto esta en un estado valido y almacena los erroes encontrados en errors
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function validate()
    {
        $this->errors=array();
        if (empty($this->type)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'type',
                    'error' => 'No debe estar vacio'
                )
            );
        }
        if (empty($this->schedule)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'schedule',
                    'error' => 'No debe estar vacio'
                )
            );
        }
        if (empty($this->name)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'name',
                    'error' => 'No debe estar vacio'
                )
            );
        }

        return empty($this->errors);
    }

    /**
     * devuelve el array de errores recogidos tras una validacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *  funcion que se ejecuta antes de persistir la entidad
     */
    public function prePersist()
    {
        if(isset($this->schedule) AND $this->schedule instanceof Schedule)
            $this->schedule = $this->schedule->toArray();
        if(isset($this->breakfast_time) AND $this->breakfast_time instanceof Schedule)
            $this->breakfast_time = $this->breakfast_time->toArray();
        if(isset($this->lunch_time) AND $this->lunch_time instanceof Schedule)
            $this->lunch_time = $this->lunch_time->toArray();
        if(isset($this->dinner_time) AND $this->dinner_time instanceof Schedule)
            $this->dinner_time = $this->dinner_time->toArray();
    }

    /**
     * Función para devolver un valor string dependiendo del valor entero
     * que mantiene almacenada la entidad.
     *
     * @return string
     */
    public function getBuffetCartaString()
    {
        switch ($this->buffet_carta) {
            case 1:
                return "Buffet";
                break;
            case 2:
                return "Carta";
                break;
            case 3:
                return "Buffet-Carta";
                break;
        }
    }
}
