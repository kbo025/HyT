<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clase AAVVAdditionalQuota.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de un conjunto
 * de costos adicionales para agencia de viaje
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class AAVVAdditionalQuota
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Esta propiedad es usada para interactuar con la descripción de
     * un costo adicional para la AAVV.
     *
     * @var string
     */
    private $description;

    /**
     * Esta propiedad es usada para interactuar con el monto con el que se realizo el pago.
     *
     * @var float
     */
    private $amount;

    /**
     * Esta propiedad es usada para interactuar con el conjunto de agencias de viajes
     * a las que se les asigno el costo adicional.
     *
     * @var \Navicu\Core\Domain\Model\Entity\AAVV
     */
    private $aavvs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aavvs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set description
     *
     * @param string $description
     * @return AAVVAdditionalQuota
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
     * Add aavvs
     *
     * @param \Navicu\Core\Domain\Model\Entity\Hobbies $aavvs
     * @return AAVVAdditionalQuota
     */
    public function addAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavvs)
    {
        $this->aavvs[] = $aavvs;

        return $this;
    }

    /**
     * Remove aavvs
     *
     * @param \Navicu\Core\Domain\Model\Entity\Hobbies $aavvs
     */
    public function removeAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavvs)
    {
        $this->aavvs->removeElement($aavvs);
    }

    /**
     * Get aavvs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAavvs()
    {
        return $this->aavvs;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return AAVVAdditionalQuota
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
}
