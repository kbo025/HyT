<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\RoomFeatureType;
use Navicu\Core\Domain\Model\Entity\Room;

/**
 * Clase RoomFeature
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * caracteristicas del espacio o interiores de una habitacion.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class RoomFeature
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * @var Integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $amount;

    /**
     * Esta propiedad es usada para interactuar con RoomFeatureType
     * @var RoomFeatureType
     */
    protected $feature;

    /**
     * @var Room
     */
    protected $room;

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
     * Set amount
     *
     * @param integer $amount
     * @return RoomFeature
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set feature
     *
     * @param RoomFeatureType $feature
     * @return RoomFeature
     */
    public function setFeature(RoomFeatureType $feature = null)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * Get feature
     *
     * @return RoomFeatureType 
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return RoomFeature
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
    *   devuelve una representacion del objeto en array
    *   @return array
    */
    public function toArray()
    {
        $res=array();
        if(!empty($this->feature))
            $res['feature'] = $this->feature->getId();
        if(!empty($this->amount))
            $res['amount'] = $this->amount;
        return $res;
    }

    /**
    *   indica que el objeto se encuentra en un estado valido
    *   @return boolean
    */
    public function validate()
    {
        $this->errors = array();
        $this->errors['feature']=array();
        $this->errors['amount']=array();
        if (empty($this->feature)) {
            array_push(
                $this->errors['feature'],
                array('message'=>'feature should not be empty')
            );
        } else {
            if ( ($this->feature->getType()==0)||($this->feature->getTypeVal()==1) ) {
                if( empty($this->amount) ){
                    array_push(
                        $this->errors['amount'],
                        array('message'=>'amount should not be empty')
                    );
                } else {
                    if ( !is_integer($this->amount) || ($this->amount < 1) ) {
                        array_push(
                            $this->errors['amount'],
                            array('message'=>'amount is invalid')
                        );
                    } 
                }
            } else {
                $this->amount =1;
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
    *   retorna un array de errores del objeto
    *   @return array
    */
    public function getErrors()
    {
        return $this->errors;
    }
}
