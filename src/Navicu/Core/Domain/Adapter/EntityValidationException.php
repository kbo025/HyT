<?php
namespace Navicu\Core\Domain\Adapter;

/**
 * clase creada para manejar las excepciones lanzadas por las entidades cuando hay errores de validcacion
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 16-10-2015
 */
class EntityValidationException extends \Exception
{
    /**
     * indica el atributo que generó la excepcion
     *
     * @var String
     */
    protected $att;

    /**
     * indica el nombre de la clase que generó la excepcion
     *
     * @var String
     */
    protected $entity;

    /**
    *   clase constructora
    */
    public function __construct($att=null,$entity=null,$msj=null,$code=null) {
        $this->att = $att;
        $this->entity = $entity;
        parent::__construct($msj,$code);
    }

    /**
     * devuelve el nombre del atributo que generó la excepcion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16-10-2015
     * @return String $att
     */
    public function getAttribute()
    {
        return $this->att;
    }

    /**
     * devuelve el nombre de la entidad que generó la excepcion
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 16-10-2015
     * @return String $att
     */
    public function getEntity()
    {
        return $this->entity;
    }
}