<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Repository\PropertyServiceRepository;

/**
 * Clase LoadPropertyServiceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de comidas que ofrecen los restaurantes
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class DbPropertyServiceRepository extends EntityRepository implements PropertyServiceRepository
{
    /**
     * La siguiente función retorna los servcios
     * dado un servicio padre (root) referente a un establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $service
     * @return array
     * @version 18/11/2015
     */
    public function findByPropertyService($slug, $service)
    {
        return $this->createQueryBuilder('ps')
            ->join('ps.property', 'property')
            ->join('ps.type', 'service')
            ->join('service.root', 'root')
            ->where('
                    property.slug = :slug and
                    root.title = :service
                ')
            ->setParameters(
                array(
                    'slug' => $slug,
                    'service' => $service
                )
            )
            ->getQuery()->getResult();
    }

     /**
     *  Busca una instancia de PropertyService Dado un ID
     *
     * @param integer $id
     * @retur PropertyService | null
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * persiste la entidad en la base de datos
     *
     * @param PropertyService $propertyService
     */
    public function save(PropertyService $propertyService)
    {
        $this->getEntityManager()->persist($propertyService);
        $this->getEntityManager()->flush();
    }

    /**
     * elimina la entidad de la base de datos
     *
     * @param PropertyService $propertyService
     */
    public function remove(PropertyService $propertyService)
    {
        $this->getEntityManager()->remove($propertyService);
        $this->getEntityManager()->flush();
    }

    /**
     * La función retorna una instancia dado un id del establecimiento y un id de tipo se dervicio
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     *
     * @param $propertyId
     * @param $typeId
     *
     * @return PropertyService | null
     * @version 22/12/2015
     */
    public function findByPropertyType($propertyId, $typeId)
    {
        return $this->createQueryBuilder('ps')
            ->where('
                ps.property = :propertyId and
                ps.type = :typeId
                ')
            ->setParameters([
                'propertyId' => $propertyId,
                'typeId' => $typeId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}