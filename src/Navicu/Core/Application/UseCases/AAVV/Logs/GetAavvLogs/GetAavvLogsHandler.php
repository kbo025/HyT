<?php

namespace Navicu\Core\Application\UseCases\AAVV\Logs\GetAavvLogs;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;

class GetAavvLogsHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $translator = new CoreTranslator();

        $logrep = $rf->get('AAVVLog');

        $userrep = $rf->get('User');

        $user = CoreSession::getUser();

        $profile = $user->getAavvProfile();

        $aavv = $profile->getAavv();

        if ($aavv) {

            $logs = $logrep->getLogHeaders($aavv->getId());

            $tranlatedLogs = array();

            foreach ($logs as $log){
                $currentlog = [];
                $currentlog['date'] = $log['date'];
                $currentlog['user_id'] = $log['user_id'];
                $currentlog['user'] = $log['user'];
                $currentlog['module'] = $translator->getTranslator('aavv.logs.modules.'.$log['module']);

                $tranlatedLogs[] = $currentlog;
            }

            $response = array();

            $response['aavv_id'] = $aavv->getId();
            $response['aavv'] = $aavv->getCommercialName();
            $response['logs'] = $tranlatedLogs;


            return new ResponseCommandBus(200,'ok',$response);
        } else {
            return new ResponseCommandBus(400,'');
        }
    }
}