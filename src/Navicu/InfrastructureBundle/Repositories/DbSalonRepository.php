<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\SalonRepository;
use Navicu\Core\Domain\Model\Entity\Salon;

/**
 * SalonRepository  implementa los metodos de manipulacion de datos y comunicacion con BD de la clase RoomType
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class DbSalonRepository extends EntityRepository implements SalonRepository
{
    /**
     * La siguiente función retorna los salones de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug)
    {
        return $this->createQueryBuilder('s')
            ->join('s.service', 'service')
            ->join('service.property', 'property')
            ->where('
                    property.slug = :slug
                ')
            ->setParameters(
                array(
                    'slug' => $slug
                )
            )
            ->getQuery()->getResult();
    }

    /**
     * devuelve un array con los tipos de salones que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getSalonTypesArray()
    {
        return [
            [ 'id' => 1, 'name' => 'Salón'],
            [ 'id' => 2, 'name' => 'Auditorio'],
            [ 'id' => 3, 'name' => 'Teatro']
        ];
    }

    /**
     * Devuelve una instancia dado un id
     *
     * @param integer $id
     * @return Object
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * elimina una instancia dado un id
     *
     * @param Salon $instance
     */
    public function remove(Salon $instance)
    {
        $this->getEntityManager()->remove($instance);
        $this->getEntityManager()->flush();
    }

    /**
     * crea o guarda una instancia
     *
     * @param Salon $instance
     */
    public function save(Salon $instance)
    {
        $this->getEntityManager()->persist($instance);
        $this->getEntityManager()->flush();
    }
}