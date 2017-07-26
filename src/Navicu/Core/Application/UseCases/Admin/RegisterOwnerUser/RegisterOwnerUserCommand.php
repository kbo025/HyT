<?php
namespace Navicu\Core\Application\UseCases\Admin\RegisterOwnerUser;

use Navicu\Core\Application\Contract\Command;


class RegisterOwnerUserCommand implements Command
{
    /**
     * El slug que representa al usuario tempownwer
     *
     * @var $slug
     */
    protected $slug;

    /**
     * Representa el codigo publico asignado al usuario
     *
     * @var $publicId
     */
    protected $publicId;

    /**
     * Representa el email a donde se enviará la confirmación
     *
     * @var $email
     */
    protected $email;

	public function __construct($slug)
	{
        $this->slug = $slug;
	}

	public function getRequest()
	{
        return array(
            'slug' => $this->slug,
            'codeProperty' => $this->publicId,
            'emailOwner' => $this->email
        );
	}

    public function get($att)
    {
        return ( isset($this->$att) ? $this->$att : null );
    }

    /**
     * Metodo set del atributo $publicId
     * @param $publicId
     */
    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    /**
     * Metodo get del atributo $publicId
     * @param $publicId
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * Metodo set del atributo $email
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Metodo get del atributo $email
     * @param $email
     */
    public function getEmail()
    {
        return $this->email;
    }
}