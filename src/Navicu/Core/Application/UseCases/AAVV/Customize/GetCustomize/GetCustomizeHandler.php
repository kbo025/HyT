<?php
namespace Navicu\Core\Application\UseCases\AAVV\Customize\GetCustomize;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Clase para ejecutar el caso de uso GetCustomize
 * Obtener los datos personalizados
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 19/09/2016
 */
class GetCustomizeHandler implements Handler
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
            $response['customize'] =  $aavv->getCustomize();

            $documentLogo = $rf->get('AAVVDocument')->findOneBy(['aavv' => $aavv]);

            $response['logo'] = [];
            $response['logo']['imageUrl'] =
                $documentLogo ?
                '/uploads/images/images_original/'.$documentLogo->getDocument()->getFileName() :
                null;
            $response['logo']['nameImage'] = $documentLogo ?
                $documentLogo->getDocument()->getName(): null;

            return new ResponseCommandBus(200, 'Ok', $response);
        } catch (\Exception $e) {
            return new ResponseCommandBus(500, 'Internal Error', $e->getMessage());
        }
    }
}
