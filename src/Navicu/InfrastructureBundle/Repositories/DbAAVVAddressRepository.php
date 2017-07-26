<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 09/09/16
 * Time: 06:08 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\AAVVAddressRepository;

class DbAAVVAddressRepository extends EntityRepository implements AAVVAddressRepository
{

    /**
     * @param $updateAddress
     */
    public function save($updateAddress)
    {
        $this->getEntityManager()->persist($updateAddress);
        $this->getEntityManager()->flush();
    }

    public function getAllAddress()
    {
        return $this->findAll();
    }

    public function persistObject($address)
    {
        $this->getEntityManager()->persist($address);
    }

    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }
}