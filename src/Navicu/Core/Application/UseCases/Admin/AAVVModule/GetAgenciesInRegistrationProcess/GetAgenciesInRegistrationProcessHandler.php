<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\GetAgenciesInRegistrationProcess;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Adapter\CoreSession;

class GetAgenciesInRegistrationProcessHandler implements Handler
{

    /**
     * instancia del RepositoryFactory
     */
    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;

        $rp = $rf->get('AAVV');

        $filters = $command->getRequest();

        $aavvs = $rp->findAllInRegistrationProcess($filters);

        $data = $this->getDataAAVV($aavvs);

        if ( isset($filters['orderBy']) && ($filters['orderBy']=='city' || $filters['orderBy']=='percentComplete' ))
            usort($data,self::build_sorter($filters['orderBy'],$filters['order']));

        //$data = $this->getTestData();

        return new ResponseCommandBus(200,'OK',$data);
    }

    /**
     * arma el array de datos que requiere frontend
     *
     * @param $aavvs
     * @return array
     */
    private function getDataAAVV($aavvs)
    {
        $response = [];

        CoreSession::setFinishRegistrationAAVV();

        foreach ($aavvs as $aavv) {

            $address = $aavv->getAavvAddress();

            $city = null;
            $i = 0;
            while ( $i<count($address) && is_null($city) )  {
                if ($address[$i]->getTypeAddress()==0) {

                    $city = $address[$i]
                        ->getLocation()
                        ->getCityId();

                    if(empty($city)) {
                        $city = $address[$i]->getLocation();
                        if($city->getParent())
                            $city = $city->getParent()->getTitle();
                        else
                            $city = $city->getTitle();

                    } else {
                        $city = $city->getTitle();
                    }
                }
                $i++;
            }

            $validationResponse = ValidateRegistrationHandler::getValidations($aavv,$this->rf)->getData();

            $percent = 25 * ( 4 - count($validationResponse) );

            $response[] = [
                'beginDate' => $aavv->getRegistrationDate()->format('d-m-Y'),
                'idAgency' => $aavv->getPublicId(),
                'slug' => $aavv->getSlug(),
                'city' => $city,
                'nameAgency' => $aavv->getCommercialName(),
                'percentComplete' => $percent,
            ];
        }
        return $response;
    }

    private function getTestData()
    {
        $response = [];
        $amount = rand(1,100);
        $numran = rand(1,$amount);
        for ($i = 0; $i<$amount; $i++) {
            $response[] = [
                'beginDate' => date('d-m-Y'),
                'idAgency' => 'NAV'.rand(10000,99999),
                'city' => 'Valencia',
                'slug' => 'agencia-prueba-'.$numran,
                'nameAgency' => 'Agencia Prueba N°'.$numran,
                'percentComplete' => rand(0,100),
            ];
        }
        return $response;
    }

    public static function build_sorter($key,$order) {
        return function ($a, $b) use ($key,$order) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            if(strtolower($order)=='asc')
                return ($a[$key] < $b[$key]) ? -1 : 1;
            else
                return ($a[$key] > $b[$key]) ? -1 : 1;
        };
    }
}