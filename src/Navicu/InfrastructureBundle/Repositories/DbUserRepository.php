<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;
use Navicu\Core\Domain\Repository\UserRepository;
use Navicu\InfrastructureBundle\Entity\User;

/**
 * UserRepository implementa las operaciones de manipulacion de los datos de la clase User definidas
 * en la interfaz UserRepositoryInterface del dominio
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 05-05-15
 */
class DbUserRepository extends DbBaseRepository implements UserRepository
{

	/**
	 * Busca dentro de la BD a un usuario por su userName o
	 * por su Email.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param  String $data
	 * @return Object
	 */
	public function findByUserNameOrEmail($data)
	{
        return $this->createQueryBuilder('u')
            ->where('
                LOWER(u.username) = LOWER(:data) or
                LOWER(u.email) = LOWER(:data)
                ')
            ->setParameters(
                array(
                    'data' => $data
                )
            )->getQuery()->getOneOrNullResult();
	}

    /**
     * Funcion encargada de activar los usuarios de las agencias de viajes dentro de fos_user
     *
     * @param $users
     * @return \Doctrine\DBAL\Driver\Statement
     * @author Isabel Nieto <isabecnd@gmail.com>
     */
    public function updateStatus($users)
    {
        // Concatenamos y eliminanos la ultima ',' no necesaria
        $param = "";
        foreach ($users as $user)
            $param = $user . "," . $param;
        $param = rtrim($param,',');
        $param = "(" . $param . ")";

        return $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery("UPDATE fos_user SET enabled = TRUE WHERE aavv_profile_id IN $param")
            ->rowCount();
	}

	/**
	 * Buscas todos los usuario dado un conjunto de palabras
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param  String $data
	 * @return Object
	 */
	public function findUsersByWords($data)
	{
		$tsQuery = $this->getTsQuery($data['search'], "search_vector");
		$tsRank = $this->getTsRank($data['search'], "search_vector");

        $aditionalCriteria = $this->criteriaByField('role', $data['aditionalCriteria']);
        if ($aditionalCriteria != '') {
            return $this
                ->select('
				id,
				user_name,
				email,
				enabled,
				role,
				phone
			')
                ->from('admin_all_user_view')
                ->where($tsQuery, $aditionalCriteria)
                ->paginate($data['page'], 10)
                ->order($data['orderBy'], $data['orderType'], $tsRank)
                ->getResults();
        } else {
            $emptyStructure = [];
            $emptyStructure['pagination']['current'] = 1;
            $emptyStructure['pagination']['items'] = 0;
            $emptyStructure['pagination']['next'] = 2;
            $emptyStructure['pagination']['pages'] = 0;
            $emptyStructure['pagination']['previous'] = null;
            $emptyStructure['data'] = [];
            return $emptyStructure;
        }

	}

	/**
	 * Busca dentro de la vista de usuario la catidad por role de usuario.
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @return Object
	 */
	public function findCountByUser()
	{
		return $this
			->select('
				role,
				count(id) as cantidad
			')
			->from('admin_all_user_view')
			->where('id is not null group by role')
			->paginate(1,10)
			->getResults();
	}
}
