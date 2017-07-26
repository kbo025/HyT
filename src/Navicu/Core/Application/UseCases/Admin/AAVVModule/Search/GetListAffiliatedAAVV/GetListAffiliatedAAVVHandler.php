<?php
namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\Search\GetListAffiliatedAAVV;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Caso de uso para retornar un listado de agencias de viajes afiliadas a
 * la web.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetListAffiliatedAAVVHandler implements Handler
{
    private $rf;

    /**
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso.
     *
     * @param SetDataReservationCommand $command
     * @param RepositoryFactory $rf
     * @return ResponseCommandBus Object
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $request = $command->getRequest();
        $user = CoreSession::getUser();

        if(!is_object($user))
            return new ResponseCommandBus(400, 'Ok', null);

        $aavvs = $rf->get('AAVV')->findViewAffiliatedAAVV($request);

        return new ResponseCommandBus(200,'Ok', $aavvs);
    }
}
