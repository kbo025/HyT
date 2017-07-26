<?php
namespace Navicu\Core\Domain\Adapter;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * La clase siguiente hace uso del conntenedor de servicio para
 * hacer llamado al servicio de session de symfony2.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class CoreSession
{
    /**
     * Metodo que hace uso del manejador de session para
     * guardar en session un parametro especifico.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $name
     * @param $data
     * @return
     * @internal param object $obj
     */
    public static function set($name, $data)
    {
        global $kernel;
        return $kernel->getContainer()->get('session')->set($name, $data);
    }

    /**
     * Metodo que hace uso del manejador de session para devolver
     * un parametro en especifico.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $name
     * @internal param object $obj
     * @return string
     */
    public static function get($name)
    {
        global $kernel;
        return $kernel->getContainer()->get('session')->get($name);
    }

    /**
     * crea la variable de sesion finishRegistrationAAVV
     *
     * @author Gabriel Camacho
     */
    public static function setFinishRegistrationAAVV()
    {
        global $kernel;
        $kernel->getContainer()->get('session')->set('finishRegistrationAAVV',true);
    }

    /**
     * crea la variable de sesion sessionAavv la cual indica
     * que agencia de viaje esta editando el usuario admin
     * @author Alejandro Conde
     */
    public static function setSessionAavvSlug($slug)
    {
        global $kernel;
        $kernel->getContainer()->get('session')->set('sessionAavv',$slug);
    }

    /**
     * La función retorna si el usuario tiene un rol
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $role
     * @return mixed
     * @version 20/10/2015
     */
    public static function isRole($role)
    {
        global $kernel;
        return $kernel->getContainer()->get('security.context')->isGranted($role);
    }

    /**
     * Obtiene el usuario de la session
     *
     * @return mixed
     */
    public static function getUser()
    {
        global $kernel;
        return $kernel->getContainer()->get('security.context')->getToken()->getUser();
    }

    /**
     * La función se encarga de asignar los permisos por defecto segun el rol
     *
     * @author Freddy Contreras
     * @param $role
     * @return string
     */
    public static function setDefaultsPermissions($role)
    {
        global $kernel;

        $response = [];
        $arrayAccess = Yaml::parse(file_get_contents($kernel->getRootDir().'/config/access.yml'))['admin_access'];

        // Al rol admin y director general se le dan todos los permisos
        if ($role == 'ROLE_ADMIN' or $role == 'ROLE_DIR_GENERAL')
            $response = $arrayAccess['module_access'];
        else
            foreach ($arrayAccess['defaults'] as $currentAccess)
                if (in_array($role,$currentAccess['roles'])) {
                    $response = $currentAccess['access'];
                    break;
                }

        return $response;
    }

    /**
     *
     */
    public static function havePermissons($module, $permission = null)
    {
        global $kernel;

        $user = $kernel->getContainer()->get('security.context')->getToken()->getUser();
        if ($user)
            return $user->hasAccess($module, $permission);
        else
            return false;
    }

    /**
     * crea un mensaje flash que contiene un id de sesion de reserva que se usa para desbloquear la reserva
     */
    public static function setSessionReservation($id = null)
    {
        if (!isset($id)) {
            global $kernel;

            $sessionContext = $kernel
                ->getContainer()
                ->get('session');

            $id = $sessionContext->getId().rand(0,1000);
        }
        
        $sessionContext
            ->getFlashBag()
            ->add('sessionReservation', $id);

        //self::set('sessionReservation',$id);

        return $id;
    }

    /**
     * devuelve el mensaje flash almacenado en la sesion de reserva activa
     */
    public static function getSessionReservation()
    {
        global $kernel;

        $data = $kernel
            ->getContainer()
            ->get('session')
            ->getFlashBag()
            ->get('sessionReservation');

        return $data;

        //return self::get('sessionReservation');
    }

    /**
     * devuelve el mensaje flash almacenado en la sesion de reserva activa
     *
     * @param String $id
     * @return String
     */
    public static function renewSessionReservation($id)
    {
        global $kernel;

        $sessionContext = $kernel
            ->getContainer()
            ->get('session');

        /*$sessionContext
            ->getFlashBag()
            ->get('sessionReservation');*/

        $sessionContext
            ->getFlashBag()
            ->add('sessionReservation', $id);

        return true;
    }

    /**
     * Metodo que hace uso del manejador de session para devolver
     * el id de la session de usuario.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @return string
     */
    public static function getSessionId()
    {
        global $kernel;

        return $kernel
            ->getContainer()
            ->get('session')->getId();
    }
}