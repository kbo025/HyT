<?php 

namespace Navicu\Core\Domain\Repository;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;

/**
*   Interfaz de la PropertyFavoriteImagesRepository
*
*   @author Freddy Contreras <freddycontreras3@gmail.com>
*   @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*   @version 26-05-2015
*/
interface PropertyFavoriteImagesRepository
{
    /**
     * Almacena en BD toda la informacion referente al PropertyFavoriteImages
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  PropertyFavoriteImages
     */
    public function save(PropertyFavoriteImages $favoriteImage);

    /**
     * La siguiente función se encarga de eliminar
     * un objecto de tipo PropertyFavoriteImages
     *
     * @param PropertyFavoriteImages $favoriteImage
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 12/01/2016
     */
    public function delete(PropertyFavoriteImages $favoriteImage);

    /**
     * La siguiente función retorna un PropertyFavoriteImage
     * dado un slug y el id de una imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idImage
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugImage($slug, $idImage);

    /**
     * La siguiente función retorna todas las imagenes
     * favoritas execto el id de imagen del favorito
     *
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idFavoriteImage
     * @return array
     * @version 12/01/2015
     */
    public function findBySlugNotEqualId($slug, $idFavoriteImage);

    /**
     * La siguiente función retorna un PropertyFavoriteImage
     * dado un slug y el id de un PropertyFavoriteImages
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idFavorite
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugId($slug, $idFavorite);
}