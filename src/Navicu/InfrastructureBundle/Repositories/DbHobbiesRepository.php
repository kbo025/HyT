<?php
/**
 * Created by Isabel Nieto.
 * Date: 26/04/16
 * Time: 11:37 AM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\HobbiesRepository;

/**
 * se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Profession
 *
 * @author Isabel Nieto <isabelcnd@gmail.com>
 * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
 * @version 26/04/16
 */
class DbHobbiesRepository extends EntityRepository implements HobbiesRepository
{
    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }

    public function getAll()
    {
        return $this->findAll();
    }

    public function save($hobbies)
    {
        $this->getEntityManager()->persist($hobbies);
        $this->getEntityManager()->flush();
    }
}