<?php
namespace Navicu\InfrastructureBundle\Controller\WebService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zend\Serializer\Adapter\Json;

class DefaultController extends Controller
{
    /**
     * Rederizacion de la vista para la peticion del token
     * @return Response
     */
    public function renderViewRequestOauthTokenAction()
    {
        $user = $this->get('SessionService')->userOwnerSession()->getUser();
        $client = $this->container->get('nvc_rest_api.create_auth_user')->CreateSearchOAuthUser($user->getEmail());

        if (is_null($client))
            return new Response("User credentials not created", 400);

        return $this->render('NavicuInfrastructureBundle:WebService/RestApi:requestToken.html.twig');

        //Verificamos que el usuario tenga sus credenciales activas
        /*$clientRegistered = $this->container
            ->get('nvc_rest_api.validate_auth_user')
            ->existOAuthUser($user);

        // Si las credenciales para ese usuario ya han vencido se crea unas nuevas credenciales
        if ( is_null($clientRegistered) )
            $clientRegistered = $this->container->get('nvc_rest_api.create_auth_user')->CreateSearchOAuthUser($user->getEmail());

        if (get_class($clientRegistered) === 'Exception')
            return new JsonResponse("In file: ".$clientRegistered->getFile(). "  " . $clientRegistered->getMessage(), 500);

        return new JsonResponse(['publicId' => $clientRegistered->getPublicId(), 'secret' => $clientRegistered->getSecret()], 200);*/
    }

    /**
     * Funcion encargada de crear un cliente para hacer peticiones a la api y devolver el token
     *
     * @param Request $request
     * @return JsonResponse array 'publicId', 'secret'
     */
    public function requestCredentialsAction( Request $request)
    {
        // De la session sacamos el email
        $user = $this->get('SessionService')->userOwnerSession()->getUser();

        //Verificamos que el usuario tenga sus credenciales activas
        $clientRegistered = $this->container
            ->get('nvc_rest_api.validate_auth_user')
            ->existOAuthUser($user);

        // Si las credenciales para ese usuario ya han vencido se crea unas nuevas credenciales
        if ( is_null($clientRegistered) )
            $clientRegistered = $this->container->get('nvc_rest_api.create_auth_user')->CreateSearchOAuthUser($user->getEmail());

        if (get_class($clientRegistered) === 'Exception')
            return new JsonResponse("In file: ".$clientRegistered->getFile(). "  " . $clientRegistered->getMessage(), 500);

        return new JsonResponse(['publicId' => $clientRegistered->getPublicId(), 'secret' => $clientRegistered->getSecret()], 200);
        /*
        // Le creamos al usuario el public y el secret
        $client = $this->container->get('nvc_rest_api.create_auth_user')->CreateSearchOAuthUser($user->getEmail());

        // La session de este usuario ha expirado
        if (is_null($client))
            return new JsonResponse("User credentials not created", 400);

        return new JsonResponse(['publicId' => $client->getPublicId(), 'secret' => $client->getSecret()], 201);
        /*$token = $this->AuthUser($client, $user);
        return new JsonResponse($token['access_token'],200);*/
    }
}
