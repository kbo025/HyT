<?php

namespace Navicu\Rest\ApiBundle\Resources\Services;
use Herrera\Json\Exception\Exception;
use Navicu\Rest\ApiBundle\Entity\OAuthAuthCode;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: isa
 * Date: 29/05/17
 * Time: 08:56 AM
 */
class CreateAuthUser
{
    private $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $email string
     * @return array|\Exception
     * array body = ['publicId' => string, 'secret' => string];
     * @throws \Exception
     */
    public function CreateSearchOAuthUser($email)
    {
        $container = $this->container;

        $oauthServer = $container->get('fos_oauth_server.server');
        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $repo = $container->get('doctrine')->getRepository('NavicuInfrastructureBundle:User');
        $repoClient = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthClient');
        $repoAuthCode = $container->get('doctrine')->getRepository('NavicuRestApiBundle:OAuthAuthCode');
        $fos_user = $repo->findOneBy(['email' => $email]);

        if (is_null($fos_user))
            return new \Exception("No user registered", 500);

        try {
            $grantType = 'client_credentials';
            // TODO: Establecer la URI a la cual se le hara la redireccion
            $redirectUri = ["www.navicu.dev/app_dev.php/requestToken"];

            $client = $clientManager->createClient();
            $client->setName($fos_user->getEmail());
            $client->setRedirectUris($redirectUri);
            $client->setAllowedGrantTypes(["authorization_code", "password", "refresh_token", "token", "client_credentials"]);
            $clientManager->updateClient($client);

            $queryData['client_id'] = $client->getPublicId();
            $queryData['redirect_uri'] = $client->getRedirectUris()[0];
            $queryData['response_type'] = 'code';

            $authRequest = new Request($queryData);

            $oauthServer->finishClientAuthorization(true, $fos_user, $authRequest, $grantType);

            $lastAuthCode = $repoAuthCode->findOneBy(['user' => $fos_user], ['expiresAt' => 'desc']);
            $lastAuthCode->setOauthClient($client);
            $repoAuthCode->save($lastAuthCode);

            return $client;
        } catch (\Exception $exception) {
            return new \Exception($exception->getMessage(), 500);
        }
    }
}