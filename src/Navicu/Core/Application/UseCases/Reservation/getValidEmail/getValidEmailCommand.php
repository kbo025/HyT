<?php

namespace Navicu\Core\Application\UseCases\Reservation\getValidEmail;

use Navicu\Core\Application\Contract\Command;

/**
 * Clase para ejecutar el caso de uso getValidEmail
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */

class getValidEmailCommand implements Command
{
    /**
     * @var string
     */
    protected $data;

    /**
     * 	constructor de la clase
     *	@param $data Array
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getRequest()
    {
        return $this->data;
    }
}