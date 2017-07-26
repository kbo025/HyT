<?php
namespace Navicu\Core\Application\UseCases\Admin\TransferReservationExpiration;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso TransferReservationExpiration.
 *
 * Class TransferReservationExpirationCommand
 * @package Navicu\Core\Application\UseCases\Admin\TransferReservationExpiration
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class TransferReservationExpirationCommand extends CommandBase implements Command
{
    /**
     * @var Fecha Actual.
     */
    protected $dateNow;

    /**
     * Constructor de la clase
     * @param $slug
     */
    public function __construct()
    {
        $this->dateNow = new \DateTime(date("Y-m-d H:m:s"));
    }

    public function getRequest()
    {
        return
            array(
                'dateNow' => $this->dateNow->format("Y-m-d H:m:s")
        );
    }
}