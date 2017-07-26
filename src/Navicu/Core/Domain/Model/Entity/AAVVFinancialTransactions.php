<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clase AAVVFinancialTransactions.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de un conjunto
 * de movimiento financiero asignadas a una agencia de viaje
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVFinancialTransactions
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha en la que se realizo
     * el movimiento financiero.
     *
     * @var date
     */
    private $date;

    /**
     * Esta propiedad es usada para interactuar con la descripción asignada al movimiento
     * financiero
     *
     * @var string
     */
    private $description;

    /**
     * Esta propiedad es usada para interactuar con el operador (-,+) asignado al movimiento
     * financiero
     *
     * @var string
     */
    private $sign;

    /**
     * Esta propiedad es usada para interactuar con el tipo de movimiento
     * financiero
     *
     * @var integer
     */
    private $transactions_type;

    /**
     * Esta propiedad es usada para interactuar con el estatus del movimiento
     * financiero
     *
     * @var integer
     */
    private $status;

    /**
     * Esta propiedad es usada para interactuar con el monto con el que se se realizo
     * el movimiento financiero.
     *
     * @var float
     */
    private $amount;

    /**
     * Esta propiedad es usada para interactuar con la agencia de viajes
     * a las que se les asigno un conjunto de movimiento financiero.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavv;

    /**
     * Constructor de la entidad asignando valores por defecto
     */
    public function __construct()
    {
        $this->date = new \DateTime("now");
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
     * Set date
     *
     * @param \DateTime $date
     * @return AAVVFinancialTransactions
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AAVVFinancialTransactions
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
     * Set sign
     *
     * @param string $sign
     * @return AAVVFinancialTransactions
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign
     *
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return AAVVFinancialTransactions
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVFinancialTransactions
     */
    public function setAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavv = null)
    {
        $this->aavv = $aavv;

        return $this;
    }

    /**
     * Get aavv
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVV
     */
    public function getAavv()
    {
        return $this->aavv;
    }

    /**
     * La función actualiza los datos de un AAVVFinancialTransactions,
     * dado un array ($data) con los valores asociados a un movimiento
     * financiero.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return void
     */
    public function updateObject($data)
    {
        $this->description = $data["description"];
        $this->sign = $data["sign"];
        $this->amount = $data["amount"];

        return $this;
    }
}
