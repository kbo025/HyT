<?php

namespace Navicu\Core\Domain\Model\Entity;

/**
 * LogsUser
 */
class LogsUser
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Fecha y hora en la cual se esta realizando la modificacion
     *
     * @var \DateTime
     */
    private $dateTime;

    /**
     * Accion que se esta ejecutando sobre la entidad
     *
     * @var string
     */
    private $action;

    /**
     * Nombre de la entidad que se esta manipulando
     *
     * @var string
     */
    private $resource;

    /**
     * Identificador de la entidada que se esta manipulando
     *
     * @var integer
     */
    private $idResource;

    /**
     * Parametros relevantes para describir que se le hizo a la entidad
     *
     * @var array
     */
    private $description;

    /**
     * Usuario que realizo la modificacion sobre la entidad
     *
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    private $user;

    /**
     * Property que sufrio la modificacion
     *
     * @var \Navicu\Core\Domain\Model\Entity\Property
     */
    private $property;

    public function __construct()
    {
        $this->dateTime = new \DateTime("now");
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
     * Asignar la fecha y hora en la cual se modifico una entidad
     *
     * @param \DateTime $dateTime
     * @return LogsUser
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Obtener fecha y hora en la cual se realizo la modificacion a la entidad
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Asignar la accion que se llevo a cabo del CRUD
     *
     * @param string $action
     * @return LogsUser
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Obtener la accion que se llevo a cabo del CRUD
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Asignar la entidad afectada
     *
     * @param string $resource
     * @return LogsUser
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Obtener la Entidad que fue afectada
     *
     * @return string 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Asignar el identificador de la entidad que ha sido modificada
     *
     * Set idResource
     *
     * @param integer $idResource
     * @return LogsUser
     */
    public function setIdResource($idResource)
    {
        $this->idResource = $idResource;

        return $this;
    }

    /**
     * Obtener el identificador de la entidad que fue modificada
     *
     * Get idResource
     *
     * @return integer 
     */
    public function getIdResource()
    {
        return $this->idResource;
    }

    /**
     * Asignar cambios detallados sobre los valores modificados
     *
     * Set description
     *
     * @param array $description
     * @return LogsUser
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Obtener imformacion detallada del cambio realizado
     *
     * Get description
     *
     * @return array
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Metodo para asignar el usuario que realizo el cambio
     *
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return LogsUser
     */
    public function setUser(\Navicu\InfrastructureBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Metodo para obtener el usuario que realizo el cambio
     *
     * Get user
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Metodo para asignar el property al cual se le hizo la midificacion
     *
     * Set property
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $property
     * @return LogsUser
     */
    public function setProperty(\Navicu\Core\Domain\Model\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Metodo para obtener el property al cual se le hizo la midificacion
     *
     * Get property
     *
     * @return \Navicu\Core\Domain\Model\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
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
        $data['action'] = $this->action;
        $data['resource'] = $this->resource;
        $data['idResource'] = $this->idResource;
        $data['description'] = $this->description;
        $data['property'] = $this->property->getName();
        return $data;
    }

    /**
     * Función para la creación de un LogsUser a partir de un
     * arreglo con información basica.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param array $data, array con los parametros a ser guardados
     * @return Array
     */
    public function updateObject($data)
    {
        $this->action = $data['action'];
        $this->resource = $data['resource'];
        $this->idResource = $data['idResource'];
        $this->description = $data['description'];
        $this->user = $data['user'];
        $this->property = $data['property'];

        return $this;
    }
}
