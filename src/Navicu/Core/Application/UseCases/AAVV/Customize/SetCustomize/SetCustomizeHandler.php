<?php
namespace Navicu\Core\Application\UseCases\AAVV\Customize\SetCustomize;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Application\Contract\ResponseCommand;

/**
 * Clase para ejecutar el caso de uso SetCustomize
 * Edición de personalización de los datos de la AAVV
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/09/2016
 */
class SetCustomizeHandler implements Handler
{
    /**
     * @param Command $command
     * @param RepositoryFactoryInterface|null $rf
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        try {
            $response = [];
            $aavv = CoreSession::getUser()->getAavvProfile()->getAAVV();
            $request = $command->getRequest();
            $aavv->setCustomize($request['data']);
            CoreSession::set('customize',json_encode($request['data']));
            $rf->get('AAVV')->save($aavv);

            return new ResponseCommandBus(200, 'Ok', $response);

        } catch (\Exception $e) {
            return new ResponseCommandBus(500, 'Internal Error', $e->getMessage());
        }
    }
}
