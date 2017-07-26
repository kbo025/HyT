<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LockedAvailability
 */
class LockedAvailability
{

    /**
     * identificador
     *
     * @var integer
     */
    private $id;

    /**
     * identificador de la sesion que bloquea la disponibilidad
     *
     * @var string
     */
    private $id_session;

    /**
     * numero de paquetes bloqueados
     *
     * @var integer
     */
    private $number_packages;

    /**
     * referencia al DailyPack Bloqueado
     *
     * @var \Navicu\Core\Domain\Model\Entity\DailyPack
     */
    private $blocked_dp;

    /**
     * referencia al DailyRoom Bloqueado
     *
     * @var \Navicu\Core\Domain\Model\Entity\DailyRoom
     */
    private $blocked_dr;

    /**
     * momento de vencimiento del
     *
     * @var integer
     */
    private $expiry;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Reservation
     */
    private $blocked_reservation;

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
     * Set id_session
     *
     * @param string $idSession
     * @return LockedAvailability
     */
    public function setIdSession($idSession)
    {
        $this->id_session = $idSession;

        return $this;
    }

    /**
     * Get id_session
     *
     * @return string 
     */
    public function getIdSession()
    {
        return $this->id_session;
    }

    /**
     * Set number_packages
     *
     * @param integer $numberPackages
     * @return LockedAvailability
     */
    public function setNumberPackages($numberPackages)
    {
        $this->number_packages = $numberPackages;

        return $this;
    }

    /**
     * Get number_packages
     *
     * @return integer 
     */
    public function getNumberPackages()
    {
        return $this->number_packages;
    }

    /**
     * Set blocked_dp
     *
     * @param \Navicu\Core\Domain\Model\Entity\DailyPack $blockedDp
     * @return LockedAvailability
     */
    public function setBlockedDp(\Navicu\Core\Domain\Model\Entity\DailyPack $blockedDp = null)
    {
        $this->blocked_dp = $blockedDp;

        return $this;
    }

    /**
     * Get blocked_dp
     *
     * @return \Navicu\Core\Domain\Model\Entity\DailyPack 
     */
    public function getBlockedDp()
    {
        return $this->blocked_dp;
    }

    /**
     * Set blocked_dr
     *
     * @param \Navicu\Core\Domain\Model\Entity\DailyRoom $blockedDr
     * @return LockedAvailability
     */
    public function setBlockedDr(\Navicu\Core\Domain\Model\Entity\DailyRoom $blockedDr = null)
    {
        $this->blocked_dr = $blockedDr;

        return $this;
    }

    /**
     * Get blocked_dr
     *
     * @return \Navicu\Core\Domain\Model\Entity\DailyRoom 
     */
    public function getBlockedDr()
    {
        return $this->blocked_dr;
    }

    /**
     * Set expiry
     *
     * @param integer $expiry
     * @return LockedAvailability
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function isExpired()
    {
        return strtotime('now') > $this->expiry; 
    }

    /**
     * Set blocked_reservation
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $blockedReservation
     * @return LockedAvailability
     */
    public function setBlockedReservation(\Navicu\Core\Domain\Model\Entity\Reservation $blockedReservation = null)
    {
        $this->blocked_reservation = $blockedReservation;

        return $this;
    }

    /**
     * Get blocked_reservation
     *
     * @return \Navicu\Core\Domain\Model\Entity\Reservation 
     */
    public function getBlockedReservation()
    {
        return $this->blocked_reservation;
    }
}
