<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Repository\RoomImagesGalleryRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad RoomImagesGallery
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 26/05/15
*/

class DbRoomImagesGalleryRepository extends EntityRepository implements 
	RoomImagesGalleryRepository
{
	/**
	 * Almacena en BD toda la informacion referente al RoomImagesGallery
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  RoomImagesGallery
	 */
	public function save(RoomImagesGallery $roomImage)
	{
		$this->getEntityManager()->persist($roomImage);
		$this->getEntityManager()->flush($roomImage);
	}

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
	public function findOneBySlugRoomImage($slug, $idRoom, $idImage)
	{
		return $this->createQueryBuilder('ri')
			->select('ri')
			->join('ri.image', 'image')
			->join('ri.room','room')
			->join('room.property', 'property')
			->where('
					property.slug = :slug and
					room.id = :idRoom and
					image.id = :idImage
                ')
			->setParameters(array(
					'slug' => $slug,
					'idRoom' => $idRoom,
					'idImage' => $idImage
				)
			)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * La función elimina de la BD un objecto RoomImagesGallery
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $roomImagesGallery
	 * @version 12/01/2016
	 */
	public function delete(RoomImagesGallery $roomImagesGallery)
	{
		$this->getEntityManager()->remove($roomImagesGallery);
		$this->getEntityManager()->flush();
	}

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
	public function findByRoomNotEqualId($idRoom, $idRoomImageGallery)
	{
		return $this->createQueryBuilder('ri')
			->select('ri')
			->join('ri.room','room')
			->where('
					ri.id != :idGallery and
					room.id = :idRoom
                ')
			->setParameters(array(
					'idGallery' => $idRoomImageGallery,
					'idRoom' => $idRoom
				)
			)
			->getQuery()->getResult();
	}

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
	public function findOneBySlugImage($slug, $idImage)
	{
		return $this->createQueryBuilder('ri')
			->select('ri')
			->join('ri.image','image')
			->join('ri.room','room')
			->join('room.property','property')
			->where('
					property.slug = :slug and
					image.id = :idImage
                ')
			->setParameters(array(
					'slug' => $slug,
					'idImage' => $idImage
				)
			)
			->getQuery()->getOneOrNullResult();
	}


}