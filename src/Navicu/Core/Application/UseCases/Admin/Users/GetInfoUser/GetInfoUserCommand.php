<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\GetInfoUser;

use Navicu\Core\Application\Contract\Command;

/**
 *  Método Devuelve la info necesario para crear usuarios o editar un usuario
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 09-06-2016
 * @return  Array
 */

class GetInfoUserCommand implements Command
{
    /**
     * @var integer representa el tipo de rol del usuario
     */
    private $role;

    /**
     * @var integer id del usuario a buscar
     */
    private $userId;


    /**
     * CreateUsersCommand constructor.
     *
     * Si el userId es nulo quiere decir que esta editando un usuario
     */
    public function __construct($role = null,$userId = null)
    {
        $this->role = $role;
        $this->userId = $userId;
    }

    public function getRequest()
    {
        return [
            'role' => $this->role,
            'userId' => $this->userId
        ];
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
}