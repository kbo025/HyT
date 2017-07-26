<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 * Clase Agreemente representa el acuerdo legal entre navicu y un establecimiento
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Agreement
{
    /**
     * identificador de la entidad
     *
     * @var integer
     */
    private $id;

    /**
     * direccion ip desde donde se acepto el cuerdo
     *
     * @var string
     */
    private $client_ip_address;

    /**
     * fecha y hora del momento en que se acepto el acuerdo
     *
     * @var \DateTime
     */
    private $date;

    /**
     * relacion con el estbalesimiento al cual pertenece el contrato
     *
     * @var Property
     */
    private $property;

    /**
     * referencia al objeto document con la informacion del archivo pdf de terminos y condiciones del establecimiento
     *
     * @var \Navicu\Core\Domain\Model\Entity\Document
     */
    private $pdf;

    /**
     * indica el numero de dias de credito contratado con el establecimiento
     *
     * @var integer
     */
    private $credit_days = 30;

    /**
     * Constructor
     */
    public function __constructor()
    {

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
     * Set client_ip_address
     *
     * @param string $clientIpAddress
     * @return Agreement
     */
    public function setClientIpAddress($clientIpAddress)
    {
        $this->client_ip_address = $clientIpAddress;

        return $this;
    }

    /**
     * Get client_ip_address
     *
     * @return string 
     */
    public function getClientIpAddress()
    {
        return $this->client_ip_address;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Agreement
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
     * Set property
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $property
     * @return Agreement
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Navicu\Core\Domain\Model\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set pdf
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $pdf
     * @return Agreement
     */
    public function setPdf(\Navicu\Core\Domain\Model\Entity\Document $pdf = null)
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * Get pdf
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document 
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Set credit_days
     *
     * @param integer $creditDays
     * @return Agreement
     */
    public function setCreditDays($creditDays)
    {
        $this->credit_days = $creditDays;

        return $this;
    }

    /**
     * Get credit_days
     *
     * @return integer 
     */
    public function getCreditDays()
    {
        return $this->credit_days;
    }
}
