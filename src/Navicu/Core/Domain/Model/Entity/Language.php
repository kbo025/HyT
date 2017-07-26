<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Representa todos los idiomas usados dentro del dominio de Navicu
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 */
class Language
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var string
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el nombre del idioma.
     *
     * @var string
     */
    protected $native;

    /**
     * @var ArrayCollection
     */
    private $property_language;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->property_language = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Language
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set native
     *
     * @param string $native
     *
     * @return Language
     */
    public function setNative($native)
    {
        $this->native = $native;

        return $this;
    }

    /**
     * Get native
     *
     * @return string
     */
    public function getNative()
    {
        return $this->native;
    }

    /**
     * Add property_language
     *
     * @param Property $propertyLanguage
     *
     * @return Language
     */
    public function addPropertyLanguage(Property $propertyLanguage)
    {
        $this->property_language[] = $propertyLanguage;

        return $this;
    }

    /**
     * Remove property_language
     *
     * @param Property $propertyLanguage
     */
    public function removePropertyLanguage(Property $propertyLanguage)
    {
        $this->property_language->removeElement($propertyLanguage);
    }

    /**
     * Get property_language
     *
     * @return ArrayCollection
     */
    public function getPropertyLanguage()
    {
        return $this->property_language;
    }
}
