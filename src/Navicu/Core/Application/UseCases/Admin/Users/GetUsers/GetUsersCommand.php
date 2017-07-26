<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\GetUsers;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;
/**
 * Comando es usado para listar los usuarios del sistema
 * dado un conjunto de parametros de busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetUsersCommand extends CommandBase implements Command
{
    /**
     * @var string          Tipo de lista usuario
     */
    protected  $role;

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
     * Constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->page = isset($data["page"]) ? $data["page"] : 1;
        $this->role = isset($data["role"]) ? $data["role"] : 0;
        $this->search = isset($data["search"]) ? $data["search"] : null;
        $this->orderBy = isset($data["orderBy"]) ? $data["orderBy"] : null;
        $this->orderType = isset($data["orderType"]) ? $data["orderType"] : null;

    }
}