<?php
namespace Navicu\Core\Domain\Adapter;

use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\InfrastructureBundle\Entity\User as EntityUser;
use Navicu\Core\Domain\Contract\User;

/**
* La clase siguiente hace uso del manejo del comportamiento de
* la entiad User de infraestructura.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
class CoreUser implements User
{
    /**
     * Metodo que hace uso de la entidad user para el registro de usuario
     * dado un conjunto de datos.
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param array $data
	 * @param object $profile
	 */
    public static function setUser($data, $profile = null)
    {
        global $kernel;
        $container = $kernel->getContainer();

        $rf = $container->get('RepositoryFactory');
        $role = $rf->get('Role')->findByName($data['role']);
        $user = new EntityUser();
        if(isset($data["password"])) {
            $password = new Password($data["password"]);
            $password = $password->toString();
        } else {
            $password = substr(sha1(uniqid(mt_rand(), true)),0,8);
        }
		$user->setUsername($data["username"]);
		$user->setEmail($data["email"]);
        $user->setPlainPassword($password);
        /*Buscamos a nivel de bd el rol con ese identificador*/
        $user->addRole($role);
		$user->setEnabled(true);
        $profile->setUser($user);
        return $user;

    }

    /**
     * Retorna y actualiza un usuario
     *
     * la variable debe contener los valores basicos
     * de un usuario username, email y password
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @return EntityUser
     */
    public static function generateUser($data, &$password = false)
    {
        $user = new EntityUser();
        if (!isset($data['password'])) {
            $data['password'] = substr(sha1(uniqid(mt_rand(), true)),0,8);
            $password = $data['password'];
        }

        $user->setAtributes($data);

        return $user;
    }

    /**
     * Funcion que se encuentra asociada a los campos de User para Crear y editar perfiles de usuarios
     * @Autor Mary Sanchez <msmarycarmen@gmail.com>
     * @param null $profile
     * @param $data
     * @return EntityUser
     * @version 23-06-16
     */
    public  static function setEditCreatUser($data, $profile = null)
    {

        global $kernel;
        $container = $kernel->getContainer();

        $rf = $container->get('RepositoryFactory');
        if(!isset($data['id'])) {

           if (isset($data["password"])) {
               $password = new Password($data["password"]);
               $password = $password->toString();
           } else {
               $password = substr(sha1(uniqid(mt_rand(), true)), 0, 8);
           }
           $user = new EntityUser();
        } else
           $user = $profile->getUser();

        $user->setUsername($data["username"]);
        $user->setEmail($data["email"]->toString());
        if (isset($password))
            $user->setPlainPassword($password);

        $roleName = $user->translateRole($data['role']);

        if(!$user->hasRole($roleName)) {
            foreach ($user->getRolesList() as $role) {
                $user->removeRole($role->getName());
            }

            $role = $rf->get('Role')->findByName($roleName);
            $user->addRole($role);
        }

        $user->setEnabled(true);

        if ($data['role'] == 6) {
            /** perfil Agente de viaje**/

            $user->setAavvProfile($profile);
            $profile->setUser($user);

        } elseif ($data["role"] >= 7 and $data['role'] <= 20) {
            /** perfil administrador**/

            $user->setNvcProfile($profile);
            $profile->setUser($user);

        } elseif ($data['role'] == 21) {
            /** perfil cliente**/
            $user->setClientProfile($profile);
            $profile->setUser($user);

        } elseif ($data['role'] == 1) {
            /** perfil Hotelero**/
            $user->setOwnerProfile($profile);
            $profile->setUser($user);
        }

       return $user;
    }

    /**
     * Metodo que hace uso del repositoryFactory para la persistencia
     * de un objeto user.
     *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 *
	 * @param ReposirotyFactory $rf
	 * @param object $obj
	 */
    public static function save($user, $rf)
    {
		$rf->get("User")->save($user);
    }

    public static function updatePassword($user, $password)
    {
        global $kernel;
        $container = $kernel->getContainer();

        $user->setPlainPassword($password);

        $container->get('fos_user.user_manager')->updatePassword($user);

        $container->get('doctrine.orm.default_entity_manager')->flush();
    }


}