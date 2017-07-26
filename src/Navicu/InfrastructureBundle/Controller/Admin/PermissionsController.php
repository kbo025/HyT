<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 13/02/17
 * Time: 03:58 PM
 */

namespace Navicu\InfrastructureBundle\Controller\Admin;

use Navicu\Core\Application\UseCases\Admin\GetRolesAndPermissions\GetRolesAndPermissionsCommand;
use Navicu\Core\Application\UseCases\Admin\Users\Admin\PermissionCreate\PermissionCreateCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Navicu\Core\Application\UseCases\Admin\EditRolesAndPermissions\EditRolesAndPermissionsCommand;
use Symfony\Component\HttpFoundation\JsonResponse;

class PermissionsController extends Controller
{
    /**
     * La siguiente función renderiza la pagina de roles y permisos
     * @return Response
     */
    public function rolesAndPermissionsPageAction()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Admin:permissions/permissions.html.twig'
        );
    }

    /**
     * La siguiente función lista los roles con sus permisos asociados
     * @return Response
     */
    public function listRolesAndPermissionsAction()
    {
        $command = new GetRolesAndPermissionsCommand();

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    /**
     * La siguiente función edita los permisos de acuerdo a la data recibida
     * @return Response
     */
    public function editRolesAndPermissionsAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);

        $command = new EditRolesAndPermissionsCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }

    /**
     * La siguiente función permite crear un role
     * @return Response
     */
    public function permissionsCreateAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);

        $command = new PermissionCreateCommand($data);

        $response = $this->get('CommandBus')->execute($command);

        return new JsonResponse($response->getArray(), $response->getStatusCode());
    }
}
