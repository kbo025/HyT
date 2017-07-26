<?php
namespace Navicu\Core\Application\UseCases\Admin\ReservationModule\ReservationList;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando es usado para listar las reservas del sistema dado un usuario Admin
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ReservationListCommand extends CommandBase implements Command
{
    /**
     * @var string          Tipo de lista usuario
     */
    protected  $status;

    /**
     * @var string          Conjunto de palabras usada para la busqueda
     */
    protected $search;

    /**
     * @var object          Parametro de ordenamiento por "parametro a ordenar"
     */
    protected $orderBy;

    /**
     * @var object          Tipo de ordenamiento "DESC || ASC"
     */
    protected $orderType;

    /**
     * @var integer         Manejo de nuemero de pagina.
     */
    protected $page;

    /**
     * @var integer         item por pagina
     */
    protected $itemsPerPage;

    /**
     * Constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->page = isset($data["page"]) ? $data["page"] : 1;
        $this->status = isset($data["reservationStatus"]) ? $data["reservationStatus"] : null;
        $this->search = isset($data["search"]) ? $data["search"] : null;
        $this->orderBy = isset($data["orderBy"]) ? $data["orderBy"] : null;
        $this->orderType = isset($data["orderType"]) ? $data["orderType"] : null;
        $this->itemsPerPage = isset($data["itemsPerPage"]) ? $data["itemsPerPage"] : null;
    }
}
