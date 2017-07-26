<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\Search\GetListAffiliatedAAVV;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Caso de uso para retornar un listado de agencias de viajes afiliadas a
 * la web.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetListAffiliatedAAVVCommand extends CommandBase implements Command
{
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
     * @var integer Manejo de nuemero de pagina.
     */
    protected $page;

}
