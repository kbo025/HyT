<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\RestaurantRepository;
use Navicu\Core\Domain\Model\Entity\Restaurant;


/**
 * RestaurantRepository  implementa los metodos de manipulacion de datos y comunicacion con BD de la clase
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class DbRestaurantRepository extends EntityRepository implements RestaurantRepository
{
    /**
     * La siguiente función retorna los restaurantes de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug)
    {
        return $this->createQueryBuilder('r')
            ->join('r.service', 'service')
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
     *  devuelve un array con los tipos de buffet o carta que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getBuffetCartaTypesArray()
    {
        return [
            [ 'id' => 1, 'name' => 'Buffet' ],
            [ 'id' => 2, 'name' => 'Carta'],
            [ 'id' => 3, 'name' => 'Buffet y Carta']
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
     * @param Restaurant $instance
     */
    public function remove(Restaurant $instance)
    {
        $this->getEntityManager()->remove($instance);
        $this->getEntityManager()->flush();
    }

    /**
     * crea o guarda una instancia
     *
     * @param Restaurant $instance
     */
    public function save(Restaurant $instance)
    {
        $this->getEntityManager()->persist($instance);
        $this->getEntityManager()->flush();
    }
}