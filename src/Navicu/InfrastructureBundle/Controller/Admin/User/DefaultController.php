<?php
namespace Navicu\InfrastructureBundle\Controller\Admin\User;

use Navicu\Core\Application\UseCases\Admin\Users\Owner\CreateUserOwner\CreateUserOwnerCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Owner\EditUserOwner\EditUserOwnerCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Navicu\Core\Application\UseCases\Admin\Users\GetUsers\GetUsersCommand;
use Navicu\Core\Application\UseCases\Admin\Users\RegisterUser\RegisterUserCommand;
use Navicu\Core\Application\UseCases\Admin\Users\NewEditUser\NewEditUserCommand;
use Navicu\Core\Application\UseCases\Admin\Users\GetInfoUser\GetInfoUserCommand;
use Navicu\Core\Application\UseCases\Admin\Users\DeactivateAdvanceForUser\DeactivateAdvanceForUserCommand;
use Navicu\Core\Application\UseCases\Admin\Users\StatusChangeUsers\StatusChangeUsersCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Admin\GetDataAdmin\GetDataAdminCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Owner\GetDataOwner\GetDataOwnerCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Admin\CreateUserAdmin\CreateUserAdminCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Admin\EditUserAdmin\EditUserAdminCommand;

/**
 * Clase encargada de recibir las peticiones realizadas sobre los
 * usuarios del sistema
 *
 * Class DefaultController
 * @package Navicu\InfrastructureBundle\Controller\Admin\User
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 */
class DefaultController extends Controller
{
/**
     * Vista del home page del listado de los usuarios y por rol asignados
     * @author mary sanchez <msmarycarmen@gmail.com>     *
     * @param $rol
     * @return \Symfony\Component\HttpFoundation\Response
     */


    public function indexAction($role, Request $request)

    {

        switch ($role) {
            case '1':
                $twig ='NavicuInfrastructureBundle:Admin:users/propertiesUsers/propertiesUsers.html.twig';
                break;
            case '2':
                $twig ='NavicuInfrastructureBundle:Admin:users/adminUsers/adminUsers.html.twig';
                break;
            case '3':
                $twig ='NavicuInfrastructureBundle:Admin:users/clientUsers/clientUsers.html.twig';
                break;
            case '4':
                $twig ='NavicuInfrastructureBundle:Admin:users/AAVVUsers/AAVVUsers.html.twig';
                break;
            case '0':
                $twig = 'NavicuInfrastructureBundle:Admin:users/allUsers/allUsers.html.twig';
            default:
                break;
        }
        return $this->render($twig);
    }


    /**
     * Validación de los datos enviados
     * al momento de crear un usuario
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @version 04/04/2016
     */
    public function newRegisterAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $command = new RegisterUserCommand();

            if (isset($_POST['role']))
                $command->setRole($_POST['role']);

            if (isset($_POST['username']))
                $command->setUsername($_POST['username']);

            if (isset($_POST['password']))
                $command->setPassword($_POST['password']);

            if (isset($_POST['fullName']))
                $command->setFullName($_POST['fullName']);

            if (isset($_POST['identityCard']))
                $command->setIdentityCard($_POST['identityCard']);

            if (isset($_POST['email']))
                $command->setEmail($_POST['email']);

            if (isset($_POST['password']))
                $command->setPassword($_POST['password']);

            $response = $this->get('commandbus')->execute($command);

            if ($response->getStatusCode() != 200) {
                return $this->render('NavicuInfrastructureBundle:Admin/User:newUser.html.twig',[
                    'error' => $response->getData(),
                    'form' => $command->getRequest(),
                    'success' => false
                ]);
            } else {
                return $this->redirectToRoute('navicu_users_new', ['success' => true], 301);
            }
        }
    }

    /**
     *Esta funcion crea y editar los usuarios segun su rol asignados
     * @author mary sanchez <msmarycarmen@gmail.com>
     * @internal $request , Id
     * @version 31/05/2016
     */
    public function newUserEditsAction(Request $request){

       if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new NewEditUserCommand($data);
            $response = $this->get('commandbus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
    }

    /**
     *Esta funcion Editar los usuarios segun su rol asignados
     * @author mary sanchez <msmarycarmen@gmail.com>
     * @internal param $
     * @version 09/06/2016 */

    public function editUsersAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new EditUserCommand($data);
            $response = $this->get('commandbus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
    }


    /**
     *Esta funcion inactiva los usuarios segun su rol asignados
     * @author mary sanchez <msmarycarmen@gmail.com>
     * @internal param $
     * @version 15/06/2016
     */
    public function statusChangeUsersAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new StatusChangeUsersCommand($data['id']);
            $response = $this->get('commandbus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
    }

    /**
     * La función se encarga de obtener los datos
     * necesarios para editar o crear un usuario
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $role
     * @param $userId
     */
    public function infoNewEditAction($role, $userId = null)
    {
        $command = new GetInfoUserCommand($role, $userId);
        $response = $this->get('CommandBus')->execute($command);
        return $this->render('NavicuInfrastructureBundle:Admin/User:newUser.html.twig',[
            'role' => json_encode($role),
            'data' => json_encode($response->getData())
        ]);
    }

    /**
     * accion para la activacion y desactivacion de los dias de antelacion de un usuario
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     */
    public function changeStatusAdvanceForUserAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $user = $this->get('SessionService')->getUserSession();
            $data['deactivateBy'] = $user->getId();
            $command = new DeactivateAdvanceForUserCommand($data);
            $response = $this->get('commandbus')->execute($command);
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return Response('Not Found',404);
    }


    /**
     * La siguiente función hace el render
     * de la vista editar o crear usuario
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersEditAction ()
    {
        $data = [];
        $data['total'] = $this->get('doctrine.orm.entity_manager')
            ->getRepository('NavicuInfrastructureBundle:User')
            ->findCountByUser()['data'];

        $aux["role"] = "all";
        $aux["cantidad"] = array_sum(array_column($data["total"], "cantidad"));
        array_push($data["total"], $aux);

        $data['total'] = json_encode($data['total']);

        $roles = $this->get('doctrine.orm.entity_manager')
            ->getRepository('NavicuInfrastructureBundle:Role')
            ->getSimpleList();

        $rolesArray = array();

        foreach($roles as $role) {

            if (!in_array($role['name'], ['ROLE_ADMIN_FIREWALL', 'ROLE_USER', 'ROLE_AAVV'])
                && $role['userReadableName'] != null && is_null($role['aavv_id'])) {
                $current = array();

                $current['id']   = $role['id'];
                $current['role'] = $role['userReadableName'];

                $rolesArray[] = $current;
            }
        }

        $data['roles'] = json_encode($rolesArray);

        return $this->render('NavicuInfrastructureBundle:Admin:users/newUser/newUser.html.twig',$data);
    }

    /**
     * Funciòn es usada para devolver una lista de usuario dado
     * un role.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param  Request $request
     * @return Json
     */
    public function getListUserAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new GetUsersCommand($data);
            $response = $this->get('commandbus')->execute($command);

            return new JsonResponse($response->getData(), $response->getStatusCode());
        } else
            return Response('Not Found',404);
    }

    /**
     * La siguiente función retorna los datos de un usuario
     * hotelero o admin, dado el id del usuario y el tipo de usuario
     *
     * @author Freddy contreras
     * @param Request $request
     * @return JsonResponse
     */
    public function getDataAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['user_type'])) {
                if ($data['user_type'] == 'admin')
                    $command = new GetDataAdminCommand($data);
                 else if ($data['user_type'] == 'owner')
                    $command = new GetDataOwnerCommand($data);

                $response = $this->get('commandbus')->execute($command);
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }

            return new JsonResponse(null,400);
        }

        return new JsonResponse(null, 400);
    }

    /**
     * La siguiente función retorna los datos de un usuario
     * hotelero o admin, dado el id del usuario y el tipo de usuario
     *
     * @author Freddy contreras
     * @param Request $request
     * @return JsonResponse
     */
    public function setDataAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['user_type'])) {
                if ($data['user_type'] == 'admin')
                    $command = new SetDataCommand($data);
                else if ($data['user_type'] == 'owner')
                    return new JsonResponse(null,200);

                $response = $this->get('commandbus')->execute($command);
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }

            return new JsonResponse(null,400);
        }

        return new JsonResponse(null, 400);
    }

    /**
     * Crear usuario
     *
     * En el siguiente controlador se puede
     * crear usuario de tipo admin (nvc_profile) o hotelero
     *
     * @author Freddy Contreras
     * @param Request $request
     * @return JsonResponse
     */
    public function createUserAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['user_type'])) {
                if ($data['user_type'] == 'admin')
                    $command = new CreateUserAdminCommand($data);
                else if ($data['user_type'] == 'owner')
                    $command = new CreateUserOwnerCommand($data);
                else
                    return new JsonResponse(['message' => 'user_type_not_defined'], 400);

                $response = $this->get('commandbus')->execute($command);
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }

            return new JsonResponse(null,400);
        }

        return new JsonResponse(null, 400);
    }

    /**
     * Crear usuario
     *
     * En el siguiente controlador se puede
     * editar un usuario admin (nvc_profile) o hotelero (owner_profile)
     *
     * @author Freddy Contreras
     * @param Request $request
     * @return JsonResponse
     */
    public function editUserAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            if (isset($data['user_type'])) {
                if ($data['user_type'] == 'admin')
                    $command = new EditUserAdminCommand($data);
                else if ($data['user_type'] == 'owner')
                    $command = new EditUserOwnerCommand($data);

                $response = $this->get('commandbus')->execute($command);
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }

            return new JsonResponse(null,400);
        }

        return new JsonResponse(null, 400);
    }
}
