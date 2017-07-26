<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\ValueObject\PublicId;

/**
 * Clase ReservationCanceled
 *
 * La clase representa las cancelaciones realizadas
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 05/11/2015
 */
class ReservationCanceled
{

    /**
     * @var integer
     */
    private $id;

    /**
     * Representa el id publico de la cancelación
     *
     * @var string
     */
    private $public_id;

    /**
     * Representa la fecha que se realizo la cancelación
     *
     * @var \DateTime
     */
    private $date_cancellation;

    /**
     * Representa el reembolso del cliente
     *
     * @var float
     */
    private $refund_client = 0;

    /**
     * Representa el reembolso al hotelero
     *
     * @var float
     */
    private $refund_owner = 0;

    /**
     * Representa el dinero ganado por navicu
     *
     * @var float
     */
    private $no_refund = 0;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Reservation
     */
    private $reservation;

    /**
     * Constructor
     */
    public function __construct()
    {
        //Se le agrega un formato de generación de id publico en base hexadecimal
        $this->public_id = new PublicId('dateHex');
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
     * Set public_id
     *
     * @param string $publicId
     * @return ReservationCanceled
     */
    public function setPublicId($publicId)
    {
        $this->public_id = $publicId;

        return $this;
    }

    /**
     * Get public_id
     *
     * @return string 
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * Set date_cancellation
     *
     * @param \DateTime $dateCancellation
     * @return ReservationCanceled
     */
    public function setDateCancellation($dateCancellation)
    {
        $this->date_cancellation = $dateCancellation;

        return $this;
    }

    /**
     * Get date_cancellation
     *
     * @return \DateTime 
     */
    public function getDateCancellation()
    {
        return $this->date_cancellation;
    }

    /**
     * Set refund_client
     *
     * @param float $refundClient
     * @return ReservationCanceled
     */
    public function setRefundClient($refundClient)
    {
        $this->refund_client = $refundClient;

        return $this;
    }

    /**
     * Get refund_client
     *
     * @return float 
     */
    public function getRefundClient()
    {
        return $this->refund_client;
    }

    /**
     * Set refund_owner
     *
     * @param float $refundOwner
     * @return ReservationCanceled
     */
    public function setRefundOwner($refundOwner)
    {
        $this->refund_owner = $refundOwner;

        return $this;
    }

    /**
     * Get refund_owner
     *
     * @return float 
     */
    public function getRefundOwner()
    {
        return $this->refund_owner;
    }

    /**
     * Set no_refund
     *
     * @param float $noRefund
     * @return ReservationCanceled
     */
    public function setNoRefund($noRefund)
    {
        $this->no_refund = $noRefund;

        return $this;
    }

    /**
     * Get no_refund
     *
     * @return float 
     */
    public function getNoRefund()
    {
        return $this->no_refund;
    }

    /**
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservation
     * @return ReservationCanceled
     */
    public function setReservation(\Navicu\Core\Domain\Model\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Navicu\Core\Domain\Model\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     *  convierte el OV PublicId a su representacion String para el almacenamiento
     */
    public function publicIdToString()
    {
        if($this->public_id instanceof PublicId)
            $this->public_id = $this->public_id->toString();
    }
}
