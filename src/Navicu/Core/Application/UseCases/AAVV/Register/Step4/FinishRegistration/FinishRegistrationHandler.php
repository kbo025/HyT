<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\FinishRegistration;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Model\Entity\AAVV;

class FinishRegistrationHandler implements  Handler
{

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $rpAAVV = $rf->get('AAVV');

        $aavv = $rpAAVV->findOneByArray(['slug' => $command->get('slug')]);

        if ($aavv) {

            CoreSession::setFinishRegistrationAAVV();
            $validationResponse = ValidateRegistrationHandler::getValidations($aavv,$rf);
            //$aavv->setRegistrationDate(new \DateTime('now'));
            $aavv->setStatusAgency(AAVV::STATUS_COMPLETED_REGISTRATION);

            $validationData = $validationResponse->getData();
            if (empty($validationData)) {
                $rpAAVV->save($aavv);
                return new ResponseCommandBus(201,'ok');
            } else {
                return new ResponseCommandBus(400, 'validation_error', $validationData);
            }
        } else {
            return new ResponseCommandBus(404,'');
        }
    }
}
