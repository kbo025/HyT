<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Bedroom;

/**
 *
 * Define cantidad y tipo de una cama la habitacion de un establecimiento
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 21/05/2015
 * 
 */
class Bed
{
	const SINGLE80 = 0;
	const SINGLE90 = 1;
	const SINGLE110 = 2;
	const DOBLE = 3;
	const QUEEN = 4;
	const KING = 5;
	const SOFABED  = 6;
    const BUNKBED = 7;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Bedroom
     */
    private $bedroom;

	/**
	* 	tipo de cama
	*	@var Integer
	*/
	protected $type;

	/**
	* 	cantidad de camas
	*	@var Integer
	*/
	protected $amount;

	/**
	 *	metodo constructor de la clase
     *
     * @author Currently Working: Gabriel Camacho
	 *	@param $type integer
	 *	@param $amount integer
	 */
	public function __construct($type,$amount)
	{
		if ((is_integer($type)) && 
			($type==self::SINGLE80 || $type==self::SINGLE90 ||
			 $type==self::SINGLE110 ||
			 $type==self::DOBLE ||
			 $type==self::QUEEN ||
			 $type==self::KING ||
             $type==self::BUNKBED ||
			 $type==self::SOFABED))
		{
			$this->type = $type;
			if ((is_integer($amount)) && ($amount>0)) {
				$this->amount=$amount;
			} else {
				throw new \Exception('Invalid Amount');
			}
		} else {
			throw new \Exception('Invalid Type');
		}
	}

	/**
	 *	Devuelve el valor de type
     *  @author Currently Working: Gabriel Camacho
	 *	@return Integer
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 *	Devuelve el valor de type comko un string
     *
     *  @author Currently Working: Gabriel Camacho
	 *	@return String
	 */
	public function getTypeString()
	{
		switch($this->type) {
			/*case self::SINGLE80: $ret = 'Individual 80 cm de ancho'; break;*/
			case self::SINGLE80: $ret = 'Individual'; break;
			/*case self::DOBLE: $ret = 'Doble 150 cm de ancho'; break;*/
			case self::DOBLE: $ret = 'Doble'; break;
			/*case self::QUEEN: $ret = 'Queen 180 cm de ancho'; break;*/
			case self::QUEEN: $ret = 'Queen'; break;
			/*case self::KING: $ret = 'King 2 metros'; break;*/
			case self::KING: $ret = 'King'; break;
			//case self::SOFABED: $ret = 'Sofacama cm de ancho';  break;
			/*case self::SINGLE90: $ret = 'Individual 90 cm de ancho'; break;*/
			case self::SINGLE90: $ret = 'Individual'; break;
			/*case self::SINGLE110: $ret = 'Individual 110 cm de ancho'; break;*/
			case self::SINGLE110: $ret = 'Individual'; break;
            case self::BUNKBED: $ret = 'Litera'; break;
		}

		return $ret;
	}

	/**
	 *	Devuelve el valor amount
	 *	@return Real
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 *	Devuelve una representacion de si mismo como array
	 *	@return Array()
	 */
	public function toArray()
	{
		return [
            'type' => $this->type,
            'typeString' => $this->getTypeString(),
            'amount' => $this->amount
        ];
	}

	/**
	 *	funcion estatica que devuelve los tipos de camas
	 */
	public static function getBedsTypes()
	{
		return array(
            array('id'=>self::DOBLE,'name' => 'Doble 150 cm de ancho'),
            array('id'=>self::SINGLE80,'name' => 'Individual 80 cm de ancho'),
            array('id'=>self::SINGLE90,'name' => 'Individual 90 cm de ancho'),
            array('id'=>self::SINGLE110,'name' => 'Individual 110 cm de ancho'),
            array('id'=>self::KING,'name' => 'King 2 metros'),
            array('id'=>self::QUEEN,'name' => 'Queen 180 cm de ancho'),
            array('id'=>self::BUNKBED,'name' => 'Litera'),
            //array('id'=>self::SOFABED,'name' => 'Sofacama'),
		);
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
     * Set type
     *
     * @param integer $type
     * @return Bed
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return Bed
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set bedroom
     *
     * @param Bedroom $bedroom
     * @return Bed
     */
    public function setBedroom(Bedroom $bedroom = null)
    {
        $this->bedroom = $bedroom;

        return $this;
    }

    /**
     * Get bedroom
     *
     * @return Bedroom
     */
    public function getBedroom()
    {
        return $this->bedroom;
    }
}
