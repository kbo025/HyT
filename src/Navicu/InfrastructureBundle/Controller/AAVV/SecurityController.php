<?php
namespace Navicu\InfrastructureBundle\Controller\AAVV;

use Navicu\Core\Application\UseCases\AAVV\Security\DeleteUser\DeleteUserCommand;
use Navicu\Core\Domain\Adapter\CoreSession;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use Navicu\Core\Domain\Adapter\CoreTranslator;

use Navicu\Core\Application\UseCases\AAVV\Preregister\GetValidAAVVEmail\GetValidAAVVEmailCommand;
use Navicu\Core\Application\UseCases\AAVV\Security\CreateRole\CreateRoleCommand;
use Navicu\Core\Application\UseCases\AAVV\Security\EditRole\EditRoleCommand;
use Navicu\Core\Application\UseCases\AAVV\Security\DeleteRole\DeleteRoleCommand;
use Navicu\Core\Application\UseCases\AAVV\Security\EditRolePermissions\EditRolePermissionsCommand;

use Navicu\Core\Application\UseCases\AAVV\Security\GetUsers\GetUsersCommand;
use Navicu\Core\Application\UseCases\AAVV\Security\EditUsers\EditUsersCommand;

use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\InfrastructureBundle\Entity\ModuleAccess;
use Navicu\InfrastructureBundle\Entity\Permission;


class SecurityController extends Controller
{
    public function getRolesAction()
    {
        $security = $this->container->get('security.context');

        $user = $security->getToken()->getUser();

        $profile = $user->getAavvProfile();

        $aavv = $profile->getAavv();

        $roles = $aavv->getRoles()->toArray();

        $response = array();

        foreach($roles as $role) {

            $current = array();

            $current['id'] = $role->getId();
            $current['role'] = $role->getName();
            $current['admin'] = $role->isAdmin();

            $response[] = $current;
        }

        return new JsonResponse($response,200);
    }

    public function getModulesAction()
    {
        
        $modulerep = $this->getdoctrine()->getRepository('NavicuInfrastructureBundle:ModuleAccess');

        $modules = $modulerep->getAavvModules();

        $translator = new CoreTranslator();

        $response = array();

        foreach($modules as $module) {

            $current = array();

            $current['id'] = $module->getId();
            //$current['module'] = $module->getName();
            $current['module'] = $translator->getTranslator($module->getName(), 'modules');

            $response[] = $current;
        }

        return new JsonResponse($response,200);
    }

    public function getRolePermsAction($id)
    {
        $rolerep = $this->getdoctrine()->getRepository('NavicuInfrastructureBundle:Role');

        $translator = new CoreTranslator();

        $role = $rolerep->findById($id);

        $rolemodules = $role->getModules();

        $response = array();

        foreach($rolemodules as $module) {

            $curmodperms = array();

            $curmodperms['module'] = $translator->getTranslator($module->getName(), 'modules');
            $curmodperms['permissions'] = $role->getModulePerms($module->getName());

            $response[] = $curmodperms;

        }

        return new JsonResponse($response,200);
    }

    public function rolesListAction()
    {
        $user = CoreSession::getUser();

        //dump($user->hasAccess('aavv_billing', 'VIEW'));

        return $this->render('NavicuInfrastructureBundle:AAVV/editRoles:index.html.twig');
    }

    public function usersListAction()
    {
        $locationsrep = $this->getDoctrine()->getRepository('NavicuDomain:Location');

        $locations = $locationsrep->getAll();

        $data['locations'] = $locations;


        return $this->render('NavicuInfrastructureBundle:AAVV/editUserPermissions:index.html.twig',
            array('data' => json_encode($data)));
    }

    public function createRoleAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

            $command = new CreateRoleCommand($data);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());

    }

    public function editRoleAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

            $command = new EditRoleCommand($data);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function deleteRoleAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

            $command = new DeleteRoleCommand($data);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());

    }

    public function editPermissionsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

            $command = new EditRolePermissionsCommand($data);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());


    }

    public function getUsersAction()
    {
        $command = new GetUsersCommand();

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function editUsersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        //die(var_dump($data[0]));
        
        $command = new EditUsersCommand($data);

        //die(var_dump($command->getRequest()));

        $response = $this->get('CommandBus')->execute($command);



        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    public function deleteUserAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $command = new DeleteUserCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());

    }
}
