<?php
namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase FoodType representa todos los tipos de comida servidas dentro del dominio de Navicu
 * 
 * Se define una clase y una serie de propiedades para el manejo de los tipos de comida
 * del restaurante.
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class FoodType
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el nombre del tipo de comida.
     *
     * @var string
     */
    protected $title;


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
     * Set title
     *
     * @param string $title
     *
     * @return FoodType
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
