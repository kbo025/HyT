<?php 

namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;

/**
* 	Interfaz de la PropertyImagesGalleryRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 26-05-2015
*/
interface PropertyImagesGalleryRepository
{
    /**
     * Almacena en BD toda la informacion referente al PropertyImagesGallery
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  PropertyImagesGallery
     */
    public function save(PropertyImagesGallery $propertyImage);

    /**
     * La siguiente función se encarga de consulta una galería
     * de un establecimiento dado un slug, una galeríua y el id de la imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idGallery
     * @param $idImage
     * @return mixed
     * @version 11/01/2016
     */
    public function findOneBySlugGalleryImage($slug, $idService, $idImage);

    /**
     * La siguiente función se encarga de eliminar de la BD
     * un objecto PropertyImagesGallery
     *
     * @param PropertyImagesGallery $propertyImagesGallery
     */
    public function delete(PropertyImagesGallery $propertyImagesGallery);

    /**
     * La siguiente función retorna una entidad PropertyImagesGallery
     * dado un slug y el id de una imagen (Document)
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idImage
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @version 12/01/2016
     */
    public function findOneBySlugImage($slug, $idImage);
}