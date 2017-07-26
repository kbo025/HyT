<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\Room;

/**
 *
 * Colleccion de Camas que representa una combinacion de camas en una habitacion
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/08/2015
 * 
 */
class Bedroom
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Room
     */
    private $room;

	/**
	* 	Contenedor de las diferentes combinaciones de camas
	*	@var ArrayCollection
	*/
	protected $beds;

	/**
	*	capacidad de personas
	*	@var Real 
	*/
	protected $amount_people;

	/**
	*	indica si el dormitorio tiene baño
	*	@var Real 
	*/
	protected $bath;

    /**
     * Contiene los errores de la entidad tras
     * @var array
     */
	protected $errors;

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Los reservaciones asociadas con la entidad bedroom
     *
     * @var ArrayCollection
     */
    private $reservation_packages;

	/**
	* 	constructor de la clase
	*/
	public function __construct()
	{
		$this->beds = new ArrayCollection();
        $this->reservation_packages = new ArrayCollection();
	}

	/**
	*	Agrega un objeto Bed
	*	@param $bed Bed
	*/
	public function addBed(Bed $bed)
	{
		$this->beds->add($bed);

        return $this;
	}

	/**
	*	Elimina un objeto Bed
	*	@param $bed Bed
	*/
	public function removeBed(Bed $bed)
	{
		$this->beds->remove($bed->getType());

        return $this;
	}

	/**
	*	retorna cantidad de combinaciones
	*	@return integer
	*/
	public function count()
	{
		return $this->beds->count();
	}

	/**
	*	retorna amount
	*	@return integer
	*/
	public function getAmountPeople()
	{
		return $this->amount_people;
	}

    /**
	*	modifica amount
	*	@param $amount Integer
	*/
	public function setAmountPeople($amount)
	{
		$this->amount_people=$amount;

        return $this;
	}

    /**
	*	retorna amount
	*	@return integer
	*/
	public function getBath()
	{
		return $this->bath;
	}

    /**
	*	modifica amount
	*	@param $amount Integer
	*/
	public function setBath($bath)
	{
		$this->bath=$bath;

        return $this;
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
     * Get beds
     *
     * @return ArrayCollection
     */
    public function getBeds()
    {
        return $this->beds;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return Bedroom
     */
    public function setRoom(Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * devuelve una representacion del objeto en array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     */
    public function toArray()
    {
        $res=array();
        $res['amount_people'] = $this->amount_people;
        $res['bath'] = $this->bath;
        $res['beds'] = array();
        foreach($this->beds as $bed)
        {
            $res['beds'][$bed->getType()]= $bed->toArray();
        }
        return $res;
    }

    /**
     * indica si el objeto esta en un estaod valido y almacena los errores encontrados en el atributo errors
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return boolean
     */
    public function validate(){
        $this->errors=array();
        $this->errors['amount_people'] = array();
        $this->errors['beds'] = array();
        if(empty($this->amount_people)){
            array_push(
                $this->errors['amount_people'],
                array('message'=>'amount_people no debe ser vacio')
            );
        } else {
            if ( is_integer($this->amount_people) ) {
                if( $this->amount_people<1 ) {
                    array_push(
                        $this->errors['amount_people'],
                        array('message'=>'amount_people debe ser mayor a 0')
                    );
                }
            } else {
                array_push(
                    $this->errors['amount_people'],
                    array('message'=>'amount_people debe ser un numero entero')
                );
            }
        }
        if($this->beds->is_empty()){
            array_push(
                $this->errors['beds'],
                array('message'=>'beds no debe ser vacio')
            );
        }
        foreach ($this->errors as $errors) {
            if(empty($errors)){
                unset($errors);
            }
        }
        return empty($this->errors);
    }

    /**
     * devueleve el conjunto de errores encontrados tras una validacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     */
    public function getErrors(){
        return $this->errors;
    }

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Add reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     * @return Bedroom
     */
    public function addReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        $this->reservation_packages[] = null;

        return $this;
    }

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Remove reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     * @return $this
     */
    public function removeReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        return $this;
    }

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Get reservation_packages
     *
     * @return ArrayCollection
     */
    public function getReservationPackages()
    {
        return $this->reservation_packages;
    }
	
    /**
     * Función para la creación de una combinación de camas
     * de una habitación a partir de un arreglo con información basica.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return Object
     */
    public function updateObject($data)
    {
		isset($data["numPersonBedroom"]) ? $this->setAmountPeople($data["numPersonBedroom"]) : $this->setAmountPeople(0);
		isset($data["bathBedroom"]) ? $this->setBath($data["bathBedroom"]) : $this->setBath(0);
	}
}
