<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\SetRecruitToProperty;
use Navicu\Core\Application\Contract\Command;


class SetRecruitToPropertyCommand implements Command
{
    /**
     * @var integer id del comercial a asignar
     */
    protected $nvcProfileId;

    /**
     * @var integer id del establecimiento
     */
    protected $propertyId;

    /**
     * @var integer tipo de establecimiento (TempOwner o Property)
     */
    protected $propertyType;

    function __construct($data)
    {
        $this->nvcProfileId = isset($data['nvcProfileId']) ? $data['nvcProfileId'] : null;
        // 1 para property, 0 para tempProperty
        $this->propertyId = isset($data['propertyId']) ? $data['propertyId'] : null;
        $this->propertyType = isset($data['propertyType']) ? $data['propertyType'] : null;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            "nvcProfileId" => $this->nvcProfileId,
            "propertyId" => $this->propertyId,
            "propertyType" => $this->propertyType,
        ];
    }
}