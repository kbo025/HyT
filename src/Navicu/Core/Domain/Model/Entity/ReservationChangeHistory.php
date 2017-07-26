<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Clase ReservationLogs
 *
 * La clase representa el historial de cambio de la reserva.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ReservationChangeHistory 
{

    /**
     * @var integer
     */
    private $id;

    /**
     * Fecha en la que se creo el log de la reserva
     * @var \DateTime
     */
    private $date;

    /**
     * Información relacionada con la reserva.
     * @var integer
     */
    private $data_log;

    /**
     * El status que maneja el log de la reserva.
     * @var integer
     */
    private $status;

    /**
     * El status anterior de la reserva.
     * 
     * @var Object
     */
    private $last_status;

    /**
     * El objeto reservacion relacionado a el log de la reserva.
     *
     * @var Object
     */
    private $reservation;

    /**
     * El objeto usuario relacionado a el log de la reserva.
     *
     * @var Object
     */
    private $user;

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
     * @return ReservationChangeHistory
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
     * Set data_log
     *
     * @param array $dataLog
     * @return ReservationChangeHistory
     */
    public function setDataLog($dataLog)
    {
        $this->data_log = $dataLog;

        return $this;
    }

    /**
     * Get data_log
     *
     * @return array 
     */
    public function getDataLog()
    {
        return $this->data_log;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return ReservationChangeHistory
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set last_status
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $lastStatus
     * @return ReservationChangeHistory
     */
    public function setLastStatus(\Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $lastStatus = null)
    {
        $this->last_status = $lastStatus;

        return $this;
    }

    /**
     * Get last_status
     *
     * @return \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory 
     */
    public function getLastStatus()
    {
        return $this->last_status;
    }

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return ReservationChangeHistory
     */
    public function setUser(\Navicu\InfrastructureBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservation
     * @return ReservationChangeHistory
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
}
