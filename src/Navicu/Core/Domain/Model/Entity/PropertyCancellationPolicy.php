<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\CancellationPolicy;
use Navicu\Core\Domain\Model\Entity\Pack;

/**
 * Clase PropertyCancellationPolicy.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * distintas politicas de cancelación establecidas para un establecimiento y aplicada
 * a sus habitaciones.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PropertyCancellationPolicy
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;
    
    /**
     * Esta propiedad es usada para interactuar con la politica de cancelación a la
     * que se le fue asignada este establecimiento.
     * 
     * @var CancellationPolicy Type Object
     */
    protected $cancellation_policy;

    /**
     * Esta propiedad es usada para interactuar con los servicios asociados a una
     * serie de politica de cancelación de un establecimiento.
     * 
     * @var Pack Type Object
     */
    protected $packages;

    /**
     * Esta propiedad es usada para interactuar con el Establecimiento a quien se
     * le fue asignada la politica de cancelación.
     *
     * @var Property
     */
    private $property;

    /**
     * Representa las reservaciones de los servicios con las políticas de cancelación
     *
     * @var ArrayCollection
     */
    protected $reservation_packages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->packages = new ArrayCollection();
        $this->reservation_packages = new ArrayCollection();
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
     * Set property
     *
     * @param Property $property
     * @return PropertyCancellationPolicy
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set cancellation_policy
     *
     * @param CancellationPolicy $cancellationPolicy
     * @return PropertyCancellationPolicy
     */
    public function setCancellationPolicy(CancellationPolicy $cancellationPolicy = null)
    {
        $this->cancellation_policy = $cancellationPolicy;

        return $this;
    }

    /**
     * Get cancellation_policy
     *
     * @return CancellationPolicy
     */
    public function getCancellationPolicy()
    {
        return $this->cancellation_policy;
    }

    /**
     * Add packages
     *
     * @param Pack $packages
     * @return PropertyCancellationPolicy
     */
    public function addPackage(Pack $packages)
    {
        $this->packages[] = $packages;

        return $this;
    }

    /**
     * Remove packages
     *
     * @param Pack $packages
     */
    public function removePackage(Pack $packages)
    {
        $this->packages->removeElement($packages);
    }

    /**
     * Get packages
     *
     * @return ArrayCollection
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Add reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     * @return PropertyCancellationPolicy
     */
    public function addReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        $this->reservation_packages[] = $reservationPackages;

        return $this;
    }

    /**
     * Remove reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     */
    public function removeReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        $this->reservation_packages->removeElement($reservationPackages);
    }

    /**
     * Get reservation_packages
     *
     * @return ArrayCollection
     */
    public function getReservationPackages()
    {
        return $this->reservation_packages;
    }
}
