<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\StatusChangeUsers;

use Navicu\Core\Application\Contract\Command;

/**
 * Conjunto de objetos para eliminar los valores ingresados del usuario por perteneciente al caso de uso deleteusers
 * @author	Mary sanchez
 * @version 15-06-2016
 */
class StatusChangeUsersCommand implements Command
{

    private $id;



    /**
     * EditUserCommand constructor.
     * @param $data
     */
    public function __construct( $id)
    {
        //$this->data = $data;
        $this->id= $id;
    }
    /**
     *  metodo getRequest devuelve un array con los parametros del command
     * @author Mary sanchez <msmarycarmen@gmail.com>
     * @version 15-06-2016
     * @return  Array
     */
    public function getRequest()
    {
        return
            array(
                //'data'=>$this->data,
                'id' => $this->id
            );
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}