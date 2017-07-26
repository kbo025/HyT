<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVStagingAdditionalQouta
 */
class AAVVStagingAdditionalQouta
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $oldamount;

    /**
     * @var integer
     */
    private $newamount;

    /**
     * @var integer
     */
    private $targetid;

    /**
     * @var \DateTime
     */
    private $valid_since;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $createdBy;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $updatedBy;


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
     * Set oldamount
     *
     * @param integer $oldamount
     * @return AAVVStagingAdditionalQouta
     */
    public function setOldamount($oldamount)
    {
        $this->oldamount = $oldamount;

        return $this;
    }

    /**
     * Get oldamount
     *
     * @return integer 
     */
    public function getOldamount()
    {
        return $this->oldamount;
    }

    /**
     * Set newamount
     *
     * @param integer $newamount
     * @return AAVVStagingAdditionalQouta
     */
    public function setNewamount($newamount)
    {
        $this->newamount = $newamount;

        return $this;
    }

    /**
     * Get newamount
     *
     * @return integer 
     */
    public function getNewamount()
    {
        return $this->newamount;
    }

    /**
     * Set targetid
     *
     * @param integer $targetid
     * @return AAVVStagingAdditionalQouta
     */
    public function setTargetid($targetid)
    {
        $this->targetid = $targetid;

        return $this;
    }

    /**
     * Get targetid
     *
     * @return integer 
     */
    public function getTargetid()
    {
        return $this->targetid;
    }

    /**
     * Set valid_since
     *
     * @param \DateTime $validSince
     * @return AAVVStagingAdditionalQouta
     */
    public function setValidSince($validSince)
    {
        $this->valid_since = $validSince;

        return $this;
    }

    /**
     * Get valid_since
     *
     * @return \DateTime 
     */
    public function getValidSince()
    {
        return $this->valid_since;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVStagingAdditionalQouta
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return AAVVStagingAdditionalQouta
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return AAVVStagingAdditionalQouta
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param integer $updatedBy
     * @return AAVVStagingAdditionalQouta
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    /**
     * @var boolean
     */
    private $applied;


    /**
     * Set applied
     *
     * @param boolean $applied
     * @return AAVVStagingAdditionalQouta
     */
    public function setApplied($applied)
    {
        $this->applied = $applied;

        return $this;
    }

    /**
     * Get applied
     *
     * @return boolean 
     */
    public function getApplied()
    {
        return $this->applied;
    }
}
