<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\RegisterUser;

use Navicu\Core\Application\Contract\Command;

/**
 *
 */
class RegisterUserCommand implements Command
{
    /**
     * @var string nombre de usuario
     */
    private $username;

    /**
     * @var integer tipo de rol
     */
    private $role;

    /**
     * @var string nombre y apellido del usuario
     */
    private $full_name;

    /**
     * @var string contraseña
     */
    private $password;

    /**
     * @var integer cedula de identidad
     */
    private $identity_card;

    /**
     * @var contraseña del usuario
     */
    private $email;

    /**
     * Constructor de la clase
     *
     */
    public function __construct()
    {

    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
       return [
           'userName' => $this->username,
           'role' => $this->role,
           'fullName' => $this->full_name,
           'password' => $this->password,
           'identityCard' => $this->identity_card,
           'email' => $this->email
       ];
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param int $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * @param string $full_name
     */
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getIdentityCard()
    {
        return $this->identity_card;
    }

    /**
     * @param int $identity_card
     */
    public function setIdentityCard($identity_card)
    {
        $this->identity_card = $identity_card;
    }

    /**
     * @return contraseña
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param contraseña $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}