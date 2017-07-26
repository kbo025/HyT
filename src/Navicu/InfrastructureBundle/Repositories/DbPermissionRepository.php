<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\PermissionRepository;
use Navicu\InfrastructureBundle\Entity\Permission;

/**
 * PermissionRepository implementa las operaciones de manipulacion de los datos de la clase Permission definidas 
 * en la interfaz PermissionRepositoryInterface del dominio
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 26-08-16
 */
class DbPermissionRepository extends EntityRepository implements PermissionRepository
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
                u.name = UPPER(:data) 
           
                ')
            ->setParameters(
                array(
                    'data' => $data
                )
            )->getQuery()->getOneOrNullResult();
	}

	public function findByNameAndModule($name, $module)
	{
        return $this->createQueryBuilder('u')
        	->innerJoin('u.module', 'm', 'WITH', 'm.name = :module')
            ->where('
                u.name = UPPER(:name) 
           
                ')
            ->setParameters(
                array(
                    'name' => $name,
                    'module' => $module 
                )
            )->getQuery()->getOneOrNullResult();
	}

	public function validNameInModule($module, $name)
	{
		$perm =  $this->createQueryBuilder('u')
			->where('
					u.module = :module AND
					u.name 		= :name
				')
			->setParameters(
				array(
					'module' => $module,
					'name'	 => $name
				)
			)->getQuery()->getOneOrNullResult();

			if($perm)
				return false;
			else
				return true;
	}

}