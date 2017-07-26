<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AddPaymentInfo;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\ValueObject\BankAccount;
use Navicu\Core\Domain\Model\ValueObject\Slug;
use Navicu\Core\Domain\Model\Entity\Document;

class AddPaymentInfoHandler implements Handler
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
        $location_rep = $rf->get('Location');
        $currency_rep = $rf->get('CurrencyType');
        $code = 201;
        $errors = array();
        $global_errors = array();

            //Busco el usuario
            $tempowner = $tempowner_repository->findOneByArray(
                array('slug'=>$request['slug'])
            );
            //si existe
            if (!empty($tempowner)) {
                if(
                    empty($request['account_number_part1']) ||
                    empty($request['account_number_part2']) ||
                    empty($request['account_number_part3']) ||
                    empty($request['account_number_part4'])
                ) {
                    $global_errors[]='Debes completar los campos de tu cuenta bancaria';
                } else {
                    try {
                        $account = new BankAccount(
                            $request['account_number_part1'],
                            $request['account_number_part2'],
                            $request['account_number_part3'],
                            $request['account_number_part4']
                        );
                        $response['account'] = $account->toArray();
                    } catch(\Exception $e) {
                        $errors[]='Hubo un problema con el número de cuenta, verifica que este correcto o comunicate con nosotros';
                        $code = 400;
                    }
                }

                if ( !empty($request['charging_system']) ) {
                    if ( is_string($request['charging_system']) ) {
                        if ($request['charging_system']=='Traferencia bancaria') {
                            $response['charging_system'] = 1;
                        }
                        /*if($request['charging_system']=='Tarjeta de crédito'){
                            $response['charging_system'] = 2;
                        }*/
                    } else {
                        if (
                            $request['charging_system']==1/*  ||
                            $request['charging_system']==2*/
                        ){
                            $response['charging_system'] = $request['charging_system'];
                        } else {
                            $errors[]='Hubo un problema con el sistema de pago, verifica que este correcto o comunicate con nosotros';
                            $code = 400;
                        }
                    }
                } else {
                    $global_errors[]='Debes seleccionar un tipo de pago';
                }

                if (
                    !empty($request['currency_id']) && 
                    is_integer($request['currency_id'])
                ) {
                    $currency = $currency_rep->find($request['currency_id']);
                    if ( !empty($currency) ) {
                        $response['currency_id'] = $request['currency_id'];
                    } else {
                        $errors[]='Hubo un problema con el campo "Moneda", verifica que este correcto o comunicate con nosotros';
                        $code = 400;
                    }
                } else {
                    $global_errors[]='Debes seleccionar un tipo de moneda';
                }

                if ( !empty($request['tax_id']) ) {
                    $response['tax_id'] = $request['tax_id'];
                } else {
                    $global_errors[]='Debes indicar el código fiscal de su establecimiento';
                }

                $response['same_data_property'] = $request['same_data_property'];
                if ( !$request['same_data_property'] ) {

                    if (!empty($request['name'])) {
                        $response['name'] = $request['name'];
                    } else {
                        $global_errors[]='Debes incluir el nombre de tu establecimiento';
                    }

                    if (!empty($request['address'])) {
                        $response['address'] = $request['address'];
                    } else {
                        $global_errors[]='Debes incluir una direccion de facturación';
                    }

                    if (
                        !empty($request['location']) && 
                        is_integer($request['location'])
                    ) {
                        $location = $location_rep->find($request['location']);
                        if ( !empty($location) ) {
                            $response['location'] = $request['location'];
                        } else {
                            $errors[]='Hubo un problema con el Pais, estado y/o ciudad, verifica que esten correctos o comunicate con nosotros';
                            $code = 400;
                        }
                    } else {
                        $global_errors[]='Debes seleccionar pais, estado y ciudad';
                    }
                }

                if(!empty($request['swift'])) {
                    if(is_string($request['swift'])) {
                        if (strlen($request['swift'])>=4 && strlen($request['swift'])<=11) {
                            $response['swift'] = $request['swift'];
                        } else {
                            $errors[]='Hubo un problema con el campo "SWIFT", verifica que este correcto o comunicate con nosotros';
                            $code = 400;
                        }
                    } else {
                        $errors[]='Hubo un problema con el campo "SWIFT", verifica que este correcto o comunicate con nosotros';
                        $code = 400;
                    }
                }

                if(!empty($request['rif'])) {
                    //Se obtiene los datos del establecimiento
                    $registerProperty = $tempowner->getPropertyForm();
                    $paymentForm = $tempowner->getPaymentInfoForm();

                    $repositoryDocument = $rf->get('Document');

                    //Se crea el nombre el archivo a almecenar
                    $nameFile =
                        'navicu-reserva-'.
                        Slug::generateSlug(trim($registerProperty['name'],' ')).'-';

                    $folder1 = Slug::generateSlug($request['slug']);

                    //Se crea el nombre de la ruta del archivo
                    $path = $folder1.'/';

                    $encoded = preg_replace('#^data:application/\w+;base64,#i', '', $request['rif']);

                    $decodedFile = base64_decode($encoded);

                    if(isset($paymentForm['rif']))
                    {
                        try {
                            $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/documents/' . $paymentForm['rif'];
                            unlink($file);


                        } catch(\Exception $e) {
                            return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                        }
                    }


                    //Se crea el documento
                    $document = new Document();
                    $document->setFile($decodedFile);
                    $document->setName($request['rifName']);
                    $document->setFileName($path.'/'.$nameFile);
                    $document->upload('document', $path, $nameFile);

                    /*$this->setDataFormImages(
                        $registerImagesBD,
                        $document,
                        $command->getIdSubGallery(),
                        $command->getGallery(),
                        $command->getSubGallery());*/

                    $response['rif'] = $document->getFileName();
                    $response['rifName'] = $document->getName();

                    //Almacenando el nombre del path de la imagen
                    /*$command->setPathImage($document->getFileName());

                    $responseArray = array();
                    $responseArray['name'] = $document->getName();
                    $responseArray['path'] = $document->getFileName();*/
                } else {
                    $paymentForm = $tempowner->getPaymentInfoForm();
                    if (isset($paymentForm['rif'])) {
                        $response['rif'] = $paymentForm['rif'];
                        $response['rifName'] = $paymentForm['rifName'];
                    }
                }

                $validations = $tempowner->getValidations();
                if(empty($global_errors))
                {
                    //si el usuario estaba en una seccion anterior y terminó la actual se actualiza su estado
                    if($tempowner->getLastsec()<5){
                        $tempowner->setLastsec(5);
                    }
                    $validations['paymentInfo'] = 'OK';
                    //índico que el usuario completo el formulario de informacion de pago
                    $tempowner->setProgress(4,1);
                } else {
                    $tempowner->setProgress(4,0);
                    $validations['paymentInfo'] = $global_errors;
                }
                $tempowner->setValidations($validations);
                $tempowner->setPaymentInfoForm($response);
                $response = new ResponseCommandBus(201,'OK');
                $tempowner_repository->save($tempowner);

                $response = new ResponseCommandBus($code,($code==201 ? 'OK' : 'Bad request'),$errors);
             } else {
                $response = new ResponseCommandBus(401,'Unauthorized');
            }

        return $response;
    }
}