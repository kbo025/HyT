<?php

namespace Navicu\InfrastructureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DisableAdvance
 */
class DisableAdvance
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    private $deactiveBy;

    /**
     * @var \Navicu\InfrastructureBundle\Entity\User
     */
    private $user_id;


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
     * Set date
     *
     * @param \DateTime $date
     * @return DisableAdvance
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return DisableAdvance
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set deactiveBy
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $deactiveBy
     * @return DisableAdvance
     */
    public function setDeactiveBy(\Navicu\InfrastructureBundle\Entity\User $deactiveBy = null)
    {
        $this->deactiveBy = $deactiveBy;

        return $this;
    }

    /**
     * Get deactiveBy
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getDeactiveBy()
    {
        return $this->deactiveBy;
    }

    /**
     * Set user_id
     *
     * @param \Navicu\InfrastructureBundle\Entity\User $userId
     * @return DisableAdvance
     */
    public function setUserId(\Navicu\InfrastructureBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return \Navicu\InfrastructureBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
