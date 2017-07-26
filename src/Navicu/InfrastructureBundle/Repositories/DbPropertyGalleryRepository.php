<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Model\Entity\PropertyGallery;
use Navicu\Core\Domain\Repository\PropertyGalleryRepository;

/**
* La clase se declaran los métodos y funciones que implementan
* el repositorio de la entidad PropertyGallery
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 26/05/15
*/

class DbPropertyGalleryRepository extends EntityRepository implements PropertyGalleryRepository
{

	/**
	 * Almacena en BD toda la informacion referente al Room
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param  Room
	 */
	public function save(PropertyGallery $propertyGallery)
	{
		$this->getEntityManager()->persist($propertyGallery);
		$this->getEntityManager()->flush();
	}

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
	public function findOneBySlugGallery($slug, $propertyGId)
	{
		return $this->createQueryBuilder('pg')
			->where('
                property.slug = :slug and
                pg.id = :propertyGId
                ')
			->setParameters(array(
				'slug' => $slug,
				'propertyGId' => $propertyGId
			))
			->join('pg.property','property')
			->getQuery()->getOneOrNullResult();
	}
}