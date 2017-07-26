<?php

namespace Navicu\Rest\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * OAuthRefreshToken
 */
class OAuthRefreshToken extends BaseRefreshToken
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    protected $user;

    /**
     * @var \Navicu\Rest\ApiBundle\Entity\OAuthClient
     */
    protected $oauth_client;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return OAuthRefreshToken
     */
    /*public function setUser(Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }*/

    /**
     * Get user
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set oauth_client
     *
     * @param \Navicu\Rest\ApiBundle\Entity\OAuthClient $oauthClient
     * @return OAuthRefreshToken
     */
    public function setOauthClient(\Navicu\Rest\ApiBundle\Entity\OAuthClient $oauthClient = null)
    {
        $this->oauth_client = $oauthClient;

        return $this;
    }

    /**
     * Get oauth_client
     *
     * @return \Navicu\Rest\ApiBundle\Entity\OAuthClient 
     */
    public function getOauthClient()
    {
        return $this->oauth_client;
    }
}
