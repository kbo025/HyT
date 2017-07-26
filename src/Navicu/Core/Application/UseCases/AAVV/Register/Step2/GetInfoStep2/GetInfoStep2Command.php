<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step2\GetInfoStep2;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para devolver la información necesaria para el manejo del paso
 * 2 de registro de AAVV.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetInfoStep2Command implements Command
{

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
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
        return [];
    }
}