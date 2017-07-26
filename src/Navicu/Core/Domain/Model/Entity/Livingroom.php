<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Room;

/**
 * Representa las salas de estar de un tipo de habitacion de un establecimiento
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Livingroom
{
	/**
	*	cantidad de sofacama
	*	@var integer
	*/
	protected $amount_couch;

	/**
	*	capacidad del espacio
	*	@var integer
	*/
	protected $amount_people;

    /**
     * contiene los errores que fueron encontrados tras una validacion
     * @var array
     */
	protected $errors;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Room
     */
    private $room;

	/**
	* 	constructor de la clase
	*/
	public function __construct()
	{

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
	public function getAmountCouch()
	{
		return $this->amount_couch;

	}

	/**
	*	modifica amount
	*	@param $amount Integer
	*/
	public function setAmountCouch($amount_couch)
	{
		$this->amount_couch=$amount_couch;

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
     * Set room
     *
     * @param Room $room
     * @return Livingroom
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
        $res['amount_couch'] = $this->amount_couch;
        return $res;
    }

    /**
     * indica si el objeto se encuentra en un estado valido y almacena los errores encontrados en errors
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Boolean
     */
    public function validate(){
        $this->errors=array();
        $this->errors['amount_people'] = array();
        $this->errors['amount_couch'] = array();
        if(empty($this->amount_people)){
            array_push(
                $this->errors['amount_people'],
                array('message'=>'amount_people no debe ser vacio')
            );
        } else {
            if ( is_integer($this->amount_people) ){
                if( $this->amount_people<1) {
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
        if(empty($this->amount_couch)){
            array_push(
                $this->errors['amount_couch'],
                array('message'=>'amount_couch no debe ser vacio')
            );
        } else {
            if ( is_integer($this->amount_couch) ){
                if( $this->amount_couch<1) {
                    array_push(
                        $this->errors['amount_couch'],
                        array('message'=>'amount_couch debe ser mayor a 0')
                    );
                }
            } else {
                array_push(
                    $this->errors['amount_couch'],
                    array('message'=>'amount_couch debe ser un numero entero')
                );
            }
        }
        foreach($this->errors as $errors){
            if(empty($errors)){
                unset($errors);
            }
        }
        return empty($this->errors);
    }

    /**
     * devueleve un array con los errores encontrados tras un proceso de validacion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return array
     */
    public function getErrors(){
        return $this->errors;
    }
}
