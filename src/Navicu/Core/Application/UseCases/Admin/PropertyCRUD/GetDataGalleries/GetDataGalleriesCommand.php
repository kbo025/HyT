<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\GetDataGalleries;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de GetDataGalleries
 *
 * Class GetDataGalleriesCommand
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\GetDataGalleries
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class GetDataGalleriesCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * Constructor de la clase
     * @param $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug
            );
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