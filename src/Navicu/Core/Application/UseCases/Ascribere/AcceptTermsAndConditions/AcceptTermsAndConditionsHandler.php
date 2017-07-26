<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AcceptTermsAndConditions;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class AcceptTermsAndConditionsHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas 
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        //obtengo la data del comando
        $request = $command->getRequest();
        //obtengo los repositorios de TempOwner, Category y Location del repositoryFactory
        $tempowner_repository = $rf->get('TempOwner');
        $errors = array();
        $global_errors = array();

        try{
            //Busco el usuario
            $tempowner = $tempowner_repository->findOneByArray(
                array('slug'=>$request['slug'])
            );
            //si existe
            if(!empty($tempowner)){
                $oldData = $tempowner->getTermsAndConditionsInfo();
                $terms = array();
                if ($request['is_admin']) {
                    /*if ($request['accepted'] != $oldData['accepted']) {
                        $errors[] = 'Acción no autorizada';
                        $terms['accepted'] = $oldData['accepted'];
                        $terms['discount_rate'] = $oldData['discount_rate'];
                    } else {
                        if ($request['discount_rate'] != $oldData['discount_rate'])
                            $terms['accepted'] = false;
                        else
                            $terms['accepted'] = $oldData['accepted'];
                        $terms['discount_rate'] = $request['discount_rate'];
                    }*/
                    $terms['discount_rate'] = $request['discount_rate'];
                    $terms['accepted'] = $request['accepted'];
                    $terms['credit_days'] = $request['credit_days'];
                } else {
                    if (isset($oldData['discount_rate'])) {
                        if ($request['discount_rate'] != $oldData['discount_rate']) {
                            $errors[] = 'Acción no autorizada';
                            $terms['accepted'] = $oldData['accepted'];
                            $terms['discount_rate'] = $oldData['discount_rate'];
                            $terms['credit_days'] = $oldData['credit_days'];
                        } else {
                            $terms['accepted'] = !empty($request['accepted']);
                            $terms['discount_rate'] = $request['discount_rate'];
                            $terms['credit_days'] = $request['credit_days'];
                        }
                    } else {
                        $terms['accepted'] = !empty($request['accepted']);
                        $terms['discount_rate'] = 0.3;
                        $terms['credit_days'] = 30;
                    }
                }
                if (isset($request['accepted']) && $request['accepted']) {
                    if (isset($request['client_ip'])) {
                        $terms['client_ip'] = $request['client_ip'];
                    } else {
                        $errors[] = 'Hubo un problema durante el registro, comunicate con nosotros para ayudarte a solucionarlo';
                    }
                    $terms['date'] = new \DateTime("now");
                } else {
                    $global_errors[] = 'Debes Aceptar los terminos y condiciones';
                }
                $tempowner->setTermsAndConditionsInfo($terms);
                if (empty($errors)) {
                    $validations = $tempowner->getValidations();
                    if (!empty($global_errors)) {
                        $validations['termsAndConditions'] = $global_errors;
                        $tempowner->setProgress(5,0);
                        //$tempowner->setProgress(6,0);
                        //$tempowner->setProgress(7,0);
                    } else {
                        $validations['termsAndConditions'] = 'OK';
                        //si el usuario estaba en una seccion anterior y terminó la actual se actualiza su estado
                        if ($tempowner->getLastsec()<6) {
                            $tempowner->setLastsec(6);
                        }
                        //índico que el usuario completo el formulario de informacion de pago
                        $tempowner->setProgress(5,1);
                    }
                    $tempowner->setValidations($validations);
                    $tempowner_repository->save($tempowner);
                    $response = new ResponseCommandBus(201,'OK');
                } else {
                    $response = new ResponseCommandBus(400,'Bad request',$errors);
                }
            } else {
                $response = new ResponseCommandBus(401,'Unauthorized');
            }
        } catch( \Exception $e) {
            $response = new ResponseCommandBus(
                500,
                "\n".$e->getMessage()."\n".$e->getFile()."\n".$e->getLine()
            );
        }
        return $response;
    }
}