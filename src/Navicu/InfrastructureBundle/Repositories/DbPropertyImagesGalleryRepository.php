<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Repository\PropertyImagesGalleryRepository;

/**
* La clase se declaran los métodos y funciones que implementan
* el repositorio de la entidad PropertyImagesGallery
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 26/05/15
*/

class DbPropertyImagesGalleryRepository extends EntityRepository implements 
	PropertyImagesGalleryRepository	
{

	/**
	 * Almacena en BD toda la informacion referente al PropertyImagesGallery
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  PropertyImagesGallery
	 */
	public function save(PropertyImagesGallery $propertyImage)
	{
		$this->getEntityManager()->persist($propertyImage);
		$this->getEntityManager()->flush($propertyImage);
	}

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
	public function findOneBySlugGalleryImage($slug, $idGallery, $idImage)
	{
		return $this->createQueryBuilder('pig')
			->select('pig')
			->join('pig.image', 'image')
			->join('pig.property_gallery','property_gallery')
			->join('property_gallery.property','property')
			->where('
					property.slug = :slug and
					property_gallery.id = :idGallery and
					image.id = :idImage
                ')
			->setParameters(array(
					'slug' => $slug,
					'idGallery' => $idGallery,
					'idImage' => $idImage
				)
			)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * La siguiente función se encarga de eliminar de la BD
	 * un objecto PropertyImagesGallery
	 *
	 * @param PropertyImagesGallery $propertyImagesGallery
	 */
	public function delete(PropertyImagesGallery $propertyImagesGallery)
	{
		$this->getEntityManager()->remove($propertyImagesGallery);
		$this->getEntityManager()->flush();
	}

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
	public function findOneBySlugImage($slug, $idImage)
	{
		return $this->createQueryBuilder('pig')
			->select('pig')
			->join('pig.image', 'image')
			->join('pig.property_gallery','property_gallery')
			->join('property_gallery.property','property')
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