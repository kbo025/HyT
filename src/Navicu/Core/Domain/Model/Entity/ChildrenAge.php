<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entidad encargada de guardar las edades de las criaturas que se estan hospedando
 * ChildrenAge
 */
class ChildrenAge
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Edad de la criatura
     * @var integer
     */
    private $age;

    /**
     * Relacion con la entidad contenedora
     * 
     * @var \Navicu\Core\Domain\Model\Entity\ReservationPack
     */
    private $reservation_package;

    /**
     * Constructor
     * @param $age int edad de la criatura
     */
    public function __construct($age)
    {
        $this->age = $age;
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
     * Set age
     *
     * @param integer $age
     * @return ChildrenAge
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set reservation_package
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackage
     * @return ChildrenAge
     */
    public function setReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackage = null)
    {
        $this->reservation_package = $reservationPackage;

        return $this;
    }

    /**
     * Get reservation_package
     *
     * @return \Navicu\Core\Domain\Model\Entity\ReservationPack 
     */
    public function getReservationPackage()
    {
        return $this->reservation_package;
    }
}
