<?php

namespace Navicu\Rest\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Swagger\Annotations;

class DefaultController extends Controller
{

    public function IndexAction($name)
    {
        return new JsonResponse(['msg' => 'hello '.$name]);
    }

    /**
     * Funcion encargada de recibir las credenciales para generar un token desde afuera de la red
     *
     * @param $client_id string
     * @param $client_secret string
     * @return JsonResponse
     */
    public function credentialsRequestTokenAction($client_id, $client_secret)
    {
        //Verificamos que el usuario tenga sus credenciales activas
        $clientRegistered = $this->container
            ->get('nvc_rest_api.validate_auth_user')
            ->getOAuthClient($client_id, $client_secret);

        if (is_null($clientRegistered))
            return new JsonResponse(['data' => "This credentials are not more active nor been create, request a new one"], 401);

        $oldToken = $this->container
            ->get('nvc_rest_api.validate_auth_user')
            ->expiredToken($clientRegistered);

        // Si el token aun tiene vigencia devolvemos el mismo token
        if (!is_null($oldToken))
            return new JsonResponse(['access_token' => $oldToken], 200);

        $data = $this->generateToken($clientRegistered);

        $token = $this->container
            ->get('nvc_rest_api.validate_auth_user')
            ->associateTokenWithClient($data['access_token'], $clientRegistered);

        if (!$token)
            return new JsonResponse(['data' => "This token are not more active nor been create, request a new one"], 401);

        return new JsonResponse($data['access_token'], 200);
    }

    /**
     * Funcion encargada de generar un token
     *
     * @param $clientRegistered
     * @return mixed
     */
    public function generateToken($clientRegistered)
    {
        $petition = curl_init();
        $url =  $this->generateUrl('fos_oauth_server_token',
                [
                    'client_id' => $clientRegistered->getPublicId(),
                    'client_secret' => $clientRegistered->getSecret(),
                    'grant_type' => 'client_credentials'
                ],true);

        curl_setopt($petition, CURLOPT_URL, $url);
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($petition, CURLOPT_RETURNTRANSFER, true);
        // Indicamos que el resultado lo devuelva curl_exec() por medio de la variable $petition
        return json_decode(utf8_decode(curl_exec($petition)), true);
    }

    /**
     * Obtener el slug de tu establicimiento o la lista de slug de los establecimientos a los que tienes acceso     
     *
     * ### Respuesta Ok (e.g.) ###
     *
     *     {
     *       "success": true,
     *       "msg":"Ok",
     *       "data": ["slug-1","slug-2","slug-3"]
     *     }
     *
     * @ApiDoc(
     *  resource=false,
     *  description="Obtener el slug o lista de slug de los establecimientos a los cuales tiene acceso",
     *  tags={
     *    "BETA" = "#FF6F00"
     *  },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"=true,
     *          "description"="Token de acceso otorgado para conectarse a la api navicu (eg. Authorization: Bearer N2Q5NjJiYjFishsh22838232gfñk43HNUF982MYjNdsa2236dh7iNzYzYTAwYTg4ZWNl )"
     *      }
     *  },
     *  statusCodes={
     *    200="Lectura Correcta",
     *    401="No authorizado",
     *    403="No authorizado para acceder al recurso"
     *  }
     * )
     */
    public function getSlugAction()
    {
        $tokenManager = $this->container->get('fos_oauth_server.access_token_manager.default');
        $token = $this->container->get('security.context')->getToken()->getToken(); 
        $accessToken = $tokenManager->findTokenByToken($token);
        $owner = $accessToken->getUser()->getOwnerProfile();

        $i = 0;
        $slugList = [];
        if (!is_null($owner)) {
            $properties = $owner->getProperties();
            while($i < count($properties)) {
                $slugList[] = $properties[$i]->getSlug();
                $i++;
            }

            if(!empty($slugList))
                return new JsonResponse([
                    'success' => true,
                    'msg' => 'ok',
                    'data' => $slugList
                ]);
        }

        return new JsonResponse(['success' => false,'msg' => 'forbidden'],403);
    }
}
