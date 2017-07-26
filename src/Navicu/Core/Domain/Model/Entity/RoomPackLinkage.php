<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Pack;

/**
 * Clase RoomPackLinkage.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo
 * de los Linkages entre Habitaciones y Servicios.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RoomPackLinkage
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha de inicio del
     * linkage entre Habitaciones y Servicio.
     * 
     * @var \DateTime
     */
    protected $start_date;

    /**
     * Esta propiedad es usada para interactuar con la fecha de final del
     * linkage entre Habitaciones y Servicio.
     * 
     * @var \DateTime
     */
    protected $end_date;

    /**
     * Esta propiedad es usada para determinar si el enlace entre Habitaciones y Servicio
     * estan enlazadas o no por la disponibilidad.
     * 
     * @var Integer
     */
    protected $is_linked_availability;

    /**
     * Esta propiedad es usada para determinar si el enlace entre Habitaciones y Servicio
     * estan enlazadas o no por el Maximo de Noche.
     * 
     * @var Integer
     */
    protected $is_linked_max_night;

    /**
     * Esta propiedad es usada para determinar si el enlace entre Habitaciones y Servicio
     * estan enlazadas o no por el Minimo de Noche.
     * 
     * @var Integer
     */
    protected $is_linked_min_night;

    /**
     * @var Pack
     */
    protected $child;

    /**
     * @var Room
     */
    protected $parent;


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
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return RoomPackLinkage
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return RoomPackLinkage
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set is_linked_availability
     *
     * @param integer $isLinkedAvailability
     * @return RoomPackLinkage
     */
    public function setIsLinkedAvailability($isLinkedAvailability)
    {
        $this->is_linked_availability = $isLinkedAvailability;

        return $this;
    }

    /**
     * Get is_linked_availability
     *
     * @return integer 
     */
    public function getIsLinkedAvailability()
    {
        return $this->is_linked_availability;
    }

    /**
     * Set is_linked_max_night
     *
     * @param integer $isLinkedMaxNight
     * @return RoomPackLinkage
     */
    public function setIsLinkedMaxNight($isLinkedMaxNight)
    {
        $this->is_linked_max_night = $isLinkedMaxNight;

        return $this;
    }

    /**
     * Get is_linked_max_night
     *
     * @return integer 
     */
    public function getIsLinkedMaxNight()
    {
        return $this->is_linked_max_night;
    }

    /**
     * Set is_linked_min_night
     *
     * @param integer $isLinkedMinNight
     * @return RoomPackLinkage
     */
    public function setIsLinkedMinNight($isLinkedMinNight)
    {
        $this->is_linked_min_night = $isLinkedMinNight;

        return $this;
    }

    /**
     * Get is_linked_min_night
     *
     * @return integer 
     */
    public function getIsLinkedMinNight()
    {
        return $this->is_linked_min_night;
    }

    /**
     * Set child
     *
     * @param Pack $child
     * @return RoomPackLinkage
     */
    public function setChild(Pack $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return Pack 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set parent
     *
     * @param Room $parent
     * @return RoomPackLinkage
     */
    public function setParent(Room $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Room 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
