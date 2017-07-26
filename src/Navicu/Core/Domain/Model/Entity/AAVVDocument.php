<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\AAVVProfile;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVProfile
 *
 * La entidad representa el conjunto de perfiles del usuario
 */
class AAVVDocument
{
	/**
     * @var integer
     */
    private $id;

    /**
     * @var date
     */
    private $createdAt;
    /**
     * @var integer
     */
    private $createdBy;
    /**
     * @var date
     */
    private $updatedAt;
    /**
     * @var integer
     */
    private $updatedBy;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVVImage
     */
    private $aavv;

    private $document;

    private $type;




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
     * Set type
     *
     * @param string $type
     * @return AAVVDocument
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVDocument
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
     * @return AAVVDocument
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
     * @return AAVVDocument
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
     * @return AAVVDocument
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
     * Set document
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $document
     * @return AAVVDocument
     */
    public function setDocument(\Navicu\Core\Domain\Model\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVDocument
     */
    public function setAavv(\Navicu\Core\Domain\Model\Entity\AAVV $aavv = null)
    {
        $this->aavv = $aavv;

        return $this;
    }

    /**
     * Get aavv
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVV 
     */
    public function getAavv()
    {
        return $this->aavv;
    }
}
