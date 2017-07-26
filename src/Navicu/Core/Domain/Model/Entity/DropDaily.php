<?php
namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase DropDaily.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de los
 * elementos Diarios eliminados en el inventarios de una habitaciòn y servicios.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class DropDaily
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el id del elemento diario eliminado.
     *
     * @var Integer
     */
    protected $daily_id;

    /**
     * Esta propiedad es usada para interactuar con el tipo de elemento diario eliminado.
     * DailyPack
     * DailyRoom
     *
     * @var string
     */
    protected $type;

    /**
     * Esta propiedad es usada para interactuar con el id del padre del elemento eliminado:
     * Room o Pack.
     *
     * @var Integer
     */
    protected $parent_id;

    /**
     * Constructor de la entidad asignando valores por defecto
     */
    public function __construct()
    {
        $this->last_modified = new \DateTime();
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
     * Set daily_id
     *
     * @param integer $dailyId
     * @return DropDaily
     */
    public function setDailyId($dailyId)
    {
        $this->daily_id = $dailyId;

        return $this;
    }

    /**
     * Get daily_id
     *
     * @return integer 
     */
    public function getDailyId()
    {
        return $this->daily_id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return DropDaily
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parent_id
     *
     * @param integer $parentId
     * @return DropDaily
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parent_id;
    }
}
