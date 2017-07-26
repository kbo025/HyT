<?php
namespace Navicu\Core\Application\UseCases\Admin\GetAffiliatedProperties;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * La siguiente handler se encarga de retornar los datos
 * de los establecimientos afiliados
 *
 * Class GetAffiliatedPropertiesHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 16/10/2015
 */
class GetAffiliatedPropertiesHandler implements Handler
{
    /**
     *   instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 16/10/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $request = $command->getRequest();
        $rpProperty = $rf->get('Property');
        $arrayCommercial = [];
        $code = 400;
        $message = 'Something went wrong';
        $searchVector = "search_vector";
        try {
            if (CoreSession::havePermissons('admin_affiliates','read') and CoreSession::isRole('ROLE_SALES_EXEC')) {
                $request['search'] = $request['search']." ".$request['user']->getNvcProfile()->getFullName();
                $arrayOfProperties = $rpProperty->affiliatePropertyByFilter($request, $searchVector);
                $data = $arrayOfProperties['data'];
            }
            else {
                $arrayOfProperties = $rpProperty->affiliatePropertyByFilter($request, $searchVector);
                $data = $arrayOfProperties['data'];
            }
            if (CoreSession::isRole('ROLE_ADMIN')
                or CoreSession::havePermissons('admin_affiliates','assign_responsible'))
                $arrayCommercial = $this->getCommercials();
            else
                $arrayCommercial = [];
            if (count($data) > 0) {
                $code = 200;
                $message = 'ok';
            }
            $response['pagination'] = $arrayOfProperties['pagination'];
            $response['properties'] = $data;
            $response['commercials'] = $arrayCommercial;
        } catch (\Exception $e){
            die(var_dump($e->getMessage()));
        }
        return new ResponseCommandBus($code, $message, $response);
    }

    /**
     * Funcion encargada de listar los vendedores existentes
     *
     * @return array
     */
    public function getCommercials()
    {
        $rpCommercialProfile =  $this->rf->get('NvcProfile');
        return  $rpCommercialProfile->getComercialsList();
    }
}