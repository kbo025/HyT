<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\AuthCode as BaseAuthCode;
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
     * @var \Navicu\Core\Domain\Model\Entity\OAuthClient
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
    /*public function setUser(\Navicu\InfrastructureBundle\Entity\User $user = null)
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
     * @param \Navicu\Core\Domain\Model\Entity\OAuthClient $oauthClient
     * @return OAuthAuthCode
     */
    public function setOauthClient(\Navicu\Core\Domain\Model\Entity\OAuthClient $oauthClient = null)
    {
        $this->oauth_client = $oauthClient;

        return $this;
    }

    /**
     * Get oauth_client
     *
     * @return \Navicu\Core\Domain\Model\Entity\OAuthClient 
     */
    public function getOauthClient()
    {
        return $this->oauth_client;
    }
}
