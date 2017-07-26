<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Clase Notification.
 *
 * Se define una clase y una serie de propiedades necesarias
 * para el manejo de las notificaciones de usuarios.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class Notification
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha de creación
     * de una notificación
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Esta propiedad es usada para saber si una notificación fue vista
     * o no.
     *
     * @var Boolean
     */
    protected $view;

    /**
     * Esta propiedad es usada para interactuar con el tipo de notificación.
     *
     * @var Integer
     */
    protected $type;

    /**
     * Esta propiedad es usada para interactuar con el mensaje que lleva
     * la notificación.
     *
     * @var String
     */
    protected $message;

    /**
     * Esta propiedad es usada para contener información adicional de
     * una notificación.
     *
     * @var JsonArray
     */
    protected $data;

    /**
     * Esta propiedad es usada para interactuar con el usuario que envia
     * la notificación.
     *
     * @var Object User
     */
    protected $sender;

    /**
     * Esta propiedad es usada para interactuar con el usuario que recibe
     * la notificación.
     *
     * @var Object User
     */
    protected $reciver;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->view = false;
        $this->date = new \DateTime();
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
     * @return Notification
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
     * Set view
     *
     * @param boolean $view
     * @return Notification
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return boolean 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Notification
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set sender
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $sender
     * @return Notification
     */
    public function setSender(\Navicu\InfrastructureBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set reciver
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $reciver
     * @return Notification
     */
    public function setReciver(\Navicu\InfrastructureBundle\Entity\User $reciver = null)
    {
        $this->reciver = $reciver;

        return $this;
    }

    /**
     * Get reciver
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getReciver()
    {
        return $this->reciver;
    }

    /**
     * La función actualiza los datos de una Notificación,
     * dado un array ($data) con los valores asociados a
     * la Notificación.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return void
     */ 
    public function updateObject($data)
    {
        $this->message = $data["message"];
        $this->setReciver($data["reciver"]);
        $this->setSender(isset($data["sender"]) ? $data["sender"] : null);
        $this->data = isset($data["data"]) ? $data["data"] : null;
        $this->type = $data["type"];
        $this->view = isset($data["view"]) ? $data["view"] : false;
        
        return $this;
    }


    /**
     * Función para el manejo de la información de
     * una notificación por medio de un arreglo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function toArray()
    {
        $data["message"] = $this->message;
        $data["sender"] = $this->getSender() ? $this->getSender()->getUserName() : "Navicu";
        $data["data"] = empty($this->data) ? null : $this->data;
        $data["type"] = $this->type;
        $data["view"] = $this->view;
        $data["date"] = $this->date->format("Y-m-d H:i:s");
        
        return $data;
    }
}
