<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clase encargada de guardar valores necesarios de muchas tablas sin relacion a ninguna de ellas
 * Ej: Iva
 *
 * NvcGlobal
 */
class NvcGlobal
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Este atributo esta encargado de indicar la clave unica por la cual se haran las busquedas
     * Ej: iva
     * @var string
     */
    private $name;

    /**
     * Este atributo esta encargado de indicar el valor de la clave unica
     * Ej: 0.12
     * @var string
     */
    private $value;

    /**
     * Este parametro indica una breve descripcion de la clave
     * Ej: impuesto al valor agregado
     * @var string
     */
    private $description;

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
     * @return NvcGlobal
     */
    public function setName($name)
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
     * Set value
     *
     * @param string $value
     * @return NvcGlobal
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return NvcGlobal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
