<?php
namespace Navicu\Core\Application\UseCases\Admin\GetTempProperties;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;


/**
 * Class Admin\GetTempPropertiesHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class GetTempPropertiesHandler implements Handler
{
    /**
     * Instancia del repositoryFactory
     * @var RepositoryFactory $rf
     */
    protected $rf;

    /**
    * instancia del comando
    */
    protected $command; 

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $param = $command->getRequest();
        $arrayCommercial = [];
        $code = 400;
        $message = 'Something went wrong';
        $searchVector = "search_vector";
        $rpTempOwner = $rf->get('TempOwner');

        if (CoreSession::havePermissons('admin_temporals','read') and CoreSession::isRole('ROLE_SALES_EXEC')) {
            $param['search'] = $param['user']->getNvcProfile()->getFullName();
            $arrayOfTempProperties = $rpTempOwner->tempPropertyByFilter($param, $searchVector);
            $data = $arrayOfTempProperties['data'];
        }
        else {
            $arrayOfTempProperties = $rpTempOwner->tempPropertyByFilter($param, $searchVector);
            $data = $arrayOfTempProperties['data'];
        }
//        else {
//            $nvcProfile = CoreSession::getUser()->getNvcProfile();
//            if ($nvcProfile->havePermissons('temp_properties')) {
//                $arrayOfTempProperties = $rpTempOwner->tempPropertyByFilter($param, $searchVector);
//                $data = $arrayOfTempProperties['data'];
//            } else
//                $arrayOfTempProperties = [];
//        }

        $this->buildCommercialStructure($data);
        if (CoreSession::havePermissons('admin_temporals','assign_responsible'))
            $arrayCommercial = $this->getCommercials($rf);
        if (count($data) > 0) {
            $code = 200;
            $message = 'ok';
        }
        $response['pagination'] = $arrayOfTempProperties['pagination'];
        $response['temp_properties'] = $data;
        $response['commercials'] = $arrayCommercial;

        return new ResponseCommandBus($code, $message, $response);
    }

    /**
     * Consulta los comerciales
     *
     * @param $rf
     * @return array
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/04/2016
     */
    public function getCommercials($rf)
    {
        $rpCommercialProfile =  $rf->get('NvcProfile');
        return  $rpCommercialProfile->getComercialsList();
    }

    /**
     * Funcion para generar una estructura necesaria para fronEnd de los commerciales
     *
     * @param $data array, respuesta de la base de datos
     * @version 2017-01-26
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    private function buildCommercialStructure(&$data)
    {
        $length = count($data);
        for ($ii = 0; $ii < $length; $ii++) {
            $obj['name'] = $data[$ii]['nvc_profile_name'];
            $obj['id'] = $data[$ii]['nvc_profile_id'];
            $data[$ii]['commercial'] = $obj;
            $obj = [];
        }
    }
}