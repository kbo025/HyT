<?php
namespace Navicu\Core\Application\UseCases\Admin\GetBankList;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;

class GetBankListHandler implements Handler
{
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $bankTypeRepository = $rf->get('BankType');  
        $response = [
            'foreign' => $bankTypeRepository->getListBanksArray(2),
            'local' => $bankTypeRepository->getListBanksArray(1),
            'foreignReceiver' => $bankTypeRepository->getListBanksArray(2,true),
            'localReceiver' => $bankTypeRepository->getListBanksArray(1,true),
        ];
        return new ResponseCommandBus(201, 'OK', $response);
    }
}