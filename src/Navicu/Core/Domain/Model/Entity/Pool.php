<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\PropertyService;

/**
 * Clase poll modela un Piscina o parecidos
 * 
 * Se define una clase y una serie de propiedades necesarias para el manejo de
 * Piscina o parecidos
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Pool
{

    /**
     * identificador de la entidad
     * @var integer
     */
	protected $id;

    /**
     * indica el numero de personas que puede usar la piscina
     * @var integer
     */
	protected $capacity;

    /**
     * descripcion de la piscina
     * @var string
     */
	protected $description;

    /**
    * relacion con el PropertyService que lo relaciona con un establecimiento
    * @var string
    */
	protected $service;

    /**
    * Contiene los errores conseguidos luego de validar la entidad
    * @var array
    */
    protected $errors;

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
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Pool
     */
    public function setCapacity($capacity)
    {
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
     * Set description
     *
     * @param string $description
     *
     * @return Pool
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
     * Set service
     *
     * @param PropertyService $service
     *
     * @return Pool
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
     * Devuelve una representacion del objeto en array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function toArray()
    {
        $res=array();
        if(!empty($this->id))$res['id']=$this->id;
        if(!empty($this->capacity))$res['capacity']=$this->capacity;
        if(!empty($this->description))$res['description']=$this->description;
        return $res;
    }

    /**
     * indica si la entidad se encuentra en un estado valido
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function validate()
    {
        $validate = true;
        $this->errors=array();
        $this->errors['capacity']=array();
        if(empty($this->capacity)){
            $validate = false;
            array_push($this->errors['capacity'],'capacity can not be empty');
        }
        if(!is_integer($this->capacity)){
            $validate = false;
            array_push($this->errors['capacity'],'capacity must be an integer');
        }
        return $validate;
    }

    /**
     * devuelve el array de errores conseguido tras una validacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
