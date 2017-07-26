<?php

namespace Navicu\Core\Application\UseCases\Reservation\setClientProfile;

use Navicu\Core\Application\Contract\Command;

/**
 * Clase para ejecutar el caso de uso setClientProfile
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */

class setClientProfileCommand implements Command
{
    /**
     * @var string
     */
    protected $data;

    protected $to_aavv;

    /**
     * 	constructor de la clase
     *	@param $data Array
     */
    public function __construct($data, $to_aavv = false)
    {
        $this->to_aavv = $to_aavv;
        $data["role"] = "ROLE_WEB";
        $this->data = $data;
    }

    public function getRequest()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    public function isToAavv()
    {
        return $this->to_aavv;
    }

    /**
     * @param boolean $to_aavv
     */
    public function setToAavv($to_aavv)
    {
        $this->to_aavv = $to_aavv;
    }
}
