<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ClientProfileRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad ClientProfile
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20-08-2015
 */
class DbClientProfileRepository extends DbBaseRepository implements ClientProfileRepository
{
	/**
	 * Busca dentro de la BD información referente a un array de paramtros.
	 *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param  Integer $id
	 * @return Object Property
	 */
	public function findOneByArray($array)
	{
        return $this->findOneBy($array);
	}

    /**
     * Almacena en BD toda la informacion referente a un perfil de cliente.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param  Room
     */
    public function save($client)
	{
		$this->getEntityManager()->persist($client);
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
            ->setMaxResults(300)
            ->getResult();
    }

    /**
     * Buscas todos los usuario de tipo cliente
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
				country,
				state,
                country as city,
                CASE
                WHEN
                    disable_advance = false
                THEN
                    \'0\'
                ELSE
                    \'1\'
                END as disable_advance
			')
            ->from('admin_client_user_view')
            ->where($tsQuery)
            ->paginate($data['page'], 20)
            ->order($data['orderBy'], $data['orderType'], $tsRank)
            ->getResults();

    }
}