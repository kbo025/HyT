<?php 

namespace Navicu\Core\Domain\Repository;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;

/**
* 	Interfaz de la RoomImagesGalleryRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 26-05-2015
*/
interface RoomImagesGalleryRepository
{
    /**
     * Almacena en BD toda la informacion referente al RoomImagesGallery
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  RoomImagesGallery
     */
    public function save(RoomImagesGallery $roomImage);

    /**
     * La siguiente función se encarga de consulta una galería
     * de una habitación dado un slug, una habitación y el id de la imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idRoom
     * @param $idImage
     * @return mixed
     * @version 23/11/2015
     */
    public function findOneBySlugRoomImage($slug, $idRoom, $idImage);

    /**
     * La función elimina de la BD un objecto RoomImagesGallery
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $roomImagesGallery
     * @version 12/01/2016
     */
    public function delete(RoomImagesGallery $roomImagesGallery);

    /**
     * La siguiente función retorna todas las imágenes de una habitación
     * que sean distinto a un Id en particular
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $idRoom
     * @param $idRoomImageGallery
     * @return array
     * @version 12/01/2016
     */
    public function findByRoomNotEqualId($idRoom, $idRoomImageGallery);

    /**
     * La siguiente función se encarga de retorna
     * un RoomImagesGallery dado un slug y el id de la imagen (Document)
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $idImage
     * @return mixed
     * @version 12/01/2015
     */
    public function findOneBySlugImage($slug, $idImage);
}