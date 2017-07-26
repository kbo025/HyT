<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\EditUser;

use Navicu\Core\Application\Contract\Command;

/**
 * Conjunto de objetos para editar los valores ingresados del usuario por perteneciente al caso de uso NewUserEdit
 * @author	Mary sanchez
 * @version 09-06-2016
 */
class EditUserCommand implements Command
{

    private $data;

    /**
     * EditUserCommand constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;

    }
    /**
     *  metodo getRequest devuelve un array con los parametros del command
     * @author Mary sanchez <msmarycarmen@gmail.com>
     * @version 09-06-2016
     * @return  Array
     */
    public function getRequest()
    {
        return
            array(
                'data' => $this->data
            );
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}