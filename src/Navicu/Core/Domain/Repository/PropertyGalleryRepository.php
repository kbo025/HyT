<?php 

namespace Navicu\Core\Domain\Repository;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;


/**
* 	Interfaz de la PropertyGalleryRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 23/11/2015
*/
interface PropertyGalleryRepository
{

    /**
     * Almacena en BD toda la informacion referente al Room
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  PropertyGallery $propertyGallery
     */
    public function save(PropertyGallery $propertyGallery);

    /**
     * La siguiente función se encarga de consultar una galería dado
     * un slug y un id de la galería
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $propertyGId
     * @version 20/11/2015
     *
     */
    public function findOneBySlugGallery($slug, $propertyGId);
}