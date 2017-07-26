<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Adapter\EntityValidationException;

/**
 * Clase Salon modela un Salon/Auditorio
 *
 * Se define una clase y una serie de propiedades para el manejo de los
 * Salon/Auditorio de un establecimiento.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Salon
{
    const SALON = 1;
    const AUDITORIUM = 2;
    const TEATRO = 3;

    /**
     * identificador de la entidad
     * @var integer
     */
	protected $id;

    /**
    * Nombre del salon
    * @var string
    */
	protected $name;

    /**
    * cantidad maxima de personas
    * @var integer
    */
	protected $capacity;

    /**
    * indica si es salon o auditorio
    * @var integer
    */
	protected $type;

    /**
    * indica si el salon contiene luz natural
    * @var boolean
    */
	protected $natural_light;

    /**
    * descripcion del salon
    * @var string
    */
	protected $description;

    /**
    * relacion con el PropertyService que lo relaciona con un establecimiento
    * @var integer
    */
	protected $service;

    /**
    * tamaño del espacio en metros cuadrados
    * @var float
    */
    protected $size;

    /**
    * cantidad de salones de este e tipo
    * @var integer
    */
    protected $quantity;

    /**
    * indica si el salon esta activo (true) o inactivo (false)
    * @var boolean
    */
    protected $status;


    /**
    * Contiene los errores conseguidos luego de validar la entidad
    * @var array
    */
    protected $errors;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->natural_light = false;
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
     *
     * @return Salon
     */
    public function setName($name)
    {
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
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Salon
     */
    public function setCapacity($capacity)
    {
        if (empty($capacity))
            throw new EntityValidationException('capacity',get_class($this),'not_null');
        elseif (!is_numeric($capacity) || $capacity<0)
            throw new EntityValidationException('capacity',get_class($this),'invalid');
        else
            $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Salon
     */
    public function setType($type)
    {
        if (empty($type))
            throw new EntityValidationException('type',get_class($this),'not_null');
        elseif (!is_integer($type) || $type<=0 || $type>=4)
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
     * Set naturalLight
     *
     * @param boolean $naturalLight
     *
     * @return Salon
     */
    public function setNaturalLight($naturalLight)
    {
        $this->natural_light = !empty($naturalLight);

        return $this;
    }

    /**
     * Get naturalLight
     *
     * @return boolean
     */
    public function getNaturalLight()
    {
        return $this->natural_light;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Salon
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
     * Set size
     *
     * @param float $size
     *
     * @return Salon
     */
    public function setSize($size)
    {
        if (empty($size))
            throw new EntityValidationException('size',get_class($this),'not_null');
        elseif (!is_numeric($size) || $size<0)
            throw new EntityValidationException('size',get_class($this),'invalid');
        else
            $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Salon
     */
    public function setQuantity($quantity)
    {
        if (!empty($quantity) and !is_integer($quantity))
            throw new EntityValidationException('quantity',get_class($this),'invalid');
        else
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Salon
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
     * Set service
     *
     * @param PropertyService $service
     *
     * @return Salon
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
     * Devuelve una representacion del objeto en array
     *
     * @return Array
     */
    public function toArray()
    {
        $res = array();

        if (!empty($this->id) ) {
            $res['id'] = $this->id;
        }

        if (!empty($this->name)) {
            $res['name'] = $this->name;
        }

        if (!empty($this->capacity)) {
            $res['capacity'] = $this->capacity;
        }

        if (!empty($this->type)) {
            $res['type'] = $this->type;
        }

        if (!empty($this->description)) {
            $res['description'] = $this->description;
        }

        if (!empty($this->size)) {
            $res['size'] = $this->size;
        }

        if (!empty($this->quantity)) {
            $res['quantity'] = $this->quantity;
        }

        $res['status'] = !empty($this->status);
        $res['natural_light'] = !empty($this->natural_light);

        return $res;
    }

    /**
     * indica si el objeto se encuentra en un estado valido
     *
     * @return boolean
     */
    public function validate()
    {
        $this->errors=array();
        if (empty($this->capacity)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'capacity',
                    'error' => 'No debe estar vacio'
                )
            );
        } else {
            if (!is_integer($this->capacity)) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'capacity',
                        'error' => 'No es valido'
                    )
                );
            }
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
        if (!empty($this->quantity)) {
            if (!is_integer($this->quantity)) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'quantity',
                        'error' => 'No es valido'
                    )
                );
            }
        }
        if (!empty($this->size)) {
            if (!is_numeric($this->size)) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'size',
                        'error' => 'No es valido'
                    )
                );
            }
        }
        if (!isset($this->type)) {
            array_push(
                $this->errors,
                array(
                    'attribute' => 'type',
                    'error' => 'No debe estar vacio'
                )
            );
        } else {
            if (($this->type < 1) || ($this->type > 3)) {
                array_push(
                    $this->errors,
                    array(
                        'attribute' => 'type',
                        'error' => 'No es valido'
                    )
                );
            }
        }

        return empty($this->errors);
    }

    /**
     * devuelve el array que contiene los errores conseguidos tras una validacion
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
				return "Salón";
			case 2:
				return "Auditorio";
			case 3:
				return "Teatro";
		}
    }

}
