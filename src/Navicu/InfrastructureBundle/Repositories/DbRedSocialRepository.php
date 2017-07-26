<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\RedSocialRepository;

/**
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 09-06-2015
 */
class DbRedSocialRepository extends EntityRepository implements RedSocialRepository
{
    /**
     * Busca dentro de la entidad RedSocial información por medio
     * de una colección de atributos.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param  Array
     * @return Array
     */
    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }
}