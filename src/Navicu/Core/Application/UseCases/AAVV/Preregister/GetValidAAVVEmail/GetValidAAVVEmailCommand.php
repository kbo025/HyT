<?php

namespace Navicu\Core\Application\UseCases\AAVV\Preregister\GetValidAAVVEmail;

use Navicu\Core\Application\Contract\Command;

/**
 * Clase para ejecutar el caso de uso getValidAAVVEmail
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 */

class GetValidAAVVEmailCommand implements Command
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