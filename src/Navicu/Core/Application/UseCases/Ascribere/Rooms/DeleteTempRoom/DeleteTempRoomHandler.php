<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\DeleteTempRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class DeleteTempRoomHandler implements Handler
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

        $global_errors = array();

            $tempowner = $tempowner_repository->findOneByArray(
                array('slug'=>$request['slug'])
            );
            //si existe
            if(!empty($tempowner)){
                
                $dataImages = $tempowner->getGalleryForm();

                $flagDeleteGallery = $this->deleteGallery(
                    $dataImages, 
                    $tempowner->selectRoom($request['index'])['type']);
                
                if ($flagDeleteGallery) 
                    $tempowner->setGalleryForm($dataImages);                

                $tempowner->removeRoom( $request['index'] );
                
                if($tempowner->getPropertyForm('amount_room') < $tempowner->getAmountRoomsAdd()) {
                    $global_errors[] = 'Cantidad de habitaciones agregadas excedida';
                } else {
                    if($tempowner->getPropertyForm('amount_room') > $tempowner->getAmountRoomsAdd()) {
                        $global_errors[] = 'Cantidad de habitaciones agregadas incompleta';
                    }
                }

                $validations = $tempowner->getValidations();
                if (empty($global_errors)) {
                    $tempowner->setProgress(2,1);
                    $validations['rooms'] = 'OK';
                } else {
                    $validations['rooms'] = $global_errors;
                    $tempowner->setProgress(2,0);
                }

                //almaceno el usuario temporal
                $tempowner->setValidations($validations);
                $tempowner_repository->save( $tempowner );
                $response = new ResponseCommandBus(
                    201,
                    'OK',
                    $tempowner->getRoomsForm()
                );
            }else{
                $response = new ResponseCommandBus(401,'Unauthorized');
            }
            
        return $response;
    }


    /**
    * La siguiente función se encarga de eliminar la galería de imagenes 
    * de la habitación a eliminar
    *
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param Array $dataImages
    * @param integer $idRoom
    * @return boolean
    */

    private function deleteGallery(&$dataImages, $idRoom)
    {
        //variable que identifica si se elimina la habitación
        $flagDelete = false;

        //Si existen habitaciones cargadas
        if (!empty($dataImages) and isset($dataImages['rooms'])) {
            
            foreach ($dataImages['rooms'] as $key => $currentGalleryRoom) {

                //si la galería actual concuerda con la habitacion a eliminar
                if ($currentGalleryRoom['idSubGallery'] == $idRoom) {

                    foreach ($currentGalleryRoom['images'] as $currentImage) {
                        
                        //Eliminando la imagen de tamaño original
                        $fileOriginal = $_SERVER['DOCUMENT_ROOT'].'/images/images_original/'.$currentImage['path']; 

                        if (file_exists($fileOriginal))
                            unlink($fileOriginal);

                        //Eliminando la imagen de tamaño XS
                        $fileXS = $_SERVER['DOCUMENT_ROOT'].'/images/images_xs/'.$currentImage['path'];

                        if (file_exists($fileXS))
                            unlink($fileXS);

                        //Eliminando la imagen de tamaño SM
                        $fileSM = $_SERVER['DOCUMENT_ROOT'].'/images/images_sm/'.$currentImage['path'];

                        if (file_exists($fileSM))
                            unlink($fileSM);
                        
                        //Eliminando la imagen de tamañoMD
                        $fileMD = $_SERVER['DOCUMENT_ROOT'].'/images/images_md/'.$currentImage['path'];

                        if (file_exists($fileMD))
                            unlink($fileMD);
                    }

                    unset($dataImages['rooms'][$key]);
                    $flagDelete = true;

                    break;
                }
            }
        }

        return $flagDelete;
    }
}