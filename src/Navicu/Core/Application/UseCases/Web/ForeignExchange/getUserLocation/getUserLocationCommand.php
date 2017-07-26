<?php
namespace Navicu\Core\Application\UseCases\Web\ForeignExchange\getUserLocation;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para devolver si la ip de un usuario es de Venezuela o no.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getUserLocationCommand implements Command
{
    /**
     * @var string $ip		IP de un usuario.
     */
    protected $ip;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->ip = $data["ip"];
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return [
            "ip" => $this->ip
        ];
    }

}