<?php

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationCommand;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;

class ValidateRegistrationHandler implements Handler
{

    private $command;

    public static function getValidations(AAVV $aavv, RepositoryFactoryInterface $rf, $step = null)
    {
        $finish = CoreSession::get('finishRegistrationAAVV');
        $validationHandle = new ValidateRegistrationHandler();
        return $validationHandle->handle(
            new ValidateRegistrationCommand(['aavv' => $aavv, 'step' => $step, 'finish' => $finish]),
            $rf
        );
    }

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

        $this->command = $command;

        $aavv = is_string($command->get('aavv')) ?
            $rpAAVV->findOneByArray(['slug' => $command->get('aavv')]) :
            $command->get('aavv');

        if ($aavv instanceof AAVV) {

            $response = $command->get('finish') ? $this->validate($aavv) : [];

            if (empty($response))
                return new ResponseCommandBus(200, 'ok', $response);
            else
                return new ResponseCommandBus(400, 'badRequest', $response);

        } else {

            return new ResponseCommandBus(404,'not_found');

        }
    }

    private function validate($aavv)
    {
        $response = [];
        $step = $this->command->get('step');

        if (empty($step) || $step==1) {
            $step1 = $this->validateStep1($aavv);
            if(!empty($step1))
                $response['step1'] = $step1;
        }

        if (empty($step) || $step==2) {
            $step2 = $this->validateStep2($aavv);
            if(!empty($step2))
                $response['step2'] = $step2;
        }

        if (empty($step) || $step==3) {
            $step3 = $this->validateStep3($aavv);
            if(!empty($step3))
                $response['step3'] = $step3;
        }

        if (empty($step) || $step==4) {
            $step4 = $this->validateStep4($aavv);
            if (!empty($step4))
                $response['step4'] = $step4;
        }

        return $response;
    }

    private function validateStep1(AAVV $aavv)
    {
        $response = [];
        $users_errors = [];
        if(is_null($aavv->getSocialReason()))
            $response[] = 'social_reason_empty';

        if(is_null($aavv->getRif()))
            $response[] = 'rif_empty';

        if(is_null($aavv->getPhone()))
            $response[] = 'phone_empty';

        if(is_null($aavv->getCompanyEmail()))
            $response[] = 'empty_company_email';

        /*if(is_null($aavv->getMerchantId()))
            $response[] = 'empty_merchantId';*/

        /*if(is_null($aavv->getStatus()))
            $response[] = 'empty_status';*/

        if($aavv->getAavvAddress()->count() == 0)
            $response[] = 'empty_address';

        if(is_null($aavv->getCommercialName()))
            $response[] = 'empty_commercial_name';

        if(is_null($aavv->getOpeningYear()))
            $response[] = 'empty_opening_year';


        if($aavv->getAavvAddress()[0] != null) {
            if($aavv->getAavvAddress()[0]->getAddress() == null)
                $response[] = 'empty_address';
        }
        if ($aavv->getdocumentByType('RTN') == null)
            $response[] = 'empty_RTN';

        if ($aavv->getdocumentByType('LEASE') == null)
            $response[] = 'empty_LEASE';

        if (count($aavv->getPictures()) < 1)
            $response[] = 'missing_pictures';

        if ($aavv->getdocumentByType('LOGO') == null)
            $response[] = 'empty_LOGO';

        $profiles = $aavv->getAavvProfile();

        foreach ($profiles as $profile) {
            $user_errors = [];

            if(is_null($profile->getFullname()))
                $user_errors[] = 'empty_name';

            if(is_null($profile->getDocumentId()))
                $user_errors[] = 'empty_identity_document';

            if(is_null($profile->getPhone()))
                $user_errors[] = 'empty_phone';

            if(is_null($profile->getUser()->getEmail()))
                $user_errors[] = 'empty_email';

            if(is_null($profile->getLocation()))
                $user_errors[] = 'empty_address';

            if(!empty($user_errors))
                $users_errors[] = $user_errors;
        }

        if(!empty($users_errors))
            $response['user_errors'] = $users_errors;

        return $response;
    }

    /**
     * La función es usada para validar la agencia de viaje en el paso
     * 2 de registro.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Object $aavv
     * @return Array
     */
    private function validateStep2($aavv)
    {
        $resp = [];
        if ($aavv->getCreditInitial() === null)
            $resp[] = "credit";

        if (!$aavv->getBankDeposit()->toArray())
            $resp[] = "bankId";
        else {
            $payment = $aavv->getBankDeposit()->toArray();
            if (!$payment[0]->getNumberReference())
                $resp[] = "number";
            if (!$payment[0]->getBankType())
                $resp[] = "bankId";
            if (is_null($payment[0]->getType()))
                $resp[] = "paymentType";
        }

        return $resp;
    }

    private function validateStep3($aavv)
    {
        $addresses = $aavv->getAavvAddress();
        $response = [];
        $billingAddress = null;

        foreach ($addresses as $address) {
            if ($address->getTypeAddress() == 2)
                $billingAddress = $address;
        }

        //Si existe la direccion de facturacion verificamos que este bien
        if (!is_null($billingAddress)) {
            try{
                $phone = new Phone($billingAddress->getPhone());
            } catch(\Exception $e) {
                $response[] = 'bad_phone';
            }

            try {
                $email = new EmailAddress($billingAddress->getEmail());
            } catch(\Exception $e) {
                $response[] = 'bad_address';
            }

            $location = $billingAddress->getLocation();
            $position = 1;
            if (is_null($location))
                $response[] = "missing_location";
            else if (($location->getLvl() == 0) AND ($location->getId() == 1)) {
                $response[] = "missing_location";
            }
            else {    // Recorremos el location si no se encontra la parroquia faltan datos
                do {
                    $location = $location->getParent();
                    if (!is_null($location))
                        $position++;
                } while ((!is_null($location)) AND ($location->getLvl() > 0));
                if ($position < 4)
                    $response[] = "missing_location";
            }

            if ((is_null($billingAddress->getAddress())) OR (strcmp(gettype($billingAddress->getAddress()),"string") != 0))
                $response[] = "missing_address";
            if ((is_null($billingAddress->getBankAccount())) OR (strcmp(gettype($billingAddress->getBankAccount()),"string") != 0))
                $response[] = "missing_bank_account";
            /*if (is_null($billingAddress->getSwift()) OR (strcmp(gettype($billingAddress->getSwift()),"string") != 0))
                $response[] = "missing_swift";*/    
            if  ((is_null($billingAddress->getZipCode())) OR (strcmp(gettype($billingAddress->getZipCode()),"string") != 0))
                $response[] = "missing_zip_code";
        }
        else {//si no existe nada avisamos que falta rellenar todos los campos
            $response[] = "missing_address";
            $response[] = "missing_bank_account";
            $response[] = "missing_email_address";
            $response[] = "missing_phone";
            $response[] = "missing_swift";
            $response[] = "missing_zip_code";
        }
        return $response;
    }

    private function validateStep4($aavv)
    {
        $response = [];
        $agreement = $aavv->getAgreement();
        if(is_null($agreement))
            $response[] = 'terms_not_accepted';

        if ($aavv->getdocumentByType('CTR') == null)
            $response[] = 'empty_contract';

        return $response;
    }
}
