<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetListReservation;

use Navicu\Core\Application\Contract\Command;

/**
 * Comando para buscar un listado de reservas dado un conjunto
 * de parametros
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetListReservationCommand implements Command
{
    /**
     * @var string          Conjunto de palabras usada para la busqueda
     */
    protected $search;

    /**
     * @var integer         El estatus de la reservas a buscar.
     */
    protected $status;

    /**
     * @var date            Fecha inicial para el rango de las reservas
     */
    protected $startDate;

    /**
     * @var date            Fecha final para el rango de las reservas
     */
    protected $endDate;

    /**
     * @var date            El tipo de busqueda por fecha: 1. Creada, 2. checkIn, 3.checkOut
     */
    protected $date_type;

    /**
     * @var object          Objeto usuario usado para identiicar la AAVV
     */
    protected $user;

    /**
     * @var object          Slug de la AAVV
     */
    protected $slug;

    /**
     * @var object          Parametro de ordenamiento
     */
    protected $orderBy;

    /**
     * @var object          Tipo de ordenamiento "DESC || ASC"
     */
    protected $orderType;

    /**
     * @var object          Parametro para el manejo del numero de pagina.
     */
    protected $page;

    /**
     *	Constructor de la clase
     *
     * 	@param Array $data
     */
    public function __construct($data)
    {
        $this->search = $data["search"] == "" ? null : $data["search"];
        $this->status = isset($data["status"]) ? $data["status"] : null;
        $this->date_type = isset($data["dateType"]) ? $data["dateType"] : null;
        $this->user = $data["user"];
        $this->slug = isset($data["slug"]) ? $data["slug"] : null;
        $this->orderBy = isset($data["orderBy"]) ? $this->parseOrderBy($data["orderBy"]) : "id";
        $this->orderType = isset($data["orderType"]) ? $data["orderType"] : "DESC";
        $this->page = isset($data["page"]) ? $data["page"] : 1;

        $this->startDate = isset($data["startDate"]) ? $data["startDate"] : null;
        $this->endDate = isset($data["endDate"]) ? $data["endDate"] : null;
    }

    /**
     * Esta Función es usada para pasar parametros de busqueda
     * para el manejo de indice.
     *
     * @param string $orderBy
     * @return string
     */
    public function parseOrderBy($orderBy)
    {
        switch($orderBy) {
            case "publicid":
                $orderBy = "public_id";
                break;
            case "createdate":
                $orderBy = "create_date";
                break;
            case "checkin":
                $orderBy = "check_in";
                break;
            case "checkout":
                $orderBy = "check_out";
                break;
            case "fullname":
                $orderBy = "client_name";
                break;
            case "price":
                $orderBy = "total_to_pay";
                break;
            case "property":
                $orderBy = "name_property";
                break;
            default:
                $orderBy = "id";
                break;
        }

        return $orderBy;
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return array(
            'search'=>$this->search,
            'status'=>$this->status,
            'dateType'=>$this->date_type,
            'startDate'=>$this->startDate,
            'endDate'=>$this->endDate,
            'user'=>$this->user,
            'slug'=>$this->slug,
            'orderBy'=>$this->orderBy,
            'orderType'=>$this->orderType,
            'page'=>$this->page
        );
    }
}
