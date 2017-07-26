<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\GetAAVVAgreement;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;

class GetAAVVAgreementHandler implements Handler
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

            $validationResponse = ValidateRegistrationHandler::getValidations($aavv,$rf,4);

            $document = $aavv->getdocumentByType('CTR');

            if ($document)
                $contractName = $document->getDocument()->getName();
            else
                $contractName = null;

            $agreement = $aavv->getAgreement();
            if ($agreement) {
                $response = [
                    'data' => [
                        'date' => $agreement->getDate()->format('d-m-Y'),
                        'discountRate' => $agreement->getDiscountRate(),
                        'creditDays' => $agreement->getCreditDays(),
                        'accepted'=> true,
                        'contractName' => $contractName,
                    ],
                    'validations' => !empty($validationResponse->getData()),

                ];
            } else {
                $response = [
                    'data' => [
                        'date' => date('d-m-Y'),
                        'discountRate' => 0.1,
                        'creditDays' => 30,
                        'accepted'=> false,
                        'contractName' => $contractName,
                    ],
                    'validations' => !empty($validationResponse->getData()),

                ];
            }
            return new ResponseCommandBus(201,'ok',$response);
        } else {
            return new ResponseCommandBus(404,'');
        }
    }
}
