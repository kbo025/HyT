<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\SetAAVVAgreement;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVAgreement;
use Navicu\Core\Application\UseCases\AAVV\Register\Step1\UploadDocument\UploadDocumentCommand;

class SetAAVVAgreementHandler implements Handler
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
        $data = $command->get('form')['registrationDataStep4'];
        $contract = $command->get('form')['contract'];
        $contractName = $command->get('form')['contractName'];
        //die(var_dump($data));
        $rpAAVV = $rf->get('AAVV');
        $rpAgreement = $rf->get('AAVVAgreement');

        $aavv = $rpAAVV->findOneByArray(['slug' => $command->get('slug')]);

        if ($aavv) {
            $agreement = $aavv->getAgreement();
            if($agreement) {
                if ($data['accepted']) {
                    $agreement
                        ->setDate(new \DateTime())
                        ->setClientIpAddress($command->get('ip'))
                        ->setAavv($aavv);

                    if (true/*el usuario es admin*/) {
                        $agreement
                            ->setCreditDays($data['creditDays'])
                            ->setDiscountRate($data['discountRate']);
                    }

                    $rpAgreement->save($agreement);

                } else {
                    $rpAgreement->delete($agreement);
                }
            } else {
                if ($data['accepted']) {
                    $agreement = new AAVVAgreement();
                    $agreement
                        ->setDate(new \DateTime())
                        ->setClientIpAddress($command->get('ip'))
                        ->setAavv($aavv);

                    if (true/*el usuario es admin*/) {
                        $agreement
                            ->setCreditDays($data['creditDays'])
                            ->setDiscountRate($data['discountRate']);
                    }

                    $rpAgreement->save($agreement);
                }
            }
            return new ResponseCommandBus(201,'ok');
        } else {
            return new ResponseCommandBus(404,'');
        }
    }
}
