<?php
/**
 * Created by PhpStorm.
 * User: isa
 * Date: 29/05/17
 * Time: 01:27 PM
 */

namespace Navicu\Rest\ApiBundle\Resources\Services;


class ValidateOAuthUser
{
    private $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Funcion encargada de devolver las credenciales del usuario para enviar la peticion al token
     *
     * @param $fos_user
     * @return int
     */
    public function existOAuthUser($fos_user)
    {
        $container = $this->container;
        // si el token aun existe no se crea uno nuevo
        $repoClient = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthClient');
        $repoAuthCode = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAuthCode');

        $auth_user = $repoClient->findOneBy(['name' => $fos_user->getEmail()]);
        // Si el usuario no tiene credenciales creadas
        if (is_null($auth_user))
            return null;

        $deactive = $this->expiredCredentials($auth_user);

        // Si no ha expirado ese token
        if ( !$deactive )
            return $auth_user;
        // Si ya ha expirado hay que renovar las credenciales y el usuario
        return null;
//        return $auth_user;
    }

    /**
     * Funcion para asociar el oAuthClient al token generado
     *
     * @param $token
     * @param $authClient
     * @return bool
     */
    public function associateTokenWithClient($token, $authClient)
    {
        $container = $this->container;
        $repoAccessToken = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAccessToken');
        $repoAuthCode = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAuthCode');

        $oauthCode = $repoAuthCode->findOneBy(['oauth_client' => $authClient->getId()]);
        $fos_user = $oauthCode->getUser();

        $accessToken = $repoAccessToken->findOneBy(['token' => $token]);

        if (!$accessToken)
            return false;
        else {
            $accessToken->setUser($fos_user);
            $accessToken->setOauthClient($authClient);
            $codeExpires = new \DateTime("now");

            $newExpiredTime = strtotime($codeExpires->modify('+3 month')->format('Y-m-d'));
            $accessToken->setExpiresAt($newExpiredTime);

            $repoAccessToken->persistObject($accessToken);
            $repoAccessToken->flushObject();

            return true;
        }
    }

    /**
     * Funcion para obtener el objeto OAuthClient
     *
     * @param $client_id
     * @param $client_secret
     * @return mixed
     */
    public function getOAuthClient($client_id, $client_secret)
    {
        $repoClient = $this->container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthClient');

        $clientCredential = explode('_',$client_id);
        $userId = $clientCredential[1];
        $authClient = $repoClient->findOneBy(['randomId' => $userId, 'secret' => $client_secret], ['id' => 'desc']);

        if ( !$this->expiredCredentials($authClient) )
            return $authClient;
        else
            return null;
    }

    /**
     * Funcion para validar la vigencia de la credencial existente
     *
     * @param $authClient
     * @return bool
     */
    public function expiredCredentials($authClient)
    {
        $repoAuthCode = $this->container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAuthCode');
        $authCode = $repoAuthCode->findOneBy(['oauth_client' => $authClient->getId()],['expiresAt' => 'desc']);
        $codeExpires = $authCode->getExpiresAt();

        $codeExpires = date("Y-m-d H:i:s", $codeExpires);
        $codeExpires = new \DateTime($codeExpires);
        $now = new \DateTime('now');

        // Si no ha expirado ese token
        if ( $codeExpires->diff($now)->h == 0 )
            return false;
        // Si ya ha expirado hay que renovar las credenciales y el usuario
        return true;
    }

    /**
     * Funcion para verificar la vignencia del token
     * 
     * @param $authClient
     * @return null
     */
    public function expiredToken($authClient) {
        $container = $this->container;
        $repoAccessToken = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAccessToken');

        $accessToken = $repoAccessToken->findOneBy(['oauth_client' => $authClient->getId()],['expiresAt' => 'desc']);
        if(is_null($accessToken))
            return null;

        $codeExpires = $accessToken->getExpiresAt();

        /*$codeExpires = date("Y-m-d H:i:s", $codeExpires);
        $codeExpires = new \DateTime($codeExpires);
        $now = new \DateTime('now');*/

        // Si no ha expirado ese token
        //if ( $codeExpires->diff($now)->h == 0 )
        if ($codeExpires > strtotime(date('Y-m-d')))
            return $accessToken->getToken();
        // Si ya ha expirado hay que renovar las credenciales y el usuario
        return null;
    }
}