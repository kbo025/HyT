<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Pack;

/**
 * Clase PackLinkage.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los Linkages entre Servicios.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class PackLinkage
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;
    
    /**
     * Esta propiedad es usada para interactuar con la fecha de inicio del linkage entre Servicios.
     * 
     * @var \DateTime
     */
    protected $start_date;
    
    /**
     * Esta propiedad es usada para interactuar con la fecha de final del linkage entre Servicios.
     * 
     * @var \DateTime
     */
    protected $end_date;
    
    /**
     * Esta propiedad es usada para interactuar con el tipo de monto entre Servicios.
     * 
     * @var Integer
     */
    protected $variation_type_pack;
    
    /**
     * Esta propiedad es usada para determinar si los Servicio estan enlazado o no por el precio de Venta.
     * 
     * @var Float
     */
    protected $is_linked_sell_rate;
    
    /**
     * Esta propiedad es usada para determinar si los Servicio estan enlazado o no por la disponibilidad.
     * y determinar un incremento o decremento determinado.
     * 
     * @var Integer
     */
    protected $is_linked_availability;
    
    /**
     * Esta propiedad es usada para determinar si los Servicio estan enlazado o no por el close out.
     * 
     * @var Boolean
     */
    protected $is_linked_close_out;
    
    /**
     * Esta propiedad es usada para determinar si los Servicio estan enlazado o no por el close to arrival.
     * 
     * @var Boolean
     */
    protected $is_linked_cta;
    
    /**
     * Esta propiedad es usada para determinar si los Servicio estan enlazado o no por el close to departure.
     * 
     * @var Boolean
     */
    protected $is_linked_ctd;
    
    /**
     * Esta propiedad es usada para determinar si las Habitaciones estan enlazadas o no por el Maximo de Noche.
     * y determinar un incremento o decremento determinado.
     * 
     * @var Integer
     */
    protected $is_linked_max_night;
    
    /**
     * Esta propiedad es usada para determinar si las Habitaciones estan enlazadas o no por el Minimo de Noche.
     * y determinar un incremento o decremento determinado.
     * 
     * @var Integer
     */
    protected $is_linked_min_night;

    /**
     * Esta propiedad es usada para interactuar con el servicio padre del Linkage.
     *
     * @var Pack Type Object
     */
    protected $parent;

    /**
     * Esta propiedad es usada para interactuar con el servicio hijo del Linkage.
     *
     * @var Pack Type Object
     */
    protected $child;

    /**
     * Esta propiedad es usada para interactuar con la habitación de los linkage entre Servicios.
     *
     * @var Pack Type Object
     */
    protected $room;
    /**
     * @var float
     */
    private $is_linked_base_rate;

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
     *
     * @return PackLinkage
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
     *
     * @return PackLinkage
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
     * Set variation_type_pack
     *
     * @param integer $variationTypePack
     *
     * @return PackLinkage
     */
    public function setVariationTypePack($variationTypePack)
    {
        $this->variation_type_pack = $variationTypePack;

        return $this;
    }

    /**
     * Get variation_type_pack
     *
     * @return integer
     */
    public function getVariationTypePack()
    {
        return $this->variation_type_pack;
    }

    /**
     * Set is_linked_sell_rate
     *
     * @param float $isLinkedSellRate
     *
     * @return PackLinkage
     */
    public function setIsLinkedSellRate($isLinkedSellRate)
    {
        $this->is_linked_sell_rate = $isLinkedSellRate;

        return $this;
    }

    /**
     * Get is_linked_sell_rate
     *
     * @return float
     */
    public function getIsLinkedSellRate()
    {
        return $this->is_linked_sell_rate;
    }

    /**
     * Set is_linked_availability
     *
     * @param integer $isLinkedAvailability
     *
     * @return PackLinkage
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
     * Set is_linked_close_out
     *
     * @param boolean $isLinkedCloseOut
     *
     * @return PackLinkage
     */
    public function setIsLinkedCloseOut($isLinkedCloseOut)
    {
        $this->is_linked_close_out = $isLinkedCloseOut;

        return $this;
    }

    /**
     * Get is_linked_close_out
     *
     * @return boolean
     */
    public function getIsLinkedCloseOut()
    {
        return $this->is_linked_close_out;
    }

    /**
     * Set is_linked_cta
     *
     * @param boolean $isLinkedCta
     *
     * @return PackLinkage
     */
    public function setIsLinkedCta($isLinkedCta)
    {
        $this->is_linked_cta = $isLinkedCta;

        return $this;
    }

    /**
     * Get is_linked_cta
     *
     * @return boolean
     */
    public function getIsLinkedCta()
    {
        return $this->is_linked_cta;
    }

    /**
     * Set is_linked_ctd
     *
     * @param boolean $isLinkedCtd
     *
     * @return PackLinkage
     */
    public function setIsLinkedCtd($isLinkedCtd)
    {
        $this->is_linked_ctd = $isLinkedCtd;

        return $this;
    }

    /**
     * Get is_linked_ctd
     *
     * @return boolean
     */
    public function getIsLinkedCtd()
    {
        return $this->is_linked_ctd;
    }

    /**
     * Set is_linked_max_night
     *
     * @param integer $isLinkedMaxNight
     *
     * @return PackLinkage
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
     *
     * @return PackLinkage
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
     * Set room
     *
     * @param Room $room
     *
     * @return PackLinkage
     */
    public function setRoom(Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return Room 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set parent
     *
     * @param Pack $parent
     *
     * @return PackLinkage
     */
    public function setParent(Pack $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Pack 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set child
     *
     * @param Pack $child
     *
     * @return PackLinkage
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
}
