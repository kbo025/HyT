<?php
/**
 * Created by Isabel Nieto.
 * Date: 26/04/16
 * Time: 10:13 AM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ProfessionRepository;

/**
 * se declaran los metodos y funciones que implementan
 * el repositorio de la entidad Profession
 *
 * @author Isabel Nieto <isabelcnd@gmail.com>
 * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
 * @version 26/04/16
 */
class DbProfessionRepository extends EntityRepository implements ProfessionRepository
{
    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }

    public function getAll()
    {
        return $this->findAll();
    }

    public function save($profession)
    {
        $this->getEntityManager()->persist($profession);
        $this->getEntityManager()->flush();
    }
}