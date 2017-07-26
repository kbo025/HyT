<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;

/**
 * Clase ContactPerson.
 *
 * entidad que modela las personas que fungen como contactos entre un establecimiento y navicu
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class ContactPerson
{
    
    /**
     * identificador de la entidad
     *
     * @var integer
     */
    private $id;

    /**
     * nombre del contacto
     *
     * @var string
     */
    private $name;

    /**
     * cargo que desempeña el contacto
     *
     * @var integer
     */
    private $charge;

    /**
     * tlf de contacto
     *
     * @var string
     */
    private $phone;

    /**
     * fax de contacto
     *
     * @var string
     */
    private $fax;

    /**
     * direccion de correo electronico del contacto
     *
     * @var string
     */
    private $email;

    /**
     * relacion con el establecimiento al quer esta relacionado el contacto
     *
     * @var Property
     */
    private $property;

    /**
    * array que contienen errores de validacion de la entidad en un momento determinado
    * @var array
    */
    private $errors;

    /**
     * area de contacto, se definen de la siguiente manera
     *
     * 0 = Contabilidad
     * 1 = Reservas
     * 2 = Gerencia
     * 3 = Operaciones
     * 4 = Atención al cliente
     *
     * @var integer
     */
    private $type;

    /**
     * indica si el contacto recibe emails relacionados con eventos de reservaciones
     *
     * @var boolean
     */
    private $email_reservation_receiver = false;

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
     * Set name
     *
     * @param string $name
     *
     * @return ContactPerson
     */
    public function setName($name=null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set charge
     *
     * @param integer $charge
     *
     * @return ContactPerson
     */
    public function setCharge($charge=null)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge
     *
     * @return integer 
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ContactPerson
     */
    public function setPhone(Phone $phone=null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return ContactPerson
     */
    public function setFax(Phone $fax=null)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ContactPerson
     */
    public function setEmail(EmailAddress $email=null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return ContactPerson
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
     * Set type
     *
     * @param integer $type
     * @return ContactPerson
     */
    public function setType($type=null)
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
     * funcion que se ejecuta antes de persistir la entidad en la base de datos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     */
    public function prePersist()
    {
        if ($this->phone instanceof Phone) {
            $this->phone = $this->phone->toString();
        }
        if ($this->fax instanceof Phone) {
            $this->fax = $this->fax->toString();
        }
        if ($this->email instanceof EmailAddress) {
            $this->email = $this->email->toString();
        }
    }

    /**
     *  devuelve un array con los errores de validacion de la entidad
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * indica si la entidad se encuentra en un estado valido
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Boolean
     */
    public function validate()
    {
        $this->errors = array();
        if (empty($this->name)) {
            $this->errors['name'] = array('messsage'=>'Nombre no debe estar vacio');
        }
        if (!isset($this->type)) {
            $this->errors['type'][] = array('messsage'=>'Tipo no debe estar vacio');
        } else {
            if (!is_integer($this->type)) {
                $this->errors['type'][] = array('messsage'=>'Valor incorrecto para Tipo');
            }
        }
        if (empty($this->charge)) {
            $this->errors['charge'][] = array('messsage'=>'Cargo no debe estar vacio');
        }
        if (empty($this->phone)) {
            $this->errors['phone'][] = array('messsage'=>'Teléfono no debe estar vacio');
        }
        if (empty($this->email)) {
            $this->errors['email'][] = array('messsage'=>'Email no debe estar vacio');
        }
        return empty($this->errors);
    }

    /**
     * devuelve una representacion del objeto como array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @return Array
     */
    public function toArray()
    {
        $res = array();
        $res['id'] = isset($this->id) ? $this->id : null;
        $res['name'] = isset($this->name) ? $this->name : null;
        $res['charge'] = isset($this->charge) ? $this->charge : null;
        $res['type'] = isset($this->type) ? $this->type : null;
        $res['email_reservation_receiver'] = isset($this->email_reservation_receiver) ?
            $this->email_reservation_receiver :
            null;
        if(isset($this->phone)){
            $res['phone'] = ($this->phone instanceof Phone) ?
                $this->phone->toString() :
                $this->phone;
        }
        if (isset($this->fax)) {
            $res['fax'] = ($this->fax instanceof Phone) ?
                $this->fax->toString() :
                $this->fax;
        }
        if (isset($this->email)) {
            $res['email'] = ($this->email instanceof EmailAddress) ?
                $this->email->toString() :
                $this->email;
        }
        return $res;
    }

    /**
     * Set email_reservation_receiver
     *
     * @param boolean $emailReservationReceiver
     * @return ContactPerson
     */
    public function setEmailReservationReceiver($emailReservationReceiver)
    {
        $this->email_reservation_receiver = $emailReservationReceiver;

        return $this;
    }

    /**
     * Get email_reservation_receiver
     *
     * @return boolean 
     */
    public function getEmailReservationReceiver()
    {
        return $this->email_reservation_receiver;
    }
}
