<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Model\Entity\NvcProfile;
use Navicu\Core\Domain\Repository\CommercialProfileRepository;
use Navicu\InfrastructureBundle\Entity\User as UserInfrastructure;

/**
 * DbCommercialProfileRepository
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class DbCommercialProfileRepository extends EntityRepository implements CommercialProfileRepository
{
    /**
     * Método registra a un usuario de tipo comercial
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param CommercialProfile $commercialProfile
     */
    public function registerUser($commercialProfile)
    {
        $user = new UserInfrastructure();
        $user->setUsername($commercialProfile['userName']);
        $user->setEmail($commercialProfile['email']);
        $user->setPlainPassword($commercialProfile['password']);
        $user->addRole(5);
        $user->setEnabled(true);

        $commercial = new NvcProfile();
        $commercial->setUser($user);
        $commercial->setFullName($commercialProfile['fullName']);
        $commercial->setIdentityCard($commercialProfile['identityCard']);
        $user->setNvcProfile($commercial);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->persist($commercial);
        $this->getEntityManager()->flush();
    }
}