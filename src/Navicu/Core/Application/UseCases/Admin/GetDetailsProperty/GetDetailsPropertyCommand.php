<?php
namespace Navicu\Core\Application\UseCases\Admin\GetDetailsProperty;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso GetDetailsProperty (Obtener detalles del establecimiento)
 *
 * Class GetDetailsPropertyCommand
 * @package Navicu\Core\Application\UseCases\Admin\GetDetailsProperty
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 26/10/2015
 */
class GetDetailsPropertyCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;
    private $numberResult;
    private $search;
    private $orderBy;
    private $orderType;
    private $page;

    /**
     * Constructor de la clase
     * @param $dataInput
     */
    public function __construct($dataInput)
    {
        $this->slug = isset($dataInput['slug']) ? $dataInput['slug'] : null;;
        $this->search = isset($dataInput['search']) ? $dataInput['search'] : null;
        $this->orderBy = isset($dataInput['orderBy']) ? $dataInput['orderBy'] : null;
        $this->orderType = isset($dataInput['orderType']) ? $dataInput['orderType'] : null;
        $this->page = isset($dataInput['page']) ? $dataInput['page'] : 1;
        $this->numberResult = 50;
    }

    public function getRequest()
    {
        return [
            'slug' => $this->slug,
            'search' => $this->search,
            'order_by' => $this->orderBy,
            'order_type' => $this->orderType,
            'page' => $this->page,
            'number_result' => $this->numberResult
        ];
    }

    /**
     * Método get del atributo slug
     * @return Slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Método set del atributor slug
     * @param $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}