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
 * Modela un Bar/Discoteca asociado a un establecimiento
 * 
 * Se define una clase y una serie de propiedades necesarias para el manejo de Listado de
 * los Bares y Discotecas.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Bar
{
    const TYPE_BAR = 1;
    const TYPE_DISCO = 2;

    /**
     * identificador de la entidad
     * @var integer
     */
    protected $id;

    /**
     * nombre del bar/discoteca
     * @var string
     */
    protected $name;

    /**
     * edad minima para ingresar al establecimiento
     * @var integer
     */
    protected $min_age;

    /**
     * indica si el local sirve comida
     * @var boolean
     */
    protected $food;

    /**
     * una descripcion del sitio
     * @var string
     */
    protected $description;

    /**
     * OV que representa el horario de actividades del local
     * @var array
     */
    protected $schedule;

    /**
     * indica si esta activo el local
     * @var boolean
     */
    protected $status;

    /**
     * indica si es bar (1) o discoteca (2)
     * @var integer
     */
    protected $type;

    /**
     * relacion con el Objeto PropertyServices que lo relaciona con un establecimiento
     * @var Object PropertyService
     */
    protected $service;

    /**
     * relacion con el objeto FoodType representa el tipo de comida servido en el local
     * @var array
     */
    protected $food_type;

    /**
    * Contiene los errores conseguidos luego de validar la entidad (este attributo no se persiste en BD)
    * @var array
    */
    protected $errors;

    /**
     * Constructor de la entidad
     */
    public function __construct()
    {
        $this->status = false;
        $this->food = false;
        $this->type = self::TYPE_BAR;
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
     *
     * @return Bar
     */
    public function setName($name)
    {
        if(empty($name))
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
     * Set min_age
     *
     * @param integer $minAge
     *
     * @return Bar
     */
    public function setMinAge($minAge)
    {
        if(empty($minAge))
            throw new EntityValidationException('min_age',get_class($this),'not_null');
        elseif ( !is_integer($minAge) or $minAge<0 )
            throw new EntityValidationException('min_age',get_class($this),'invalid');
        else
            $this->min_age = $minAge;

        return $this;
    }

    /**
     * Get min_age
     *
     * @return integer 
     */
    public function getMinAge()
    {
        return $this->min_age;
    }

    /**
     * Set food
     *
     * @param boolean $food
     *
     * @return Bar
     */
    public function setFood($food)
    {
        $this->food = !empty($food);

        return $this;
    }

    /**
     * Get food
     *
     * @return boolean 
     */
    public function getFood()
    {
        return $this->food;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Bar
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
     *
     * @return Bar
     */
    public function setSchedule(Schedule $schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return array 
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
     *
     * @return Bar
     */
    public function setStatus($status)
    {
        $this->status = !empty($status);

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
     * Set type
     *
     * @param integer $type
     *
     * @return Bar
     */
    public function setType($type)
    {
        if (empty($type))
            throw new EntityValidationException('type',get_class($this),'not_null');
        elseif (!is_integer($type) or ($type!=1 and $type!=2))
            throw new EntityValidationException('type',get_class($this),'invalid');
        else
            $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set service
     *
     * @param PropertyService $service
     *
     * @return Bar
     */
    public function setService(PropertyService $service)
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
     * Get food_type
     *
     * @return FoodType
     */
    public function getFoodType()
    {
        return $this->food_type;
    }
    
    /**
     * Set food_type
     *
     * @param FoodType $food_type
     * @return Bar
     */
    public function setFoodType(FoodType $food_type = null)
    {
        if  ($this->food)
        {
            if(empty($food_type))
                throw new EntityValidationException('food_type',get_class($this),'not_null');
            else
                $this->food_type = $food_type;
        }
        return $this;
    }

    /**
     * Devuelve una representacion del objeto como un array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function toArray()
    {
        $res = array();

        if (!empty($this->id)) {
            $res['id'] = $this->id;
        }

        if (!empty($this->name)) {
            $res['name'] = $this->name;
        }

        if (!empty($this->min_age)) {
            $res['min_age'] = $this->min_age;
        }

        if (!empty($this->description)) {
            $res['description'] = $this->description;
        }

        if (!empty($this->schedule) and $this->schedule instanceof Schedule) {
            $res['schedule'] = $this->schedule->toArray();
        }

        if (!empty($this->type)) {
            $res['type'] = $this->type;
        }

        if (!empty($this->food_type)) {
            $res['food_type'] = $this->food_type->getId();
        }

        $res['status'] = !empty($this->status);
        $res['food']=!empty($this->food);

        return $res;
    }

    /**
     * indica si la intancia se encuentra en un estado valido y almacena los posibles errores en el atributo errors
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function validate()
    {
        $this->errors = array();

        if (empty($this->min_age)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'min_age',
                    'error' => 'No debe estar vacio'
                )
            );
        } else {
            if (!is_integer($this->min_age) || $this->min_age < 1) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'min_age',
                        'error' => 'es inavlido'
                    )
                );
            }
        }

        if (empty($this->type)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'type',
                    'error' => 'No debe estar vacio'
                )
            );
        } else {
            if( 
                !is_integer($this->type) ||
                $this->type<1 ||
                $this->type>2
            ) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'type',
                        'error' => 'es inavlido'
                    )
                );
            }
        }

        if (empty($this->name)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'name',
                    'error' => 'no debe estar vacio'
                )
            );
        }

        if (empty($this->schedule)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'schedule',
                    'error' => 'es invalido'
                )
            );
        }

        return empty($this->errors);
    }

    /**
     * devuelve el atributo errors que contiene los errores encotrados en la entidad tras una validacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *  funcion que se ejecuta antes de persistir la entidad
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function prePersist()
    {
        if(isset($this->schedule) && ($this->schedule instanceof Schedule))
            $this->schedule = $this->schedule->toArray();
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getTypeString()
    {
		switch ($this->type) {
			case 1:
				return "Bar";
			case 2:
				return "Discoteca";
		}
    }
}
