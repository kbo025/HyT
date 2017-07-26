<?php

namespace Navicu\Rest\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;

/**
 * OAuthAuthCode
 */
class OAuthAuthCode extends BaseAuthCode
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
     * @return OAuthAuthCode
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
     * @return OAuthAuthCode
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

    /**
     * Set user
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $user
     * @return OAuthAuthCode
     */
    /*public function setUser(\Navicu\InfrastructureBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }*/
}
