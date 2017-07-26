<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\NewEditUser;

use Navicu\Core\Application\Contract\Command;

/**
 * La clase se encarga de crear o editar un usuario en el sistema
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 31/05/2016
 */
class NewEditUserCommand implements Command
{
    /**
     * Envia la data que se va actulaizar por perfiles
     * @var string
     */
    private $data;

    /**
     * CreateUsersCommand constructor.
     */
    public function __construct($data)
    {
        $this->data= $data;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return[
            'data'=>$this->data
            ];
    }



}