<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\BarRepository;
use Navicu\Core\Domain\Model\Entity\Bar;

/**
 * Se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Bar
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 18/11/15
 */
class DbBarRepository extends EntityRepository implements BarRepository
{
    /**
     * La siguiente función retorna los bares de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug)
    {
        return $this->createQueryBuilder('b')
            ->join('b.service', 'service')
            ->join('service.property','property')
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
     *  devuelve un array con los tipos de bares que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getBarTypesArray()
    {
        return [
            [ 'id' => 1, 'name' => 'Bar'],
            [ 'id' => 2, 'name' => 'Discoteca']
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
     * @param Bar $instance
     */
    public function remove(Bar $instance)
    {
        $this->getEntityManager()->remove($instance);
        $this->getEntityManager()->flush();
    }

    /**
     * crea o guarda una instancia
     *
     * @param Bar $instance
     */
    public function save(Bar $instance)
    {
        $this->getEntityManager()->persist($instance);
        $this->getEntityManager()->flush();
    }
}