<?php

namespace Navicu\Core\Application\UseCases\AAVV\Logs\GetAavvLogDetails;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;

class GetAavvLogDetailsHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $translator = new CoreTranslator();

        $request = $command->getRequest();

        $logrep = $rf->get('AAVVLog');

        $details = $logrep->getLogDetails(
            $request['date'],
            $request['user_id'],
            $translator->getTranslator('aavv.logs.modules.'.$request['module']),
            $request['aavv_id']);

        $translatedDetails = [];

        foreach ($details as $detail) {
            if ($this->isValidField($detail['field'])) {
                $currentDetail = [];
                $currentDetail['date'] = $detail['date'];
                $currentDetail['field'] = $translator->getTranslator('aavv.logs.fields.' . $detail['field']);
                $currentDetail['newvalue'] = $detail['newvalue'];
                $currentDetail['oldvalue'] = $detail['oldvalue'];
                $currentDetail['type'] = $detail['type'];

                $translatedDetails[] = $currentDetail;
            }
        }

        return new ResponseCommandBus(200,'ok',$translatedDetails);
    }

    private function isValidField($field)
    {
        $invalidField = [
           'type_address', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'status',
            'credit_initial', 'personalized_mail', 'status_agency', 'navicu_gain',
            'confirmationemailreceiver', 'cancellationemailreceiver', 'newsemailreceiver',
            'credit_available','sent_email_for_insufficient_credit', 'deactivate_reason',
            'fullname', 'position', 'last_activation', 'deleted_at'
        ];

        if (in_array($field, $invalidField))
            return false;
        else
            return true;
    }
}