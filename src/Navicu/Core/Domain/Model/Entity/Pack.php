<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Model\Entity\PackLinkage;
use Navicu\Core\Domain\Model\Entity\RoomPackLinkage;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Category;
use Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy;
use Navicu\Core\Domain\Model\Entity\DailyRoom;

/**
 * Clase Pack.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los servicios de una habitacion.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Pack
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;
    
    /**
     * Esta propiedad es usada para interactuar con los servicios diarios de un Servicio.
     *
     * @var DailyPack Type Object
     */
    protected $daily_packages;

    /**
     * Esta propiedad es usada para interactuar con los Linkage de un Servicio.
     * donde dicho servicio es el hijo.
     *
     * @var PackLinkage Type Object
     */
    protected $i_am_child;

    /**
     * Esta propiedad es usada para interactuar con los Linkage de un Servicio.
     * donde dicho servicio es el padre.
     *
     * @var PackLinkage Type Object
     */
    protected $i_am_parent;
    
    /**
     * Esta propiedad es usada para interactuar con el valor asignado en la lista de tipos de Servicio.
     * 
     * @var Category Type Object
     */
    protected $type;
    
    /**
     * Esta propiedad es usada para interactuar con la Habitación de un Servicio.
     * 
     * @var Room Type Object
     */
    protected $room;

    /**
     * Esta propiedad es usada para interactuar con los Linkage Room-Pack de un Servicio.
     * donde dicho Servicio interactua con su Habitación Padre.
     *
     * @var RoomPackLinkage Type Object
     */
    protected $child_room_pack;

    /**
     * Esta propiedad es usada para interactuar con las politicas de cancelación
     * asignada a este servicio.
     * 
     * @var PropertyCancellationPolicy Type Object
     */
    protected $pack_cancellation_policies;

    /**
     * Representa las reservaciones asociadas al pack
     *
     * @var ArrayCollection
     */
    protected $reservation_packages;

    /**
     * @var string
     */
    private $slug;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->pack_cancellation_policies = new ArrayCollection();
        $this->daily_packages = new ArrayCollection();
        $this->i_am_child = new ArrayCollection();
        $this->i_am_parent = new ArrayCollection();
        $this->child_room_pack = new ArrayCollection();
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
     * Add daily_packages
     *
     * @param DailyPack $dailyPackages
     * @return Pack
     */
    public function addDailyPackage(DailyPack $dailyPackages)
    {
        $this->daily_packages[] = $dailyPackages;

        return $this;
    }

    /**
     * Remove daily_packages
     *
     * @param DailyPack $dailyPackages
     */
    public function removeDailyPackage(DailyPack $dailyPackages)
    {
        $this->daily_packages->removeElement($dailyPackages);
    }

    /**
     * Get daily_packages
     *
     * @return ArrayCollection
     */
    public function getDailyPackages()
    {
        return $this->daily_packages;
    }

    /**
     * Add i_am_child
     *
     * @param PackLinkage $iAmChild
     * @return Pack
     */
    public function addIAmChild(PackLinkage $iAmChild)
    {
        $this->i_am_child[] = $iAmChild;

        return $this;
    }

    /**
     * Remove i_am_child
     *
     * @param PackLinkage $iAmChild
     */
    public function removeIAmChild(PackLinkage $iAmChild)
    {
        $this->i_am_child->removeElement($iAmChild);
    }

    /**
     * Get i_am_child
     *
     * @return ArrayCollection
     */
    public function getIAmChild()
    {
        return $this->i_am_child;
    }

    /**
     * Add i_am_parent
     *
     * @param PackLinkage $iAmParent
     * @return Pack
     */
    public function addIAmParent(PackLinkage $iAmParent)
    {
        $this->i_am_parent[] = $iAmParent;

        return $this;
    }

    /**
     * Remove i_am_parent
     *
     * @param PackLinkage $iAmParent
     */
    public function removeIAmParent(PackLinkage $iAmParent)
    {
        $this->i_am_parent->removeElement($iAmParent);
    }

    /**
     * Get i_am_parent
     *
     * @return ArrayCollection
     */
    public function getIAmParent()
    {
        return $this->i_am_parent;
    }

    /**
     * Add child_room_pack
     *
     * @param RoomPackLinkage $childRoomPack
     * @return Pack
     */
    public function addChildRoomPack(RoomPackLinkage $childRoomPack)
    {
        $this->child_room_pack[] = $childRoomPack;

        return $this;
    }

    /**
     * Remove child_room_pack
     *
     * @param RoomPackLinkage $childRoomPack
     */
    public function removeChildRoomPack(RoomPackLinkage $childRoomPack)
    {
        $this->child_room_pack->removeElement($childRoomPack);
    }

    /**
     * Get child_room_pack
     *
     * @return ArrayCollection
     */
    public function getChildRoomPack()
    {
        return $this->child_room_pack;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return Pack
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
     * Set type
     *
     * @param Category $type
     * @return Pack
     */
    public function setType(Category $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Category
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add pack_cancellation_policies
     *
     * @param PropertyCancellationPolicy $packCancellationPolicies
     * @return Pack
     */
    public function addPackCancellationPolicy(PropertyCancellationPolicy $packCancellationPolicies)
    {
        $this->pack_cancellation_policies[] = $packCancellationPolicies;

        return $this;
    }

    /**
     * Remove pack_cancellation_policies
     *
     * @param PropertyCancellationPolicy $packCancellationPolicies
     */
    public function removePackCancellationPolicy(PropertyCancellationPolicy $packCancellationPolicies)
    {
        $this->pack_cancellation_policies->removeElement($packCancellationPolicies);
    }

    /**
     * Get pack_cancellation_policies
     *
     * @return ArrayCollection
     */
    public function getPackCancellationPolicies()
    {
        return $this->pack_cancellation_policies;
    }

    /**
     * Add reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     * @return Pack
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

    
    /**
     * Función para la creación de un dailyRoom de una habitación
     * cuando uno de sus dailyPack esta siendo creado por primera vez.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Object $dailyPack
     * @return Array
     */
    public function createDailyByRoom($dailyPack)
    {
        $dailyRoom = new DailyRoom;
        $dailyRoom->setDate($dailyPack->getDate());
        $dailyRoom->setAvailability($dailyPack->getSpecificAvailability());
        $dailyRoom->setRoom($this->room);
        $dailyRoom->setMinNight($dailyPack->getMinNight());
        $dailyRoom->setMaxNight($dailyPack->getMaxNight());
        return $dailyRoom;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Room
     */
    public function setSlug()
    {
        $this->generateSlug();
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function generateSlug()
    {
        $slug = $this->getType()->getTitle();

        //reemplazando caracteres especiales por simples
        $slug = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $slug
        );

        $slug = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $slug
        );

        $slug = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $slug
        );

        $slug = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô','Õ','Ø'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O','O','O'),
            $slug
        );

        $slug = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $slug
        );

        $slug = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç','Š','š','Ž','ž','Ý','ý','ÿ'),
            array('n', 'N', 'c', 'C','S','s','Z','z','Y','y','y'),
            $slug
        );

        //convirtiendo todo a minusculas
        $slug = strtolower($slug);

        //eliminando el resto de los caracteres especiales
        $slug = preg_replace('/[^a-zA-Z0-9\s]/i','', $slug);

        //limpiando espacios en blancos en los extremos
        $slug = trim($slug);

        // Rellenamos espacios con guiones
        $slug = preg_replace('/\s+/', '-', $slug);

        $this->slug = $this->getRoom()->getSlug().'-'.$slug;
    }
}
