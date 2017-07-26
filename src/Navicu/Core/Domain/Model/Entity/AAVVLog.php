<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVLog
 */
class AAVVLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $aavv_id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $oldvalue;

    /**
     * @var string
     */
    private $newvalue;

    /**
     * @var integer
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
     * Set aavv_id
     *
     * @param integer $aavvId
     * @return AAVVLog
     */
    public function setAavvId($aavvId)
    {
        $this->aavv_id = $aavvId;

        return $this;
    }

    /**
     * Get aavv_id
     *
     * @return integer 
     */
    public function getAavvId()
    {
        return $this->aavv_id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return AAVVLog
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
     * Set field
     *
     * @param string $field
     * @return AAVVLog
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set oldvalue
     *
     * @param string $oldvalue
     * @return AAVVLog
     */
    public function setOldvalue($oldvalue)
    {
        $this->oldvalue = $oldvalue;

        return $this;
    }

    /**
     * Get oldvalue
     *
     * @return string 
     */
    public function getOldvalue()
    {
        return $this->oldvalue;
    }

    /**
     * Set newvalue
     *
     * @param string $newvalue
     * @return AAVVLog
     */
    public function setNewvalue($newvalue)
    {
        $this->newvalue = $newvalue;

        return $this;
    }

    /**
     * Get newvalue
     *
     * @return string 
     */
    public function getNewvalue()
    {
        return $this->newvalue;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return AAVVLog
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }
    /**
     * @var string
     */
    private $entity;

    /**
     * @var integer
     */
    private $entity_id;


    /**
     * Set entity
     *
     * @param string $entity
     * @return AAVVLog
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set entity_id
     *
     * @param integer $entityId
     * @return AAVVLog
     */
    public function setEntityId($entityId)
    {
        $this->entity_id = $entityId;

        return $this;
    }

    /**
     * Get entity_id
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entity_id;
    }
    /**
     * @var string
     */
    private $type;


    /**
     * Set type
     *
     * @param string $type
     * @return AAVVLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
