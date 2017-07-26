<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\SetCommercialToProperty;

use Navicu\Core\Application\Contract\Command;

/**
 * Class SetCommercialToPropertyCommand
 *
 * Asignación de un commercial a un establecimiento
 * en proceso de registro (TempOwner) o afiliado (Property)
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 04/04/2016
 */
class SetCommercialToPropertyCommand implements Command
{
    /**
     * @var integer id del comercial a asignar
     */
    protected $commercailId;

    /**
     * @var integer id del establecimiento
     */
    protected $propertyId;

    /**
     * @var integer tipo de establecimiento (TempOwner o Property)
     */
    protected $propertyType;

    /**
     * Constructor de la clase
     *
     */
    public function __construct($commercialId = null, $propertyId = null, $propertyType = null)
    {
        $this->commercailId = $commercialId;
        $this->propertyId = $propertyId;
        $this->propertyType = $propertyType;
    }

    public function getRequest()
    {
       return [
           'commercialId' => $this->commercailId,
           'propertyId' => $this->propertyId,
           'propertyType' => $this->propertyType
       ];
    }

    /**
     * @return int
     */
    public function getCommercailId()
    {
        return $this->commercailId;
    }

    /**
     * @param int $commercailId
     */
    public function setCommercailId($commercailId)
    {
        $this->commercailId = $commercailId;
    }

    /**
     * @return int
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * @param int $propertyId
     */
    public function setPropertyId($propertyId)
    {
        $this->propertyId = $propertyId;
    }

    /**
     * @return int
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @param int $propertyType
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;
    }
}