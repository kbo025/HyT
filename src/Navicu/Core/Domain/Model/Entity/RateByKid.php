<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;


/**
 * Clase RateByPeople.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo del incremento
 * por numero de niños de una habitación.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RateByKid
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el incremento del precio asignado por niño.
     * 
     * @var Float 
     */
    protected $amount_rate;
    
    /**
     * Esta propiedad es usada para interactuar con el numero de niños a quien se le asigna el precio.
     * 
     * @var Integer
     */
    protected $number_kid;
    
    /**
     * Esta propiedad es usada para interactuar con la Habitación a quien se le asigno el precio y el numero de niño.
     * 
     * @var Property Type Object
     */
    protected $room;

    /**
     * Propiedad que indica a que rango de edad de niño pertenece este incremento
     * @var integer
     */
    private $index = 0;


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
     * @return RateByKid
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
     * Set number_kid
     *
     * @param integer $numberKid
     * @return RateByKid
     */
    public function setNumberKid($numberKid)
    {
        $this->number_kid = $numberKid;

        return $this;
    }

    /**
     * Get number_kid
     *
     * @return integer 
     */
    public function getNumberKid()
    {
        return $this->number_kid;
    }

    /**
     * Set room
     *
     * @param \Navicu\Core\Domain\Model\Entity\Room $room
     * @return RateByKid
     */
    public function setRoom(\Navicu\Core\Domain\Model\Entity\Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Navicu\Core\Domain\Model\Entity\Room 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     *   devuelve una representacion del objeto en array
     *
     *   @author Freddy Contreras <freddycontreras3@gmail.com>
     *   @return array
     */
    public function toArray()
    {
        return [
            'id' => empty($this->id) ? null : $this->id,
            'amount_rate' => empty($this->amount_rate) ? null : $this->amount_rate,
            'number_kid' => empty($this->number_kid) ? null :$this->number_kid,
            'index' => $this->index,
        ];
    }

    /**
     *  llena el objeto con los datos entregados en la array $data el cual admite formato camel case o la otra de guiones bajos
     *
     * @param $data
     */
    public function updateObject($data)
    {
        $this->amount_rate = isset($data['amount_rate']) ?
            $data['amount_rate'] :
            (isset($data['amountRate']) ?
                $data['amountRate'] :
                null);
        $this->number_kid = isset($data['number_kid']) ?
            $data['number_kid'] :
            (isset($data['numberKid']) ?
                $data['numberKid'] : null);
    }

    /**
     * Set index
     *
     * @param integer $index
     * @return RateByKid
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get index
     *
     * @return integer 
     */
    public function getIndex()
    {
        return $this->index;
    }
}
