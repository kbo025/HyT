<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Application\Services\RateCalculator;
use Navicu\Core\Domain\Model\Entity\Room;

/**
 * Clase RateByPeople.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del incremento
 * por numero de personas de una habitación.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RateByPeople
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el incremento del precio asignado por persona.
     * 
     * @var Float 
     */
    protected $amount_rate;
    
    /**
     * Esta propiedad es usada para interactuar con el numero de persona a quien se le asigna el precio.
     * 
     * @var Integer
     */
    protected $number_people;
    
    /**
     * Esta propiedad es usada para interactuar con la Habitación a quien se le asigno el precio y el numero de persona.
     * 
     * @var Property Type Object
     */
    protected $room;

    public function __construct($data = null)
    {
        $this->updateObject($data);
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
     * Set amount_rate
     *
     * @param float $amountRate
     * @return RateByPeople
     */
    public function setAmountRate($amountRate)
    {
        $this->amount_rate = $amountRate;

        return $this;
    }

    /**
     * Get amount_rate
     *
     * @return float 
     */
    public function getAmountRate()
    {
        return $this->amount_rate;
    }

    /**
     * esta funcion calcula el aumento final por persona dependiendo de las politicas de precios seleccionados por el hotelero
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 27-10-2015
     *
     * @param $sellRate float
     * @return float
     */
    public function getSellAmountRate($sellRate)
    {
        $amount = 0;
        if ($this->room->getIncrement()) { //si la habitacion tiene incremento por persona
            if ($this->room->getVariationTypePeople()==0) {
                $amount = RateCalculator::calculateClientRate($this->amount_rate,$this->room->getProperty());
            } else {
                $amount = $this->amount_rate * $sellRate;
            }
        }
        return $amount;
    }

    /**
     * Set number_people
     *
     * @param integer $numberPeople
     * @return RateByPeople
     */
    public function setNumberPeople($numberPeople)
    {
        $this->number_people = $numberPeople;

        return $this;
    }

    /**
     * Get number_people
     *
     * @return integer 
     */
    public function getNumberPeople()
    {
        return $this->number_people;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return RateByPeople
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
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @return array
     */
    public function toArray()
    {
        return [
            'id' => empty($this->id) ? null : $this->id,
            'amount_rate' => empty($this->amount_rate) ? null : $this->amount_rate,
            'number_people' => empty($this->number_people) ? null : $this->number_people,
        ];
    }

    public function updateObject($data)
    {
        $this->amount_rate = isset($data['amount_rate']) ?
            $data['amount_rate'] :
            (isset($data['amountRate']) ?
                $data['amountRate'] :
                null);
        $this->number_people = isset($data['number_people']) ?
            $data['number_people'] :
            (isset($data['numberPeople']) ?
                $data['numberPeople'] : null);
    }
}
