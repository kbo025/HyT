<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\EntityValidationException;

/**
 * ReservationPack
 *
 * La siguiente entidad representa los servicios asociadas a un reserva
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/08/2015
 */
class ReservationPack
{
   
    /**
     * @var integer
     */
    private $id;

    /**
     * Números de habitaciones asociadas a los servicios de la reserva
     * @var integer
     */
    private $number_rooms;

    /**
     * Precio del pack en la reserva
     * @var float
     */
    private $price;

    /**
     * Representa la reserva a la cual pertenece la entidad
     * @var \Navicu\Core\Domain\Model\Entity\Reservation
     */
    private $reservation_id;

    /**
     * Representa el servicio asociado a la reserva
     * @var \Navicu\Core\Domain\Model\Entity\Pack
     */
    private $pack_id;

    /**
     * Representa la política asociada a la reserva
     * @var \Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy
     */
    private $property_cancellation_policy_id;

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Representa el conjunto de camas asociadas
     * @var \Navicu\Core\Domain\Model\Entity\BedRoom
     */
    private $bedroom_id;

    /**
     * @var array
     */
    private $bedroom;

    /**
     * @var array
     */
    private $cancellation_policy;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Category
     */
    private $type_pack;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\RoomType
     */
    private $type_room;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\Category
     */
    private $type_cancellation_policy;

    /**
     * Numero de adultos para cada ReservationPack de este tipo
     *
     * @var integer
     */
    private $number_adults;

    /**
     * Numero de niños para cada ReservationPack de este tipo
     *
     * @var integer
     */
    private $number_kids;

    /**
     * costo del pack para el hotelero
     */
    private $net_rate;

    /**
     * DP asociados al RP
     */
    private $daily_packages;

    /**
     * DR asociados al RP
     */
    private $daily_rooms;

    /**
     * Edades de las criaturas que estan en la reserva
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children_age;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children_age = new ArrayCollection();
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
     * Set number_rooms
     *
     * @param integer $numberRooms
     * @return ReservationPack
     */
    public function setNumberRooms($numberRooms)
    {
        if(empty($numberRooms))
            throw new EntityValidationException('number_rooms',\get_class($this),'is_null');
        else if(!is_numeric($numberRooms))
            throw new EntityValidationException('number_rooms',\get_class($this),'illegal_value');
        else
            $this->number_rooms = $numberRooms;

        return $this;
    }

    /**
     * Get number_rooms
     *
     * @return integer 
     */
    public function getNumberRooms()
    {
        return $this->number_rooms;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return ReservationPack
     */
    public function setPrice($price)
    {
        /*if(empty($price))
            throw new EntityValidationException('is_null');
        else*/ if(!is_numeric($price))
            throw new EntityValidationException('price',\get_class($this),'illegal_value');
        else
            $this->price = round($price,4);

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set number_people (se conservó esta función para evitar conflictos con otras funcionalidades de la app)
     *
     * @param integer $numberPeople
     * @throws EntityValidationException
     * @return ReservationPack
     */
    public function setNumberPeople($numberPeople)
    {
        if(empty($numberPeople))
            throw new EntityValidationException('number_people',\get_class($this),'is_null');
        else if(!is_numeric($numberPeople))
            throw new EntityValidationException('number_people',\get_class($this),'illegal_value');
        else
        $this->number_adults = $numberPeople;

        return $this;
    }

    /**
     * Get number_people (se conservó esta función para evitar conflictos con otras funcionalidades de la app)
     *
     * @return integer 
     */
    public function getNumberPeople()
    {
        return $this->number_adults;
    }

    /**
     * Set reservation_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\Reservation $reservationId
     * @return ReservationPack
     */
    public function setReservationId(\Navicu\Core\Domain\Model\Entity\Reservation $reservationId)
    {
        $this->reservation_id = $reservationId;

        return $this;
    }

    /**
     * Get reservation_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\Reservation 
     */
    public function getReservationId()
    {
        return $this->reservation_id;
    }

    /**
     * Set pack_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\Pack $packId
     * @return ReservationPack
     */
    public function setPackId(\Navicu\Core\Domain\Model\Entity\Pack $packId)
    {
        $this->pack_id = $packId;

        return $this;
    }

    /**
     * Get pack_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\Pack 
     */
    public function getPackId()
    {
        return $this->pack_id;
    }

    /**
     * Set property_cancellation_policy_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy $propertyCancellationPolicyId
     * @return ReservationPack
     */
    public function setPropertyCancellationPolicyId(\Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy $propertyCancellationPolicyId)
    {
        $this->property_cancellation_policy_id = $propertyCancellationPolicyId;

        return $this;
    }

    /**
     * Get property_cancellation_policy_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy
     */
    public function getPropertyCancellationPolicyId()
    {
        return $this->property_cancellation_policy_id;
    }

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Set bedroom_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\BedRoom $bedroomId
     * @return ReservationPack
     */
    public function setBedroomId(\Navicu\Core\Domain\Model\Entity\BedRoom $bedroomId = null)
    {
        $this->bedroom_id = null;

        return $this;
    }

    // No se seguira utilizando (actualizado 05/04/2017)
    /**
     * Get bedroom_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\BedRoom
     */
    public function getBedroomId()
    {
        // devolvemos el array creado de bedroom
        return $this->bedroom;
    }

    /**
     * Set bedroom
     *
     * @param array $bedroom
     * @return ReservationPack
     */
    public function setBedroom($bedroom)
    {
        $this->bedroom = $bedroom;

        return $this;
    }

    /**
     * Get bedroom
     *
     * @return array 
     */
    public function getBedroom()
    {
        return $this->bedroom;
    }

    /**
     * Set cancellation_policy
     *
     * @param array $cancellationPolicy
     * @return ReservationPack
     */
    public function setCancellationPolicy($cancellationPolicy)
    {
        $this->cancellation_policy = $cancellationPolicy;

        return $this;
    }

    /**
     * Get cancellation_policy
     *
     * @return array 
     */
    public function getCancellationPolicy()
    {
        return $this->cancellation_policy;
    }

    /**
     * Set type_pack
     *
     * @param \Navicu\Core\Domain\Model\Entity\Category $typePack
     * @return ReservationPack
     */
    public function setTypePack(\Navicu\Core\Domain\Model\Entity\Category $typePack = null)
    {
        $this->type_pack = $typePack;

        return $this;
    }

    /**
     * Get type_pack
     *
     * @return \Navicu\Core\Domain\Model\Entity\Category 
     */
    public function getTypePack()
    {
        return $this->type_pack;
    }

    /**
     * Set type_room
     *
     * @param \Navicu\Core\Domain\Model\Entity\RoomType $typeRoom
     * @return ReservationPack
     */
    public function setTypeRoom(\Navicu\Core\Domain\Model\Entity\RoomType $typeRoom = null)
    {
        $this->type_room = $typeRoom;

        return $this;
    }

    /**
     * Get type_room
     *
     * @return \Navicu\Core\Domain\Model\Entity\RoomType 
     */
    public function getTypeRoom()
    {
        return $this->type_room;
    }

    /**
     * Set type_cancellation_policy
     *
     * @param \Navicu\Core\Domain\Model\Entity\Category $typeCancellationPolicy
     * @return ReservationPack
     */
    public function setTypeCancellationPolicy(\Navicu\Core\Domain\Model\Entity\Category $typeCancellationPolicy = null)
    {
        $this->type_cancellation_policy = $typeCancellationPolicy;

        return $this;
    }

    /**
     * Get type_cancellation_policy
     *
     * @return \Navicu\Core\Domain\Model\Entity\Category 
     */
    public function getTypeCancellationPolicy()
    {
        return $this->type_cancellation_policy;
    }

    /**
     * retorna el nombre de la habitación reservada basada en su tipó
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 02-02-2016
     *
     * @return string
     */
    public function getRoomName()
    {
        $parent = $this->type_room->getParent();
        return ( !empty($parent) ? $parent->getTitle().' - ' : '' ).$this->type_room->getTitle();
    }

    /**
     * Set number_adults
     *
     * @param integer $numberAdults
     * @return ReservationPack
     */
    public function setNumberAdults($numberAdults)
    {
        $this->number_adults = $numberAdults;

        return $this;
    }

    /**
     * Get number_adults
     *
     * @return integer 
     */
    public function getNumberAdults()
    {
        return $this->number_adults;
    }

    /**
     * Set number_kids
     *
     * @param integer $numberKids
     * @return ReservationPack
     */
    public function setNumberKids($numberKids)
    {
        $this->number_kids = $numberKids;

        return $this;
    }

    /**
     * Get number_kids
     *
     * @return integer 
     */
    public function getNumberKids()
    {
        return $this->number_kids;
    }

    public function toArray()
    {
        return [
            'number_rooms' => isset($this->number_rooms) ? $this->number_rooms : null,
            'price' => isset($this->price) ? $this->price : null,
            'bedroom' => isset($this->bedroom) ? $this->bedroom : null,
            'cancellation_policy' => isset($this->cancellation_policy) ? $this->cancellation_policy : null,
            'type_pack' => isset($this->type_pack) ? $this->type_pack->getTitle() : null,
            'type_room' => isset($this->type_room) ? $this->type_room->getTitle() : null,
            'type_cancellation_policy' => isset($this->type_cancellation_policy) ? $this->type_cancellation_policy->getTitle() : null,
            'number_adults' => isset($this->number_adults) ? $this->number_adults : null,
            'number_kids' => isset($this->number_kids) ? $this->number_kids : null,
        ];
    }

    public function calculateNetRate()
    {
        if (empty($this->net_rate)) {
            $this->net_rate = 0;
            $dr = $this->reservation_id->getDiscountRate();
            $dr = empty($dr) ?
                $this->reservation_id->getPropertyId()->getDiscountRate() :
                $dr;
            if ($this->price)
                $this->net_rate = $this->price * (1 - $dr);
        }
    }

    public function getNetRate()
    {
        $this->calculateNetRate();
        return $this->net_rate;
    }

    public function setNetRate($nr)
    {
        $this->net_rate = $nr;

        return $this;
    }

    public function getDailyPackages()
    {
        return $this->daily_packages;
    }

    public function setDailyPackages($dp)
    {
        $this->daily_packages = $dp;

        return $this;
    }

    public function getDailyRooms()
    {
        return $this->daily_rooms;
    }

    public function setDailyRooms($dr)
    {
        $this->daily_rooms = $dr;

        return $this;
    }

    /**
     * Add children_age
     *
     * @param \Navicu\Core\Domain\Model\Entity\ChildrenAge $childrenAge
     * @return ReservationPack
     */
    public function addChildrenAge(\Navicu\Core\Domain\Model\Entity\ChildrenAge $childrenAge)
    {
        $this->children_age[] = $childrenAge;

        return $this;
    }

    /**
     * Remove children_age
     *
     * @param \Navicu\Core\Domain\Model\Entity\ChildrenAge $childrenAge
     */
    public function removeChildrenAge(\Navicu\Core\Domain\Model\Entity\ChildrenAge $childrenAge)
    {
        $this->children_age->removeElement($childrenAge);
    }

    /**
     * Get children_age
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenAge()
    {
        return $this->children_age;
    }
}
