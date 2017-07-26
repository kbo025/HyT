<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Repository\PropertyFavoriteImagesRepository;

/**
* La clase se declaran los métodos y funciones que implementan
* el repositorio de la entidad PropertyFavoriteImages
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 26/05/15
*/

class DbPropertyFavoriteImagesRepository extends EntityRepository implements 
	PropertyFavoriteImagesRepository	
{
	/**
	 * Almacena en BD toda la informacion referente al PropertyFavoriteImages
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  PropertyFavoriteImages
	 */
	public function save(PropertyFavoriteImages $favoriteImage)
	{
		$this->getEntityManager()->persist($favoriteImage);
		$this->getEntityManager()->flush();
	}

	/**
	 * La siguiente función se encarga de eliminar
	 * un objecto de tipo PropertyFavoriteImages
	 *
	 * @param PropertyFavoriteImages $favoriteImage
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @version 12/01/2016
	 */
	public function delete(PropertyFavoriteImages $favoriteImage)
	{
		$this->getEntityManager()->remove($favoriteImage);
		$this->getEntityManager()->flush();
	}

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
	public function findOneBySlugImage($slug, $idImage)
	{
		return $this->createQueryBuilder('pfi')
			->select('pfi')
			->join('pfi.image', 'image')
			->join('pfi.property', 'property')
			->where('
				image.id = :idImage AND
				property.slug = :slug
			')
			->setParameters(
				array(
					'idImage' => $idImage,
					'slug' => $slug
				)
			)
			->getQuery()->getOneOrNullResult();
	}

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
	public function findOneBySlugId($slug, $idFavorite)
	{
		return $this->createQueryBuilder('pfi')
			->select('pfi')
			->join('pfi.property', 'property')
			->where('
				pfi.id = :idFavorite and
				property.slug = :slug
			')
			->setParameters(
				array(
					'idFavorite' => $idFavorite,
					'slug' => $slug
				)
			)
			->getQuery()->getOneOrNullResult();
	}

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
	public function findBySlugNotEqualId($slug, $idFavoriteImage)
	{
		return $this->createQueryBuilder('pfi')
			->select('pfi')
			->join('pfi.property','property')
			->where('
					property.slug = :slug and
					pfi.id != :idFavoriteImage
                ')
			->setParameters(array(
					'slug' => $slug,
					'idFavoriteImage' => $idFavoriteImage
				)
			)
			->getQuery()->getResult();
	}
}