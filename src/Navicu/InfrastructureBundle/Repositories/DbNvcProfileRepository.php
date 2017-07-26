<?php

namespace Navicu\InfrastructureBundle\Repositories;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Navicu\Core\Domain\Repository\NvcProfileRepository;

/**
 * repositorio de AAVVProfileRepository
 * @author Mary sanchezs <msmarycarmen@gmail.com>
 * @version 28/05/2016
 */
class DbNvcProfileRepository extends DbBaseRepository implements NvcProfileRepository
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

    public function findNvcProfile($user)
    {
        return $this->createQueryBuilder('n')
            ->join('n.user','u')
            ->where('u.username = :userName')
            ->setParameters(
                array(
                    'userName' => $user
                )
            )
            ->getQuery()->getResult();
    }

    public function pruebaProperty($param)
    {
        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($param['search'], "search_vector");
        $tsRank = $this->getTsRank($param['search'], "search_vector");

        return $this
            ->select('*')
            ->from('admin_property_view')
            ->where($tsQuery)
            ->paginate(1,10)
            ->order("id", "desc",$tsRank)
            ->getResults();

        // Para funcionar mediante normalizacion de palabras
        $this->normalizedWhereClause($param['search']);

        return $this
            ->select('*')
            ->from('adminPropertyView')
            ->where("commercialname like '%".$param['search']."%'")
            ->paginate(1,10)
            ->order($param['orderBy'], $param['orderType'])
            ->getResults();

        // Para funcionar mediante consulta basica
        $this->normalizedWhereClause($param['search']);

        return $this
            ->select('*')
            ->from('Property')
            ->where("slug like '%".$param['search']."%'")
            ->paginate(1,10)
            ->order($param['orderBy'], $param['orderType'])
            ->getResults();
    }

    /**
     * Buscas todos los usuario de tipo Administrador
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
				phone,
				position,
				departament
			')
            ->from('admin_nvc_profile_view')
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
    public function findNvcProfileByUserId($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = '
            select
                nvc.full_name,
                nvc.treatment,
                nvc.gender,
                nvc.birth_date,
                nvc.identity_card,
                u.username,
                u.email,
                nvc.cell_phone
            from
              nvc_profile nvc
              left join fos_user u on u.id = nvc.user_id
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
            and (f.id != :nvc_id or f.nvc_profile_id is null));

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
            and (f.id != :id or f.nvc_profile_id is null));

        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'id' => $nvc_id
        ]);

        return $stmt->fetch();
    }

    public function getComercialsList()
    {
        $sql = "
            select p.id as id, p.full_name as name
            from fos_user u 
            inner join user_roles ur
            on (u.id = ur.user_id)
            inner join nvc_profile p
            on (p.user_id = u.id)
            inner join role r 
            on (ur.role_id = r.id and r.name in ('ROLE_DIR_COMMERCIAL', 'ROLE_SALES_EXEC', 'ROLE_TELEMARKETING'));
        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
