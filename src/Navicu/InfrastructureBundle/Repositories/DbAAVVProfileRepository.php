<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Repository\AAVVProfileRepository;


/**
 * @author Mary sanchezs <msmarycarmen@gmail.com>
 * @version 28/05/2016
 */
class DbAAVVProfileRepository extends DbBaseRepository  implements AAVVProfileRepository
{
    /**
     * Funcion encargada de actualizar sobre las agencias de viajes
     * los usuarios que hayan sido agregados actualmente, siempre y cuando la
     * agencia este activa y el usuario sea NUEVO
     *
     * @return array
     * @version 20/12/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function changeStatusOfAavvProfile()
    {
        $qb2 = $this->createQueryBuilder('ap');
        $qb = $this->createQueryBuilder('avP');
        $today = new \DateTime('now');

        $indexToLookUp = $qb2->select('distinct(ap.id)')
            ->join('NavicuDomain:AAVV','aavv','WITH','aavv.id = ap.aavv')
            ->where('aavv.status_agency = 2')
            ->andWhere('ap.status = 0')
            ->getQuery()
            ->getResult();

        $usersActivated = $qb
            ->update()
            ->set('avP.status', 1)
            ->set('avP.last_activation', ':today')
            ->where('avP.id in (:arrayOfIds)')
            ->setParameters([
                'arrayOfIds' => $indexToLookUp,
                'today' => $today
            ])
            ->getQuery()
            ->getScalarResult();

        return ['idAavvProfile' => $indexToLookUp, 'usersActivated' => $usersActivated];
    }

//    public function delete(AAVVProfile $profile)
//    {
//        $this->getEntityManager()->remove($profile);
//        $this->getEntityManager()->flush();
//    }

    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }

    public function save($profile)
    {
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->flush();
    }


    public function findByFilters($array)
    {
        $qb = $this->createQueryBuilder('p');

        $sql = '1=1';
        $parameters = [];

        if(!empty($array['word'])) {
            $sql = $sql.' AND ('.$qb->expr()->like('lower(UNACCENT(user.username))', 'lower(UNACCENT(:word))');
            $sql = $sql.' OR '.$qb->expr()->like('lower(UNACCENT(user.email))', 'lower(UNACCENT(:word))').')';
            $parameters['word'] = '%'.$array['word'].'%';
        }

        return  $qb
            ->where ($sql)
            ->join('p.user', 'user')
            ->setParameters ($parameters)
            ->getQuery()
            ->getResult();
    }

    public function findUsersByWords($data)
    {
        $tsQuery = $this->getTsQuery($data['search'], "search_vector");
        $tsRank = $this->getTsRank($data['search'], "search_vector");

        return $this
            ->select('
				id,
				user_name,
				email,
				enabled,
				phone,
				city,
				state
			')
            ->from('admin_aavv_user_view')
            ->where($tsQuery)
            ->paginate($data['page'], 20)
            ->order($data['orderBy'], $data['orderType'], $tsRank)
            ->getResults();

    }
}