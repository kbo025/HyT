<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\GetUsers;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;


/**
 * Metodo usado para listar los usuarios del sistema
 * dado un conjunto de parametros de busqueda.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetUsersHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     * @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $response["users"] = [];
        $request = $command->getRequest();
        $repository = $rf->get($this->getRepository($request["role"]));
        $request['aditionalCriteria'] = $this->filterByPermissions();
        $response = $repository->findUsersByWords($request);
        $countUser = $rf->get($this->getRepository("User"))->findCountByUser();
        if ($countUser) {
            $response["total"] = $countUser["data"];
            $aux["role"] = "all";
            $aux["cantidad"] = array_sum(array_column($countUser["data"], "cantidad"));
            array_push($response["total"], $aux);
        }

        /*
         * Manejo de informaciòn adicional para usuarios Administradores.
         */
        if ($request["role"] == 2) {
            array_walk(
                $response["data"],
                function (&$e) {
                    $e["position"] = CoreTranslator::getTranslator(
                        $e["position"],
                        'departaments'
                    );
                    $e["departament"] = CoreTranslator::getTranslator(
                        $e["departament"],
                        'departaments'
                    );
                }
            );
        }

        return new ResponseCommandBus(200,'Ok', $response);
    }

    private function getRepository($rol) {

        switch ($rol) {
            case '1':
                $repo = "OwnerProfile";
                break;
            case '2':
                $repo = "NvcProfile";
                break;
            case '3':
                $repo = "ClientProfile";
                break;
            case '4':
                $repo = "AAVVProfile";
                break;
            case '0':
            default:
                $repo = "User";
                break;
        }

        return $repo;
    }

    private function filterByPermissions()
    {
        $filter = array();
        if (CoreSession::havePermissons('admin_users_client', 'read'))
            $filter[] = 'Cliente';

        if (CoreSession::havePermissons('admin_users_extranet', 'read'))
            $filter[] = 'Hotelero';

        if (CoreSession::havePermissons('admin_users_aavv', 'read'))
            $filter[] = 'AAVV';

        if (CoreSession::havePermissons('admin_users_admin', 'read'))
            $filter[] = 'Admin';

        return $filter;
    }
}