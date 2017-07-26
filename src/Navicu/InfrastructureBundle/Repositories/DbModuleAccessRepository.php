<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\ModuleAccessRepository;
use Navicu\InfrastructureBundle\Entity\ModuleAccess;

/**
 * ModuleAccessRepository implementa las operaciones de manipulacion de los datos de la clase ModuleAccess definidas 
 * en la interfaz ModuleAccessRepositoryInterface del dominio
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 26-08-16
 */
class DbModuleAccessRepository extends EntityRepository implements ModuleAccessRepository
{
	/**
	 * Busca dentro de la BD a un rol por su nombre
	 *
	 * @author Alejandro Conde. <adcs2008@gmail.com>
	 * @param  String $data
	 * @return Object
	 */
	public function findByName($data)
	{
        return $this->createQueryBuilder('u')
            ->where('
                u.name = :data 
           
                ')
            ->setParameters(
                array(
                    'data' => $data
                )
            )->getQuery()->getOneOrNullResult();
	}

	public function getAavvModules()
	{
		return $this->createQueryBuilder('u')
			->where("
				u.name LIKE 'aavv%'
				")
			->getQuery()->getResult();
	}

	public function getMainModules()
    {
        return $this->createQueryBuilder('u')
            ->select(
                'u.id',
                'u.name'
            )->where(
                "u.parent is null and u.name <> 'client'"
            )->getQuery()->getArrayResult();
    }

    public function getChildModules($moduleId, $locale)
    {
        return $this->createQueryBuilder('u')
            ->select(
                'u.id',
                't.name'
            )->innerJoin('u.translations', 't', 'WITH', 't.locale = :locale')
            ->where(
                'u.parent = :moduleId'
            )->setParameters(
                array(
                    'moduleId' => $moduleId,
                    'locale' => $locale
                )
            )->getQuery()->getArrayResult();
    }

    public function getModulePermissions($moduleId, $locale)
    {
        $sql = "select p.id, trl.name, false as value
                from permission p
                inner join permission_trl trl
                on p.id = trl.permission and trl.locale = :locale
                 where p.module_id = :moduleId";

        $params['moduleId'] = $moduleId;
        $params['locale'] = $locale;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}