<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AdvanceSection;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class AdvanceSectionHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas 
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $request = $command->getRequest();
        $tempowner_repository = $rf->get('TempOwner');

        $tempowner = $tempowner_repository->findOneByArray(['slug'=>$request['slug']]);

        if (!empty($tempowner)) {
            $tempowner->setLastsec(7);
            $tempowner->setProgress(6,1);
            $tempowner->setProgress(7,1);
            $tempowner_repository->save( $tempowner );
            return new ResponseCommandBus(201,'OK');
        }

        return new ResponseCommandBus(401,'Unauthorized');
    }
}