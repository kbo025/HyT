<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Repository\OwnerProfileRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Pack
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbOwnerProfileRepository extends DbBaseRepository implements OwnerProfileRepository
{

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

    /**
     * Buscas todos los usuario de tipo hoteleros
     * dado un conjunto de palabras
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param  String $data
     * @return Array
     */
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
				phones,
				country
			')
            ->from('admin_owner_profile_view')
            ->where($tsQuery)
            ->paginate($data['page'], 20)
            ->order($data['orderBy'], $data['orderType'], $tsRank)
            ->getResults();

    }

    /**
     * Retorna los datos de un usuario admin dado
     * el id de un usuario
     *
     * @param $user_id
     * @return mixed
     */
    public function findOwnerByUserId($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = '
            select
                o.full_name,
                o.treatment,
                o.gender,
                o.birth_date,
                o.identity_card,
                u.username,
                u.email,
                o.cell_phone
            from
              owner_profile o
              left join fos_user u on u.id = o.user_id
              where u.id = :user_id

        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(array('user_id' => $user_id));
        return $stmt->fetch();
    }

    /**
     * Búsqueda de la existencia de un usuario
     * dado el id del perfil y username
     *
     * @param $username
     * @param $nvc_id
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findExistUsername($username, $nvc_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = '
            select f.id from fos_user f where
            (f.username = :username
            and (f.id != :nvc_id or f.owner_profile_id is null));

        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'nvc_id' => $nvc_id
        ]);

        return $stmt->fetch();
    }

    /**
     * Búsqueda de la existencia de un usuario
     * dado el id del perfil y email
     *
     * @param $email
     * @param $nvc_id
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findExistEmail($email, $nvc_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = '
            select f.id from fos_user f where
            (f.email = :email
            and (f.id != :id or f.owner_profile_id is null));

        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'id' => $nvc_id
        ]);

        return $stmt->fetch();
    }
}