<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\RoleRepository;
use Navicu\InfrastructureBundle\Entity\Role;

/**
 * UserRepository implementa las operaciones de manipulacion de los datos de la clase User definidas
 * en la interfaz UserRepositoryInterface del dominio
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 05-05-15
 */
class DbRoleRepository extends EntityRepository implements RoleRepository
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



	public function findById($id)
	{
        return $this->createQueryBuilder('u')
            ->where('
                u.id = :id 
           
                ')
            ->setParameters(
                array(
                        'id' => $id
                )
            )->getQuery()->getOneOrNullResult();
	}
	public function findInAavv($name, $id)
	{
        return $this->createQueryBuilder('u')
        	->innerJoin('u.aavv', 'a', 'WITH', 'a.id = :id')
            ->where('
                u.name = UPPER(:name)

                ')
            ->setParameters(
                array(
                    'name' => $name,
                    'id' => $id
                )
            )->getQuery()->getOneOrNullResult();
	}

	public function getSimpleList()
    {
        return $this->createQueryBuilder('u')
            ->select(
                'u.id',
                'u.name',
                'u.userReadableName',
                'av.id as aavv_id'
            )
            ->leftJoin('u.aavv', 'av')
            ->getQuery()->getArrayResult();
    }

    public function getUserPermissions($userId)
    {
        $sql = "select r.id, r.userreadablename as role
                from role r
                inner join user_roles ur on r.id = ur.role_id
                where ur.user_id = :userId and r.name <> 'ROLE_ADMIN_FIREWALL'";

        $params['userId'] = $userId;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

	public function getPermissionsInModule($roleId, $moduleId, $locale)
    {
        $sql = "select p.id, trl.name, true as value
                from role r
                inner join role_permissions rp
                on r.id = rp.role_id
                inner join permission p
                on rp.permission_id = p.id
                inner join permission_trl trl
                on p.id = trl.permission and trl.locale = :locale
                inner join module m
                on p.module_id = m.id
                where r.id = :roleId and m.id = :moduleId";

        $params['roleId'] = $roleId;
        $params['moduleId'] = $moduleId;
        $params['locale'] = $locale;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();


    }


    public function delete(Role $role)
    {
        
        /*$perms = $role->getPermissions();

        $users = $role->getUsers();

        $modules = $role->getModules();

        foreach($perms as $perm) {
            $perm->removeRole($role);
        }

        foreach($users as $user) {
            $user->removeRole($role);
        }

        foreach($modules as $module) {
            $module->removeRole($role);
        }

        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();*/

        $this->getEntityManager()->remove($role);

        $this->getEntityManager()->flush();
    }


	public function save($role)
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }


}
