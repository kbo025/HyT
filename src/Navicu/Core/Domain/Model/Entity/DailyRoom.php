<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Adapter\CoreValidator;

/**
 * Clase DailyRoom.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los cambios diarios de una Habitación.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DailyRoom
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha de los Cambios Diarios de la Habitación.
     * 
     * @var \DateTime
     */
    protected $date;

    /**
     * Esta propiedad es usada para saber si la disponibilidad Diaria esta creada con todos
     * sus valores.
     * 
     * @var Boolean
     */
    protected $is_completed;

    /**
     * Esta propiedad es usada para interactuar con la disponibilidad de los Cambios Diarios de la Habitación.
     * 
     * @var Integer
     */
    protected $availability;

    /**
     * Esta propiedad es usada para interactuar con la restricción que indica en cuantos días previos a la reserva
     * no se puede reservar la Habitación.
     * 
     * @var Integer
     */
    protected $cut_off;

    /**
     * Esta propiedad es usada como restricción para que la Habitación no salga a la venta en determinado
     * dia.
     * 
     * @var Boolean
     */
    protected $stop_sell;

    /**
     * Esta propiedad es usada para interactuar con el minimo de noche de los Cambios Diarios de la Habitación.
     * 
     * @var Integer
     */
    protected $min_night;

    /**
     * Esta propiedad es usada para interactuar con el maximo de noche de los Cambios Diarios de la Habitación.
     * 
     * @var Integer
     */
    protected $max_night;

    /**
     * Esta propiedad es usada para interactuar con la Habitación de los Cambios Diarios de la Habitación.
     * 
     * @var Room Type Object
     */
    protected $room;

    /**
     * @var \DateTime ultima modificación del daily
     */
    private $last_modified;

    /**
     * Constructor de la entidad asignando valores por defecto
     */
    public function __construct()
    {
        $this->min_night = 1;
        $this->max_night = 365;
        $this->cut_off = 0;
        $this->stop_sell = false;
        $this->is_completed = false;
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
     * Set date
     *
     * @param \DateTime $date
     * @return DailyRoom
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
     * Set is_completed
     *
     * @param boolean $isCompleted
     * @return DailyRoom
     */
    public function setIsCompleted($isCompleted)
    {
        $this->is_completed = $isCompleted;

        return $this;
    }

    /**
     * Get is_completed
     *
     * @return boolean 
     */
    public function getIsCompleted()
    {
        return $this->is_completed;
    }

    /**
     * Set availability
     *
     * @param integer $availability
     * @return DailyRoom
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return integer 
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set cut_off
     *
     * @param integer $cutOff
     * @return DailyRoom
     */
    public function setCutOff($cutOff)
    {
        $this->cut_off = $cutOff;

        return $this;
    }

    /**
     * Get cut_off
     *
     * @return integer 
     */
    public function getCutOff()
    {
        return $this->cut_off;
    }

    /**
     * Set stop_sell
     *
     * @param boolean $stopSell
     * @return DailyRoom
     */
    public function setStopSell($stopSell)
    {
        $this->stop_sell = $stopSell;

        return $this;
    }

    /**
     * Get stop_sell
     *
     * @return boolean 
     */
    public function getStopSell()
    {
        return $this->stop_sell;
    }

    /**
     * Set min_night
     *
     * @param integer $minNight
     * @return DailyRoom
     */
    public function setMinNight($minNight)
    {
        $this->min_night = $minNight;

        return $this;
    }

    /**
     * Get min_night
     *
     * @return integer 
     */
    public function getMinNight()
    {
        return $this->min_night;
    }

    /**
     * Set max_night
     *
     * @param integer $maxNight
     * @return DailyRoom
     */
    public function setMaxNight($maxNight)
    {
        $this->max_night = $maxNight;

        return $this;
    }

    /**
     * Get max_night
     *
     * @return integer 
     */
    public function getMaxNight()
    {
        return $this->max_night;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return DailyRoom
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
     * Función para el manejo del slug de un establecimiento desde el dailyRoom.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return String
     */
    public function getSlug()
    {
        return $this->room->getProperty()->getSlug();
    }

    /**
     * coleccion de bloqueos de disponibilidad
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lockeds;

    /**
     * La función actualiza los datos de un DailyRoom, dado un array ($data)
     * con los valores asociados al DailyRoom
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Array $data
     * @return void
     */
    public function updateObject($data)
    {
        if (isset($data['date']) and $this->date == null) {
            if (gettype($data['date']) != "object") {
                $this->date = (new \DateTime($data['date']));
            } else {
                $this->date = $data['date'];
            }
        }

        if (isset($data['availability']))
           $this->availability = $data['availability'];

        if (isset($data['minNight']))
            $this->min_night = $data['minNight'];

        if (isset($data['maxNight']))
            $this->max_night = $data['maxNight'];

        if (isset($data['cutOff']))
            $this->cut_off = $data['cutOff'];

        if (isset($data['stopSell']))
            $this->stop_sell = (boolean)$data['stopSell'];

        if (isset($data['isCompleted'])) 
            $this->is_completed = $data['isCompleted'];

        return $this;
    }

    /**
     * Esta función es usada para actualizar ciertas propiedades de
     * un daily bajo los criterios de una habitación "dailyRoom". Dichos
     * criterios son: Valores por defectos cuando la minNight o maxNight
     * vienen nulo y Valores por defecto cuando la disponibilidad de
     * un dailyPack viene nula.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Array $arrayDaily
     * @return Null
     */
    public function updateRestriction($objDailyPackages, $lowAvailability = false)
    {
        $response = $lowAvailability == false ? array($this) : array();

        if (!$objDailyPackages)
            return $response;

        $roomAvailability = $this->availability;
        $countA = 0; //contador de disponibilidad de servicio.
        
		for ($dp = 0; $dp < count($objDailyPackages); $dp++) {
            $ban = 0;
            $packAvailability = $objDailyPackages[$dp]->getSpecificAvailability();

            if (is_null($packAvailability) or $lowAvailability == true or ($packAvailability > $roomAvailability)) {
                $objDailyPackages[$dp]->setSpecificAvailability($this->availability);
                $ban = 1;
            }

            if ($objDailyPackages[$dp]->getMinNight() < $this->min_night ||
                $objDailyPackages[$dp]->getMaxNight() > $this->max_night ) {

                $objDailyPackages[$dp]->setMinNight($this->min_night);
                $objDailyPackages[$dp]->setMaxNight($this->max_night);
                $ban = 1;
            }

            if ($ban == 1) {
                array_push($response, $objDailyPackages[$dp]);
            }
            $countA += $objDailyPackages[$dp]->getSpecificAvailability();
        }

        if ($countA < $roomAvailability) {
            $response = array_merge(array($this), $this->updateRestriction($objDailyPackages, true));
            return $response;
        }

        return $response;
    }

    /**
     * Función para el manejo la propiedad is_completed,
     * saber si esta o no completo el dailyRoom.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return void
     */
    public function isCompleted() {
        $error = CoreValidator::getValidator($this, array(
			'isNull'
        ));

		if (count($error) > 0) {
			$this->setIsCompleted(false);
		} else {
			$this->setIsCompleted(true);
        }

        return $this;
    }

    /**
     * Get date
     *
     * @return String
     */
    public function getStringDate()
    {
        return is_object($this->date) ? $this->date->format('Y-m-d') : $this->date;
    }

    /**
     * Función para el manejo de la información por
     * medio de un arreglo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function getArray()
    {
        $data['idDailyRoom'] = $this->id;
        $data['date'] = $this->getStringDate();
        $data['availability'] = $this->availability;
        $data['idRoom'] = $this->room->getId();
        $data['minNight'] = $this->min_night;
        $data['maxNight'] = $this->max_night;
        $data['cutOff'] = $this->cut_off;
        $data['stopSell'] = $this->stop_sell;
        $data['isCompleted'] = $this->is_completed;

        return $data;
    }

    /**
     * Set last_modified
     *
     * @param \DateTime $lastModified
     * @return DailyRoom
     */
    public function setLastModified()
    {
        $this->last_modified = new \DateTime();

        return $this;
    }

    /**
     * Get last_modified
     *
     * @return \DateTime 
     */
    public function getLastModified()
    {
        return $this->last_modified;
    }

    /**
     * Add lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     * @return DailyRoom
     */
    public function addLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds[] = $lockeds;

        return $this;
    }

    /**
     * Remove lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     */
    public function removeLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds->removeElement($lockeds);
    }

    /**
     * Get lockeds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLockeds()
    {
        return $this->lockeds;
    }

    /**
     * calcula la disponibilidad bloqueada del dp
     * @param null $idSession
     * @return int
     */
    public function getLockeAvailability($idSession = [])
    {
        $locked = 0;
        $now = strtotime('now');
        foreach ($this->lockeds as $lock) {
            if (($lock->getExpiry() > $now) && (!in_array($lock->getIdSession(),$idSession)))
                $locked = $locked + $lock->getNumberPackages();
        }
        return $locked;
    }

    /**
     * indica si el dp tiene disponibilidad bloqueada
     */
    public function isLocked()
    {
        return $this->getLockeAvailability() > 0;
    }
}
