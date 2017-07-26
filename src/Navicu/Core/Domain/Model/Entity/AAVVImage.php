<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\AAVVProfile;

use Doctrine\ORM\Mapping as ORM;

/**
 * AAVVProfile
 *
 * La entidad representa el conjunto de perfiles del usuario
 */
class AAVVImage
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

    private $image_document;




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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AAVVImage
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
     * @return AAVVImage
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
     * @return AAVVImage
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
     * @return AAVVImage
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
     * Set image_document
     *
     * @param \Navicu\Core\Domain\Model\Entity\Document $imageDocument
     * @return AAVVImage
     */
    public function setImageDocument(\Navicu\Core\Domain\Model\Entity\Document $imageDocument = null)
    {
        $this->image_document = $imageDocument;

        return $this;
    }

    /**
     * Get image_document
     *
     * @return \Navicu\Core\Domain\Model\Entity\Document 
     */
    public function getImageDocument()
    {
        return $this->image_document;
    }

    /**
     * Set aavv
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVV $aavv
     * @return AAVVImage
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
