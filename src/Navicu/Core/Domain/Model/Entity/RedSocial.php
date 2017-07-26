<?php

namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\ClientProfile;

/**
 * Clase RedSocial
 *
 * representa los datos de red sociales de un perfil de usuario
 *
 * @author Carlos Aguilera <ceaf.21@gmail.com>
 * @author Currently Working: Carlos Aguilera <ceaf.21@gmail.com>
 */
class RedSocial
{

    /**
     * @var integer
     */
    private $id;

    /**
     * id del cliente en la red social
     * @var string
     */
    private $id_social;

    /**
     * nombre del tipo de red social
     * @var string
     */
    private $type;

    /**
     * link del cliente en la red social
     * @var string
     */
    private $link;

    /**
     * link de la foto del cliente en la red social
     * @var string
     */
    private $photo;

    /**
     * Rango de edad del cliente en la red social
     * @var integer
     */
    private $age_range;

    /**
     * @var integer
     */
    private $client;


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
     * Set id_social
     *
     * @param string $idSocial
     * @return RedSocial
     */
    public function setIdSocial($idSocial)
    {
        $this->id_social = $idSocial;

        return $this;
    }

    /**
     * Get id_social
     *
     * @return string 
     */
    public function getIdSocial()
    {
        return $this->id_social;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return RedSocial
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
     * Set link
     *
     * @param integer $link
     * @return RedSocial
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return integer 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return RedSocial
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set age_range
     *
     * @param integer $ageRange
     * @return RedSocial
     */
    public function setAgeRange($ageRange)
    {
        $this->age_range = $ageRange;

        return $this;
    }

    /**
     * Get age_range
     *
     * @return integer 
     */
    public function getAgeRange()
    {
        return $this->age_range;
    }

    /**
     * Set client_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clientId
     * @return RedSocial
     */
    public function setClient(\Navicu\Core\Domain\Model\Entity\ClientProfile $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\ClientProfile 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * La función actualiza los datos de una RedSocial, dado un array ($data).
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Array $data
     * @return void
     */ 
    public function updateObject($data, &$client)
    {
        $this->setIdSocial($data["idSocial"]);
        $this->setType($data["type"]);
        $this->setLink($data["link"]);
        $this->setPhoto("undefined");
        $this->setAgeRange(0);
        $this->setClient($client);
        $client->addSocial($this);

        return $this;
    }
}
