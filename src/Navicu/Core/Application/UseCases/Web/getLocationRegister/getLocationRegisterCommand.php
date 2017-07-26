<?php
namespace Navicu\Core\Application\UseCases\Web\getLocationRegister;

use Navicu\Core\Application\Contract\Command;


/**
 * Comando para devolver las localidades "Pais" y "Estados" para el registro
 * de un usuario cliente.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getLocationRegisterCommand implements Command
{
    /**
     * @var Representa el codigo del pais a buscar
     */
    private $countryCode;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->countryCode = $data["code"] ? $data["code"] : "VE";
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return ['countryCode' => $this->countryCode];
    }

}